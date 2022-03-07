<?php

namespace App\Repositories;

use App\Models\SmsMessageOut;
use App\Traits\FindAPI;
use App\Library\Bandwidth\CommunicatePhoneClass;
use Illuminate\Support\Facades\Auth;
use App\Models\PhoneNumber;
use Exception;

class MessageRepository
{
    use FIndAPI;

    public function __construct()
    {
        $this->communicate_phone = new CommunicatePhoneClass;
    }

    public function send_sms($to, $from, $message, $tag)
    {
        try
        {
            $phone_number_from = PhoneNumber::find($from);
            $phone_numbers_to = $to;

            if($phone_number_from != NULL && count($phone_numbers_to) != 0)
            {
                try{
                    $response =  $this->communicate_phone->sendSMS($phone_numbers_to, $phone_number_from->phone_number, $message, $tag);
                    return $response;
                }
                catch(Exception $e)
                {
                    $response = $e->getMessage();
                    return $response;
                }

            }
            else
            {
                $response = ["Error" => "Number Does not belong to your company"];
                return $response;
            }

        }
        catch(\Exception $e)
        {
            $response = $e->getMessage();
            return $response;
        }
    }


}
