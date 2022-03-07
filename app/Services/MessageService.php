<?php

namespace App\Services;

use App\Repositories\MessageRepository;

class MessageService
{
    protected $communicate_phone;

    public function __construct()
    {
        $this->accountId = config('bandwidth.credentials.accountid');
        $this->message_repository = new MessageRepository;
    }

    public function sendSms($to, $from, $message, $tag)
    {
        return $this->message_repository->send_sms($to, $from, $message, $tag);
    }


}


    // public function getMessages($messageId,$sourceTn,$destinationTn,$messageStatus,$errorCode,$fromDateTime,$toDateTime,$pageToken,$limit)
    // {
    //     try
    //     {
    //         $response =  $this->communicate_phone->getSMS($this->accountId,$messageId,$sourceTn,$destinationTn,$messageStatus,$errorCode,$fromDateTime,$toDateTime,$pageToken,$limit);
    //         return $response;
    //     }
    //     catch(\Exception $e)
    //     {
    //         $response = $e->getMessage();
    //         return $response;
    //     }
    // }
