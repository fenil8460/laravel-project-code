<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Services\PhoneNumberService;
use App\Services\OrderItemService;
use App\Http\Controllers\Controller as Controller;
use App\Traits\ResponseAPI;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Models\Company;
use App\Models\OrderItem;
use App\Models\PhoneNumber;
use App\Events\CompanyActivity;
use App\Services\CompanyService;
use App\Notifications\saveNotification;
use App\Traits\PusherTrait;
use Spatie\WebhookServer\WebhookCall;
use Carbon\Carbon;
use App\Services\MessageService;
use App\Services\SmsMessageOutService;
use App\Services\SmsMessageInService;
use App\Services\MessageTemplateService;

class DeveloperApiController extends Controller
{
    protected $order_service,$phone_number_service,$order_item_service;
    use ResponseAPI,PusherTrait;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user= Auth::user();
            return $next($request);
        });
        $this->order_service = new OrderService;
        $this->phone_number_service = new PhoneNumberService;
        $this->order_item_service = new OrderItemService;
        $this->company_service = new CompanyService;
        $this->message_service = new MessageService;
        $this->sms_messageout_service = new SmsMessageOutService;
        $this->sms_messagein_service = new SmsMessageInService;
    }

    public function searchAvailableNumbers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'areaCode' => 'required',
        ]);
        if($validator->fails()){
            return $this->error($validator->errors(),'404');
        }
        $input = $request->all();
        $data = $this->order_service->searchAvailableNumbers($input);
        if(!empty($data['numbers']))
        {
            return $this->success($data);
        }
        else
        {
            return $this->error($data['error'],'404');
        }
    } 

    private function payForPhoneNumberPurchase($new_phone,$company)
    {
        $company->pay($new_phone);
    }

    public function getAllInserviceNumbers(Request $request,$company_id = null)
    {
        
        $companies = $this->company_service->getAllCompanies();
      
        if($companies != NULL)
        {
            try
            {
                $phone_numbers =  $this->order_service->getInserviceNumbers();
                $data = [];
                $index = 0;
                foreach($companies as $company){
                    $numbers = $this->phone_number_service->getInserviceNumbers($company->id);
                    $finalLists = $numbers->whereIn('phone_number',$phone_numbers)->get();
                    if(count($finalLists) > 0)
                    {
                        foreach($finalLists as $finalList){
                            $user = $this->company_service->getCompanyUser($finalList->created_by_id);
                            $data[$index] = [
                                "uu_id"=>$finalList->uu_id,
                                "created_by_id"=>isset($user->uu_id) ? $user->uu_id : null,
                                "company_id"=>$company->uu_id,
                                "phone_number"=>$finalList->phone_number,
                                "nick_name"=>$finalList->nick_name,
                                "status"=>$finalList->status,
                                "running_state"=>$finalList->running_state,
                                "deleted_at"=>$finalList->deleted_at,
                                "created_at"=>$finalList->created_at,
                                "updated_at"=>$finalList->updated_at,
                                "company_name"=>$company->name,
                            ];
                            $index++;
                        }
                    }
                }
                    return $this->success($data);
            }
            catch(Exception $e)
            {
                return $e->getMessage();
            }
        }
    }

    public function getDisconnectedNumbersByDeveloper(Request $request)
    {
        $companies = $this->company_service->getAllCompanies();
        if($companies != NULL)
        {
            try
            {
                $phone_numbers =  $this->order_service->getAllDisconnectedNumbers();
                $data = [];
                $index = 0;
                foreach($companies as $company){
                    $numbers = $this->phone_number_service->getDisconnectedNumbers($phone_numbers,$company->id);
                        if(count($numbers) != 0)
                        {
                            foreach($numbers as $number){
                                $user = $this->company_service->getCompanyUser($number->created_by_id);
                                $data[$index]=[
                                    "uu_id"=> $number->uu_id,
                                    "created_by_id"=> isset($user->uu_id) ? $user->uu_id : null,
                                    "company_id"=> isset($company->uu_id) ? $company->uu_id : null,
                                    "phone_number"=> $number->phone_number,
                                    "nick_name"=> $number->nick_name,
                                    "status"=> $number->status,
                                    "running_state"=> $number->running_state,
                                    "deleted_at"=> $number->deleted_at,
                                    "created_at"=> $number->created_at,
                                    "updated_at"=> $number->updated_at,
                                    "company_name"=> isset($company->name) ? $company->name : null,
                                ];
                                $index++;
                            }
                        }
                    }
                    return $this->success($data);
            }
            catch(Exception $e)
            {
                return $e->getMessage();
            }
        }
    }

    public function findDisconnectedNumbersByDeveloper(Request $request,$phone_numbers)
    {
        try
        { 
            $data = [];
            $numbers = $this->phone_number_service->findDisconnectedNumber($phone_numbers);
            $user = $this->company_service->getCompanyUser($numbers->created_by_id);
            $company = $this->company_service->findByAdmin($numbers->company_id);
            $data = [
                "uu_id" => isset($numbers->uu_id) ? $numbers->uu_id :null,
                "created_by_id" => isset($user->uu_id) ?$user->uu_id :null,
                "company_id" => isset($company->uu_id) ? $company->uu_id :null,
                "phone_number" => isset($numbers->phone_number) ? $numbers->phone_number :null,
                "nick_name" => isset($numbers->nick_name) ? $numbers->nick_name :null,
                "status" => isset($numbers->status) ? $numbers->status :null,
                "running_state" => isset($numbers->running_state) ? $numbers->running_state :null,
                "deleted_at" => isset($numbers->deleted_at) ? $numbers->deleted_at :null,
                "created_at" => isset($numbers->created_at) ? $numbers->created_at :null,
                "updated_at" => isset($numbers->updated_at) ? $numbers->updated_at :null,
            ];
            return $this->success($data);
        }
        catch(Exception $e)
        {
            return $e->getMessage();
        }
    }

    public function getOrdersByDeveloper(Request $request)
    {
        $companies = $this->company_service->getAllCompanies();
        $data = new Collection;
        try{
            foreach($companies as $company){
                $orders = $this->order_service->getAllOrders($company->id);
                if($orders->count() > 0)
            {
                foreach($orders as $order)
                {
                    $order_item = OrderItem::where('order_id',$order->id)->get();
                    $data->push([
                        "company_name"=>$company->name,
                        "company_id"=>$company->uu_id,
                        "order_name"=> $order->order_name,
                        "order_status"=>$order->order_status,
                        "order_type" => $order->order_type,
                        "ordered_numbers" => $order_item->pluck('phone_number'),
                    ]);
                }
            }
            }
            return $this->success($data);
        }
        catch(Exception $e)
            {
                return $e->getMessage();
            }
    }

    public function getInserviceNumbers(Request $request,$company_id = null)
    {
        $company = Company::where('uu_id',$company_id)->first();
        if(!isset($company->id))
        {
            return $this->error("Company Not Found",404);
        }
        if($company != NULL)
        {
            try
            {
                $phone_numbers =  $this->order_service->getInserviceNumbers();
                $numbers = $this->phone_number_service->getInserviceNumbers($company->id);
                $finalLists = $numbers->whereIn('phone_number',$phone_numbers)->get();
                $data = [];
                if(isset($finalLists))
                {
                    foreach($finalLists as $index=>$finalList){
                        $user = $this->company_service->getCompanyUser($finalList->created_by_id);
                        $data[$index] = [
                            "uu_id"=>$finalList->uu_id,
                            "created_by_id"=>isset($user->uu_id) ? $user->uu_id : null,
                            "company_id"=>$company->uu_id,
                            "phone_number"=>$finalList->phone_number,
                            "nick_name"=>$finalList->nick_name,
                            "status"=>$finalList->status,
                            "running_state"=>$finalList->running_state,
                            "deleted_at"=>$finalList->deleted_at,
                            "created_at"=>$finalList->created_at,
                            "updated_at"=>$finalList->updated_at,
                            "company_name"=>$company->name,
                        ];
                    }
                    return $this->success($data);
                }
                else
                {
                        return $this->error("No Inservice Numbers",'500');
                }


            }
            catch(Exception $e)
            {
                return $e->getMessage();
            }
        }
    }

    public function disconnectNumberByDeveloper(Request $request)
    {
        $company = Company::where('uu_id',$request->companyId)->first();
        if(!isset($company->id))
        {
            return $this->error("Company Not Found",404);
        }

        $validator = Validator::make($request->all(), [
            'Name' => 'required',
            'TelephoneNumber' => 'required|array|min:1',
            'companyId' => 'required',
        ]);

        if($validator->fails()){
            return $this->error($validator->errors(),'404');
        }

        $input[] = $request->TelephoneNumber;
        try
        {
            $disconnect_numbers_list = new Collection();
            foreach($request->TelephoneNumber as $number)
            {

                $phone_number = $this->phone_number_service->disconnectNumber($number,$company->id);
                if($phone_number && $phone_number->status == "ACTIVE" )
                {
                    $disconnect_numbers_list->push($phone_number->phone_number);
                }
                else
                {
                    continue;
                }
            }
            if(count($disconnect_numbers_list) == 0)
            {
                $company_activities = $phone_number;
                $company_activities['type'] = 'disconnected';
                $company_activities['order_status'] = 'already disconnected';
                event(new CompanyActivity($company_activities));
                return $this->error("The requested numbers are already disconnected");
            }
            $uu_id = (string)Str::uuid();
            $response = $this->order_service->disconnectNumber($disconnect_numbers_list,$request->Name,$uu_id);

            if($response->OrderStatus->OrderStatus == "RECEIVED")
            {
                $disconnectOrderData = [];
                $disconnectOrderData = [
                    'order_name' => $request->Name,
                    'uu_id' => $uu_id,
                    'user_id' => $company->user_id,
                    'company_id' => $company->id,
                    'bandwidth_order_id' => $response->OrderId,
                    'order_status' => $response->OrderStatus->OrderStatus,
                    'order_type' => 'DISCONNECTION',

                ];
                $newOrder = $this->order_service->store($disconnectOrderData);

                foreach($disconnect_numbers_list as $number)
                {
                    $phone_number = $this->phone_number_service->disconnectNumber($number,$company->id);
                    if($phone_number)
                    {
                        $phone_number->running_state = NULL;
                        $phone_number->save();
                    }
                    $phoneData = [
                        'uu_id' => (string)Str::uuid(),
                        'created_by_id' => $company->user_id,
                        'company_id' => $company->id,
                        'phone_number' => $number,
                        'nick_name' => $phone_number->nick_name,
                        'status' => "DISCONNECTED",
                        'running_state' => 1,
                    ];
                    $this->phone_number_service->store($phoneData);
                    $company_activities = $phoneData;
                    $company_activities['type'] = 'disconnected';
                    $company_activities['order_status'] = 'disconnected';
                    event(new CompanyActivity($company_activities));
                }

                if(count($disconnect_numbers_list) == 1)
                {
                    $disconnect_order_items_data = [
                        'uu_id' => (string)Str::uuid(),
                        'order_id' => $newOrder->id,
                        'phone_number' =>  $response->OrderStatus->orderRequest->DisconnectTelephoneNumberOrderType['TelephoneNumberList']['TelephoneNumber'],
                        'order_status' => $response->OrderStatus->OrderStatus,

                    ];
                    $this->order_item_service->store($disconnect_order_items_data);

                }
                else{
                    $phone_numbers = $response->OrderStatus->orderRequest->DisconnectTelephoneNumberOrderType['TelephoneNumberList'];
                    // dd($phone_numbers);
                    foreach($phone_numbers as $phone_number)
                    {
                        $disconnect_order_items_data = [
                            'uu_id' => (string)Str::uuid(),
                            'order_id' => $newOrder->id,
                            'phone_number' =>  $phone_number,
                            'order_status' => $response->OrderStatus->OrderStatus,
                        ];
                        $this->order_item_service->store($disconnect_order_items_data);
                    }
                }
                return $this->success($response->OrderStatus);
            }
            else{
                return $this->error("Error","500");
            }
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function getCompany(){

        $companies = $this->company_service->getAllCompanies();
        $data = [];
        if(count($companies) > 0 && $companies != null){
            foreach($companies as $index=>$company){
                $user = $this->company_service->getCompanyUser($company->user_id);
                $data[$index] = [
                    "uu_id"=>$company->uu_id,
                    "user_id"=>isset($user->uu_id) ? $user->uu_id : null,
                    "name"=>$company->name,
                    "nick_name"=>$company->nick_name,
                    "created_at"=>$company->created_at,
                    "updated_at"=>$company->updated_at,
                    "deleted_at"=>$company->deleted_at,
                    "user"=>[
                        "uu_id" => isset($user->uu_id) ? $user->uu_id : null,
                        "name" => isset($user->name) ? $user->name : null,
                        "email" => isset($user->email) ? $user->email : null,
                        "email_verified_at" => isset($user->email_verified_at) ? $user->email_verified_at : null,
                        "api_token" => isset($user->api_token) ? $user->api_token : null,
                        "remember_token" => isset($user->remember_token) ? $user->remember_token : null,
                        "created_at" => isset($user->created_at) ? $user->created_at : null,
                        "updated_at" => isset($user->updated_at) ? $user->updated_at : null,
                        "provider" => isset($user->provider) ? $user->provider : null,
                        "provider_id" => isset($user->provider_id) ? $user->provider_id : null,
                        "provider_token" => isset($user->provider_token) ? $user->provider_token : null,
                        "provider_refresh_token" => isset($user->provider_refresh_token) ? $user->provider_refresh_token : null,
                        "active" => isset($user->active) ? $user->active : null,
                        "is_approved" => isset($user->is_approved) ? $user->is_approved : null,
                    ],
                ];
            }
            return $this->success($data);
        }
        else{
            return $this->error("Company Not Found",'404');
        }

    }

public function createOrder(Request $request,$number = null)
    {

        try{
            if($number != NULL)
            {
                $company = Company::where('uu_id',$request->companyId)->first();
                if(!isset($company->id))
                {
                    return $this->error("Company Not Found",404);
                }
                $validator = Validator::make($request->all(), [
                    'SiteId' => 'required',
                    'companyId' => 'required',
                ]);
                if($validator->fails()){
                    return $this->error($validator->errors(),'404');
                }
                $orderData = [];
                $uu_id = (string)Str::uuid();
                $orderData = [
                    'order_name' => "Reconnect ".$number,
                    'uu_id' => $uu_id,
                    'user_id' => $this->user->id,
                    'company_id' => $company->id,
                    'order_type' => 'RECONNECTION',
                ];
                $disconnected_number = $this->phone_number_service->findDisconnected($number,$company->id);
                // dd($disconnected_number);
                if($disconnected_number)
                {
                    if($disconnected_number->status == "DISCONNECTED" && $disconnected_number->running_state == "1")
                    {
                        $newOrder = $this->order_service->store($orderData);
                        $input[] = $number;
                        if($company->balance < count($input)*2)
                        {
                            return $this->error("No enough amount in wallet to continue purchase",'500');
                        }
                        $response = $this->order_service->createOrder($input,$newOrder->order_name,$request->SiteId,$newOrder->uu_id);
                        $company_activities = $disconnected_number;
                        $company_activities['type'] = 'reconnect';
                        $company_activities['order_status'] = 'success';
                        event(new CompanyActivity($company_activities));

                    }
                    else{
                        $company_activities = $disconnected_number;
                        $company_activities['type'] = 'reconnect';
                        $company_activities['order_status'] = 'failed';
                        event(new CompanyActivity($company_activities));
                        return $this->error('The Number is still in active state,may be owned by yourself or some other company');
                    }
                }
                else{
                    return $this->error('The Number is not owned by the company');
                }
            }
            else{
                $company = Company::where('uu_id',$request->companyId)->first();
                if(!isset($company->id))
                {
                    return $this->error("Company Not Found",404);
                }

                $validator = Validator::make($request->all(), [
                    'Name' => 'required',
                    'SiteId' => 'required',
                    'TelephoneNumber' => 'required|array|min:1',
                    'companyId' => 'required',
                ]);
                if($validator->fails()){
                    return $this->error($validator->errors(),'404');
                }

                $orderData = [];
                $uu_id = (string)Str::uuid();
                $orderData = [
                    'order_name' => $request->Name,
                    'uu_id' => $uu_id,
                    'user_id' => $this->user->id,
                    'company_id' => $company->id,
                    'order_type' => 'NEW ORDER',
                ];
                $newOrder = $this->order_service->store($orderData);

                $input[] = $request->TelephoneNumber;
                if($company->balance < count($request->TelephoneNumber)*2)
                {
                    return $this->error("No enough amount in wallet to continue purchase",'500');
                }
                $response = $this->order_service->createOrder($input,$request->Name,$request->SiteId,$newOrder->uu_id);
            }

            if($response['order']->OrderStatus == "COMPLETE" || $response['order']->OrderStatus == "Complete")
            {

                $newOrder->bandwidth_order_id = $response['order']->Order->id;
                $newOrder->order_status = $response['order']->OrderStatus;
                $newOrder->save();

                $data['OrderStatus'] = $response['order']->OrderStatus;
                $data['Name'] = $response['order']->Order->Name;
                $data['SiteId'] = $response['order']->Order->SiteId;
                if($response['order']->CompletedQuantity == 1)
                {
                    $phone_number = $this->phone_number_service->findDisconnected($response['order']->CompletedNumbers->TelephoneNumber['FullNumber'],$company->id);
                    if($phone_number)
                    {
                        if($phone_number->status == "DISCONNECTED")
                        {
                            $phone_number->running_state = NULL;
                            $phone_number->save();
                        }
                    }

                    $phoneData = [];
                    $phoneData = [
                        'uu_id' => (string)Str::uuid(),
                        'created_by_id' => $this->user->id,
                        'company_id' => $company->id,
                        'phone_number' => $response['order']->CompletedNumbers->TelephoneNumber['FullNumber'],
                        'nick_name' => $company->nick_name,
                        'status' => 'ACTIVE',
                        'running_state' => 1,
                    ];
                    $new_phone = $this->phone_number_service->store($phoneData);
                    $company_activities = $phoneData;
                    $company_activities['type'] = 'buy_number';
                    $company_activities['order_status'] = 'complete';
                    event(new CompanyActivity($company_activities));
                    $this->payForPhoneNumberPurchase($new_phone,$company);
                    $orderItemsData = [
                        'uu_id' => (string)Str::uuid(),
                        'order_id' => $newOrder->id,
                        'phone_number' =>  $response['order']->CompletedNumbers->TelephoneNumber['FullNumber'],
                        'order_status' => $response['order']->OrderStatus,
                        'city' => $response['order']->CompletedNumbers->TelephoneNumber['City'],
                        'lata' => $response['order']->CompletedNumbers->TelephoneNumber['LATA'],
                        'rate_center' => $response['order']->CompletedNumbers->TelephoneNumber['RateCenter'],
                        'state' => $response['order']->CompletedNumbers->TelephoneNumber['State'],
                        'tier' => $response['order']->CompletedNumbers->TelephoneNumber['Tier'],
                        'vendor_id' => $response['order']->CompletedNumbers->TelephoneNumber['VendorId'],
                        'vendor_name' => $response['order']->CompletedNumbers->TelephoneNumber['VendorName'],
                    ];
                    // dd($orderItemsData);
                    $this->order_item_service->store($orderItemsData);
                    $data['OrderedTelephoneNumber'] = $response['order']->CompletedNumbers->TelephoneNumber['FullNumber'];
                }
                else
                {
                    $phone_numbers = $response['order']->CompletedNumbers->TelephoneNumber;
                    foreach($phone_numbers as $phone_number)
                    {
                        $phone = $this->phone_number_service->findDisconnected($phone_number['FullNumber'],$company->id);
                        if($phone)
                        {
                            if($phone->status == "DISCONNECTED")
                            {
                                $phone->running_state = NULL;
                                $phone->save();
                            }
                        }
                        $phoneData = [];
                        $phoneData = [
                            'uu_id' => (string)Str::uuid(),
                            'created_by_id' => $this->user->id,
                            'company_id' => $company->id,
                            'phone_number' => $phone_number['FullNumber'],
                            'nick_name' => $company->nick_name,
                            'status' => "ACTIVE",
                            'running_state' => 1,
                        ];
                        $new_phone = $this->phone_number_service->store($phoneData);
                        $company_activities = $phoneData;
                        $company_activities['type'] = 'buy_number';
                        $company_activities['order_status'] = 'complete';
                        event(new CompanyActivity($company_activities));
                        $this->payForPhoneNumberPurchase($new_phone,$company);
                        $orderItemsData = [
                            'uu_id' => (string)Str::uuid(),
                            'order_id' => $newOrder->id,
                            'phone_number' =>  $phone_number['FullNumber'],
                            'order_status' => $response['order']->OrderStatus,
                            'city' => $phone_number['City'],
                            'lata' => $phone_number['LATA'],
                            'rate_center' => $phone_number['RateCenter'],
                            'state' => $phone_number['State'],
                            'tier' => $phone_number['Tier'],
                            'vendor_id' => $phone_number['VendorId'],
                            'vendor_name' => $phone_number['VendorName'],
                        ];
                        $this->order_item_service->store($orderItemsData);
                    }
                    $data['OrderedTelephoneNumber'] = collect($response['order']->CompletedNumbers->TelephoneNumber)->pluck('FullNumber');
                }
                $data['order_date'] = $response['order']->OrderCompleteDate;
                $data['orderMessage'] = 'Order Created';
                return $this->success($data);
            }
            else if($response['order']->OrderStatus == "FAILED")
            {
                $newOrder->bandwidth_order_id = $response['order']->Order->id;
                $newOrder->order_status = $response['order']->OrderStatus;
                $newOrder->save();

                if($response['order']->FailedQuantity == 1)
                {
                    $orderItemsData = [
                        'uu_id' => (string)Str::uuid(),
                        'order_id' => $newOrder->id,
                        'phone_number' =>  $response['order']->FailedNumbers->FullNumber,
                        'order_status' => $response['order']->OrderStatus,

                    ];
                    $this->order_item_service->store($orderItemsData);
                    $company_activities = $orderItemsData;
                    $company_activities['type'] = 'buy_number';
                    $company_activities['company_id'] = $company->id;
                    event(new CompanyActivity($company_activities));
                }
                else
                {
                    $phone_numbers = $response['order']->FailedNumbers->FullNumber;

                    foreach($phone_numbers as $phone_number)
                    {
                        $orderItemsData = [
                            'uu_id' => (string)Str::uuid(),
                            'order_id' => $newOrder->id,
                            'phone_number' =>  $phone_number,
                            'order_status' => $response['order']->OrderStatus,

                        ];
                        $this->order_item_service->store($orderItemsData);
                        $company_activities = $orderItemsData;
                        $company_activities['type'] = 'buy_number';
                        $company_activities['company_id'] = $company->id;
                        event(new CompanyActivity($company_activities));
                    }
                }
                return $this->error("The requested Number is unavailable",'500');
            }
            else if($response['order']->OrderStatus == "PARTIAL")
            {
                $newOrder->bandwidth_order_id = $response['order']->Order->id;
                $newOrder->order_status = $response['order']->OrderStatus;
                $newOrder->save();

                $data['OrderStatus'] = $response['order']->OrderStatus;
                $data['Name'] = $response['order']->Order->Name;
                $data['SiteId'] = $response['order']->Order->SiteId;
                if($response['order']->CompletedQuantity == 1)
                {
                    $phone_number = $this->phone_number_service->findDisconnected($response['order']->CompletedNumbers->TelephoneNumber['FullNumber'],$company->id);
                    if($phone_number && $phone_number->status == "DISCONNECTED")
                    {
                        $phone_number->running_state = NULL;
                        $phone_number->save();
                    }
                    $phoneData = [];
                    $phoneData = [
                        'uu_id' => (string)Str::uuid(),
                        'created_by_id' => $this->user->id,
                        'company_id' => $company->id,
                        'phone_number' => $response['order']->CompletedNumbers->TelephoneNumber['FullNumber'],
                        'nick_name' => $company->nick_name,
                        'status' => 'ACTIVE',
                        'running_state' => 1,
                    ];
                    $new_phone = $this->phone_number_service->store($phoneData);
                    $company_activities = $phoneData;
                    $company_activities['type'] = 'buy_number';
                    $company_activities['order_status'] = 'partial';
                    event(new CompanyActivity($company_activities));
                    $this->payForPhoneNumberPurchase($new_phone,$company);

                    $orderItemsData = [
                        'uu_id' => (string)Str::uuid(),
                        'order_id' => $newOrder->id,
                        'phone_number' =>  $response['order']->CompletedNumbers->TelephoneNumber['FullNumber'],
                        'order_status' => 'COMPLETE',
                        'city' => $response['order']->CompletedNumbers->TelephoneNumber['City'],
                        'lata' => $response['order']->CompletedNumbers->TelephoneNumber['LATA'],
                        'rate_center' => $response['order']->CompletedNumbers->TelephoneNumber['RateCenter'],
                        'state' => $response['order']->CompletedNumbers->TelephoneNumber['State'],
                        'tier' => $response['order']->CompletedNumbers->TelephoneNumber['Tier'],
                        'vendor_id' => $response['order']->CompletedNumbers->TelephoneNumber['VendorId'],
                        'vendor_name' => $response['order']->CompletedNumbers->TelephoneNumber['VendorName'],
                    ];
                    $this->order_item_service->store($orderItemsData);

                    $data['OrderedTelephoneNumber'] = $response['order']->CompletedNumbers->TelephoneNumber['FullNumber'];
                }
                else
                {
                    $phone_numbers = $response['order']->CompletedNumbers->TelephoneNumber;
                    foreach($phone_numbers as $phone_number)
                    {
                        $phone_number = $this->phone_number_service->findDisconnected($phone_number['FullNumber'],$company->id);
                        if($phone_number && $phone_number->status == "DISCONNECTED")
                        {
                            $phone_number->running_state = NULL;
                            $phone_number->save();
                        }

                        $phoneData = [];
                        $phoneData = [
                            'uu_id' => (string)Str::uuid(),
                            'created_by_id' => $this->user->id,
                            'company_id' => $company->id,
                            'phone_number' => $phone_number['FullNumber'],
                            'nick_name' => $company->nick_name,
                            'status' => "ACTIVE",
                            'running_state' => 1,

                        ];
                        $new_phone = $this->phone_number_service->store($phoneData);
                        $company_activities = $phoneData;
                        $company_activities['type'] = 'buy_number';
                        $company_activities['order_status'] = 'partial';
                        event(new CompanyActivity($company_activities));
                        $this->payForPhoneNumberPurchase($new_phone,$company);

                        $orderItemsData = [
                            'uu_id' => (string)Str::uuid(),
                            'order_id' => $newOrder->id,
                            'phone_number' =>  $phone_number['FullNumber'],
                            'order_status' => 'COMPLETE',
                            'city' => $phone_number['City'],
                            'lata' => $phone_number['LATA'],
                            'rate_center' => $phone_number['RateCenter'],
                            'state' => $phone_number['State'],
                            'tier' => $phone_number['Tier'],
                            'vendor_id' => $phone_number['VendorId'],
                            'vendor_name' => $phone_number['VendorName'],
                        ];
                        $this->order_item_service->store($orderItemsData);
                    }
                    $data['OrderedTelephoneNumber'] = collect($response['order']->CompletedNumbers->TelephoneNumber)->pluck('FullNumber');
                }
                if($response['order']->FailedQuantity == 1)
                {
                    $orderItemsData = [
                        'uu_id' => (string)Str::uuid(),
                        'order_id' => $newOrder->id,
                        'phone_number' =>  $response['order']->FailedNumbers->FullNumber,
                        'order_status' => 'FAILED',

                    ];
                    $this->order_item_service->store($orderItemsData);
                }
                else
                {
                    $phone_numbers = $response['order']->FailedNumbers->FullNumber;

                    foreach($phone_numbers as $phone_number)
                    {
                        $orderItemsData = [
                            'uu_id' => (string)Str::uuid(),
                            'order_id' => $newOrder->id,
                            'phone_number' =>  $phone_number,
                            'order_status' => 'FAILED',

                        ];
                        $this->order_item_service->store($orderItemsData);
                    }
                }
                $data['NotOrderedNumber'] = $response['order']->ErrorList->Error->TelephoneNumber;
                $data['order_date'] = $response['order']->OrderCompleteDate;
                $data['orderMessage'] = 'Partial Order Created';
                return $this->success($data);
            }
            else
            {
                return $this->error($response['error'],'500');
            }
        }
        catch(Exception $e)
        {
            return $this->error($e->getMessage());
        }
    }

    public function sendSMS(Request $request)
    {
        $company = $this->company_service->adminFindByUuid($request->companyId);
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

        $phone_number = $this->phone_number_service->findByuuid($from);
        if ($phone_number && $phone_number->company_id == $company->id) {
            if ($phone_number->status == "ACTIVE" && $phone_number->running_state == 1) {
                try {
                    if($company->balance < count($to))
                    {
                        return $this->error("No enough amount in wallet to continue purchase",'500');
                    }

                    $response = $this->message_service->sendSMS($to, $from, $message, $tag);

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
                        'created_by_id' => $company->user_id,
                        'company_id' => $phone_number->company_id,
                        'phone_number_id' => $phone_number->id,
                        'to_number' => collect($response->to),
                        'message' => $response->text,
                        'is_group' => $is_group,
                        'status' => 1,

                    ];
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

    public function getSentMessages(Request $request)
    {
        $company = $this->company_service->adminFindByUuid($request->companyId);
        if (!isset($company->id)) {
            return $this->error("Company Not Found", 404);
        }
        $data =  $this->sms_messageout_service->getData($company->id);
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

    public function getReceivedMessages(Request $request)
    {
        $company = $this->company_service->adminFindByUuid($request->companyId);
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

    public function searchSentMessages(Request $request)
    {
        $company = $this->company_service->adminFindByUuid($request->companyId);
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
        $company = $this->company_service->adminFindByUuid($request->companyId);
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

}
