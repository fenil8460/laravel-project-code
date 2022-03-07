<?php

namespace Ogt\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class DeveloperLibrary extends Controller
{
    protected $base_url = 'http://127.0.0.1:8090';

    function getCompanies($api_key,$token){
            $response = Http::withToken($api_key)->get($this->base_url.'/api/v1/developer/all-companies?api_token='.$token);
            return $response;
        }

    function createOrder($api_key,$token,$data){
        $response = Http::withToken($api_key)->post($this->base_url.'/api/v1/developer/orders?api_token='.$token,$data);
        return $response;
    }

    function getInserviceNumber($api_key,$token){
        $response = Http::withToken($api_key)->get($this->base_url.'/api/v1/developer/inservice-numbers?api_token='.$token);
        return $response;
    }

    function getAllDisconnectedNumbers($api_key,$token){
        $response = Http::withToken($api_key)->get($this->base_url.'/api/v1/developer/all-disconnected-numbers?api_token='.$token);
        return $response;
    }

    function findDisconnectedNumber($api_key,$token){
        $response = Http::withToken($api_key)->get($this->base_url.'/api/v1/developer/disconnects/9032315631?api_token='.$token);
        return $response;
    }

    function getOrders($api_key,$token){
        $response = Http::withToken($api_key)->get($this->base_url.'/api/v1/developer/orders?api_token='.$token);
        return $response;
    }

    function findInserviceNumber($api_key,$token,$company_id){
        $response = Http::withToken($api_key)->get($this->base_url.'/api/v1/developer/inservice-numbers/'.$company_id.'?api_token='.$token);
        return $response;
    }

    function disconnectNumber($api_key,$token,$data){
        $response = Http::withToken($api_key)->post($this->base_url.'/api/v1/developer/disconnects?api_token='.$token,$data);
        return $response;
    }

    function reconnectNumber($api_key,$token,$phone_number,$data){
        $response = Http::withToken($api_key)->post($this->base_url.'/api/v1/developer/reconnect/'.$phone_number.'?api_token='.$token,$data);
        return $response;
    }

    function smsSend($api_key,$token,$data){
        $response = Http::withToken($api_key)->post($this->base_url.'/api/v1/developer/sms?api_token='.$token,$data);
        return $response;
    }

    function getSmsSend($api_key,$token,$company_id){
        $response = Http::withToken($api_key)->get($this->base_url.'/api/v1/developer/sms_sent?api_token='.$token.'&companyId='.$company_id);
        return $response;
    }

    function searchSendMessage($api_key,$token,$company_id,$message){
        $response = Http::withToken($api_key)->get($this->base_url.'/api/v1/developer/search-sent-sms?api_token='.$token.'&message='.$message.'&companyId='.$company_id);
        return $response;
    }

    function searchReceivedMessage($api_key,$token,$company_id,$message="Message"){
        $response = Http::withToken($api_key)->get($this->base_url.'/api/v1/developer/search-received-sms?api_token='.$token.'&message='.$message.'&companyId='.$company_id);
        return $response;
    }

    function getReceivedMessage($api_key,$token,$company_id){
        $response = Http::withToken($api_key)->get($this->base_url.'/api/v1/developer/sms_received?api_token='.$token.'&companyId='.$company_id);
        return $response;
    }    
}


