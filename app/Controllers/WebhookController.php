<?php

namespace App\Controllers;

use App\Http\Controllers\Controller as Controller;
use App\Services\CompanyService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ResponseAPI;
use App\Services\SmsMessageOutService;

class WebhookController extends Controller
{
    use ResponseAPI;
    public $successStatus = 200;
    protected $company_service;

    public function __construct()
    {
        $this->company_service = new CompanyService;
        $this->webhook_service = new WebhookService;
        $this->sms_messageout_service = new SmsMessageOutService;
    }

    public function getWebhook(Request $request){
        $webhookCalls = $this->webhook_service->getWebhookCall();
        if(count($webhookCalls) > 0 && $webhookCalls != null){
            $company = $this->company_service->findByUuid($request->companyId, Auth::user()->id);
            if (!isset($company->id)) {
                return $this->error("Company Not Found", 404);
            }
            $get_messages =  $this->sms_messageout_service->getMessageOut($company->id);
            $data =[];
            if($get_messages['status'] == true)
            {
                foreach($webhookCalls as $index=>$webhookCall){
                    $convert_payload = json_decode($webhookCall->payload);
                    foreach($get_messages['Message'] as $get_message){
                        if(isset($convert_payload->message_send->id) && $get_message->bandwidth_referrence_id == $convert_payload->message_send->id){
                            $data[$index] =  (array)$convert_payload->message_send;
                        }
                    }
                }
                return $this->success($data);
            }
            else
            {
                return $this->error('Data not found',404);
            }
        }
        else{
            return $this->error('Data not found',404);
        }
    }

    public function deleteWebhook($id){
        $webhookCall = $this->webhook_service->deleteWebhookCall($id);
        return $this->success($webhookCall);
    }
    

}
