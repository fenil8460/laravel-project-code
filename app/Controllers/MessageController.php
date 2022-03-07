<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use App\Services\MessageService;
use App\Services\SmsMessageOutService;
use App\Services\SmsMessageInService;
use App\Services\MessageTemplateService;
use App\Services\PhoneNumberService;
use App\Http\Controllers\Controller as Controller;
use App\Models\Company;
use App\Services\CompanyService;
use App\Traits\ResponseAPI;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Events\CompanyActivity;
use App\Notifications\saveNotification;
use App\Traits\PusherTrait;
use Spatie\WebhookServer\WebhookCall;

use Illuminate\Validation\Rules\Exists;

class MessageController extends Controller
{
    protected $message_service, $sms_messageout_service, $sms_messagein_service, $phone_number_service, $company_service;
    use ResponseAPI,PusherTrait;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
        $this->message_service = new MessageService;
        $this->sms_messageout_service = new SmsMessageOutService;
        $this->sms_messagein_service = new SmsMessageInService;
        $this->phone_number_service = new PhoneNumberService;
        $this->company_service = new CompanyService;
        $this->message_template_service = new MessageTemplateService;
    }

    public function sendSMS(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if (!isset($company->id)) {
            return $this->error("Company Not Found", 404);
        }
        $validator = Validator::make($request->all(), [
            'to' => 'required|array|min:1',
            'from' => 'required',
            'message' => 'required',
            'companyId' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), '404');
        }

        $to = $request->to;
        $from = $request->from;
        $message = $request->message;
        $tag = $request->tag;

        // $company_ids = Auth::user()->company->pluck('id');
        $phone_number = $this->phone_number_service->findByuuid($from);
        if ($phone_number && $phone_number->company_id == $company->id) {
            if ($phone_number->status == "ACTIVE" && $phone_number->running_state == 1) {
                try {
                    if($company->balance < count($to))
                    {
                        return $this->error("No enough amount in wallet to continue purchase",'500');
                    }
                    $response = $this->message_service->sendSMS($to, $phone_number->id, $message, $tag);

                    if (count($response->to) > 1) {
                        $is_group = 1;
                    } else {
                        $is_group = 0;
                    }
                    // dd($response->id);
                    $data = [];

                    $data = [
                        'uu_id' => (string)Str::uuid(),
                        'bandwidth_referrence_id' => $response->id,
                        'created_by_id' => $this->user->id,
                        'company_id' => $phone_number->company_id,
                        'phone_number_id' => $phone_number->id,
                        'to_number' => collect($response->to),
                        'message' => $response->text,
                        'is_group' => $is_group,
                        'status' => 1,

                    ];

                    // $success = [
                    //     'created_by_id' => Auth::user()->id,
                    //     'company_id' => $phone_number->company_id,
                    //     'phone_number_id' => $phone_number->id,
                    //     'to_number' => collect($response->to),
                    //     'from_number' => $response->from,
                    //     'message' => $response->text,
                    //     'is_group' => $is_group,
                    //     'status' => 1,
                    // ];
                    $data =  $this->sms_messageout_service->store($data);
                    $company_activities = $data;
                    $company_activities['type'] = 'message';
                    event(new CompanyActivity($company_activities));
                    $notify_text = "Message Sent from $phone_number->phone_number to ".collect($response->to);
                    $this->sendNotification($notify_text);
                    $company->notify(new saveNotification($notify_text));
                    WebhookCall::create()
                    ->url('http://127.0.0.1:8000/api/webhook-receiving-url')
                    ->payload(['message_send' => $response])
                    ->useSecret('N16IPCgd0pFsvMpqEIef1sSpMhQFAJaOwknx92XIJadBqoSxrrpHAm5pvU20')
                    ->dispatchSync();
                    return $this->success($response, 202);
                } catch (\Exception $e) {
                    return $this->error($e->getMessage(), '404');
                }
            } else {
                return $this->error('This Number is not purchased', '404');
            }
        } else {
            return $this->error('This Number is not purchased', '404');
        }
    }




    public function sendSMSByMessageTemplate(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if (!isset($company)) {
            return $this->error("Company Not Found", '404');
        }

        $validator = Validator::make($request->all(), [
            'to' => 'required|array|min:1',
            'from' => 'required',
            'companyId' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), '404');
        }

        $to = $request->to;
        $from = $request->from;
        $tag = $request->tag;
        $message = '';

        $phone_number = $this->phone_number_service->find($from);
        if ($phone_number && $phone_number->company_id == $company->id) {
            if ($phone_number->status == "ACTIVE" && $phone_number->running_state == 1) {
                $message_template = $this->message_template_service->getMessageTemplateCompany($phone_number->company_id);
                if ($message_template['status'] == true) {
                    $message = $message_template['Message'][0]->template_text;
                } else {
                    return $this->error("Message Template Not Found", '404');
                }
                try {
                    $response = $this->message_service->sendSMS($to, $from, $message, $tag);
                    if (count($response->to) > 1) {
                        $is_group = 1;
                    } else {
                        $is_group = 0;
                    }
                    $data = [];

                    $data = [
                        'uu_id' => (string)Str::uuid(),
                        'bandwidth_referrence_id' => $response->id,
                        'created_by_id' => $this->user->id,
                        'company_id' => $phone_number->company_id,
                        'phone_number_id' => $phone_number->id,
                        'to_number' => collect($response->to),
                        'message' => $response->text,
                        'is_group' => $is_group,
                        'status' => 1,

                    ];
                    $data =  $this->sms_messageout_service->store($data);
                    return $this->success($response, 202);
                } catch (\Exception $e) {
                    return $this->error($e->getMessage(), '404');
                }
            } else {
                return $this->error('This Number is not purchased', '404');
            }
        } else {
            return $this->error('This Number is not purchased', '404');
        }
    }

    public function getSentMessages(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if (!isset($company->id)) {
            return $this->error("Company Not Found", 404);
        }
        $data =  $this->sms_messageout_service->getMessageOut($company->id);
        $messages = new Collection();
        if ($data['status'] == true) {
            foreach ($data['Message'] as $message) {
                $phone =  $this->phone_number_service->find($message->phone_number_id);
                $messages->push([
                    'from' => $phone->phone_number,
                    'to' => $message->to_number,
                    'message' => $message->message,
                    'time' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
                    'company' => $company->name,
                ]);
            }
            return $this->success($messages);
        } else {
            return $this->error("No Messages", '404');
        }
    }

    public function searchSentMessages(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if (!isset($company->id)) {
            return $this->error("Company Not Found", 404);
        }
        if (isset($request->message)) {
            $message = $request->message;
        } else {
            $message = "";
        }
        $message = $this->sms_messageout_service->show($company->id, $message);

        if ($message['status'] == true) {
            $data = new Collection();
            foreach ($message['Message'] as $message) {
                $phone =  $this->phone_number_service->find($message->phone_number_id);
                $data->push([
                    'from' => $phone->phone_number,
                    'to' => $message->to_number,
                    'message' => $message->message,
                    'time' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
                    'company' => $company->name,
                ]);
            }
            return $this->success($data);
        } else {
            return $this->error("No Messages", '404');
        }
    }

    public function searchReceivedMessages(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if (!isset($company->id)) {
            return $this->error("Company Not Found", 404);
        }
        $message = $request->message;
        $message = $this->sms_messagein_service->show($company->id, $message);

        if ($message['status'] == true) {
            $data = new Collection();
            foreach ($message['Message'] as $message) {
                $phone =  $this->phone_number_service->find($message->phone_number_id);
                $data->push([
                    'from' => $message->from_number,
                    'to' => $phone->phone_number,
                    'message' => $message->message,
                    'time' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
                    'company' => $company->name,
                ]);
            }
            return $this->success($data);
        } else {
            return $this->error("No Messages", '404');
        }
    }

    public function webhookSmsReceive(Request $request)
    {
        $data = $request->all();
        if ($data[0]['message']['direction'] == "in" && $data[0]['type'] == "message-received") {
            $real_number = substr($data[0]['to'], 2);
            $phone_number = $this->phone_number_service->findNumber($real_number);
            if ($phone_number && $phone_number->status == "ACTIVE" && $phone_number->running_state == 1) {
                $phone_number_id = $phone_number->id;
            } else {
                return $this->error('The recipient Number is not Owned by Company', '404');
            }
            $input = [];
            $input = [
                'uu_id' => (string)Str::uuid(),
                'created_by_id' => $phone_number->company->id,
                'company_id' => $phone_number->company->id,
                'phone_number_id' => $phone_number_id,
                'received_time' => $data[0]['message']['time'],
                'type' => $data[0]['type'],
                'status' => 1,
                'from_number' => $data[0]['message']['from'],
                'direction' => $data[0]['message']['direction'],
                'message' => $data[0]['message']['text'],

            ];
            try {

                $sms_received = $this->sms_messagein_service->store($input);
                $company_activities = $input;
                $company_activities['type'] = 'messageReceive';
                $company_activities['id'] = isset($data[0]['message']['id']) ? $data[0]['message']['id'] : $data[0]['message']['text'];
                event(new CompanyActivity($company_activities));
                $notify_text = "Message Received from $data[0]['message']['from'] to $phone_number->phone_number";
                $this->sendNotification($notify_text);
                $phone_number->company->notify(new saveNotification($notify_text));
                WebhookCall::create()
                ->url('http://127.0.0.1:8000/api/webhook-receiving-url')
                ->payload(['message_received' => $sms_received])
                ->useSecret('N16IPCgd0pFsvMpqEIef1sSpMhQFAJaOwknx92XIJadBqoSxrrpHAm5pvU20')
                ->dispatchSync();
                return $this->success($sms_received);
            } catch (Exception $e) {
                return $this->error($e->getMessage());
            }
        } else {
            return $this->error("There is no received Messages");
        }
    }

    public function getReceivedMessages(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if (!isset($company->id)) {
            return $this->error("Company Not Found", 404);
        }
        $data =  $this->sms_messagein_service->getData($company->id);
        $messages = new Collection();
        if ($data['status'] == true) {
            foreach ($data['Message'] as $message) {
                $phone =  $this->phone_number_service->find($message->phone_number_id);
                $messages->push([
                    'from' => $message->from_number,
                    'to' => $phone->phone_number,
                    'message' => $message->message,
                    'time' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
                    'company' => $company->name,
                ]);
            }
            return $this->success($messages);
        } else {
            return $this->error("No Messages", '404');
        }
    }
    private function payForMessageSending($message,$company)
    {
        $company->pay($message);
    }
}
