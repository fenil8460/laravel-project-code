<?php

namespace App\Repositories;

use App\Models\SmsMessageIn;
use App\Models\PhoneNumber;
use App\Traits\FindAPI;

class SmsMessageInRepository
{
    use FindAPI;

    public function getData($company_id)
    {
        $messages = SmsMessageIn::where('company_id',$company_id)->get();
        if($messages->count() > 0)
        {
            return [
                'status' => true,
                'Message' => $messages,
                'count' => $messages->count(),
            ];
        }
        else
        {
            return [
                'status' => false,
                'Message' => "Messages Not Found",
            ];
        }
    }
    public function store($data)
    {
        $number = PhoneNumber::where('id',$data['phone_number_id'])->where('status','ACTIVE')->where('running_state',1)->first();
        if($number)
        {
            return SmsMessageIn::create($data);
        }
    }


    public function show($company_id,$message)
    {
        $message = SmsMessageIn::where('company_id',$company_id)->where('message', 'like', '%' . $message . '%');
        return $this->findResource($message);
    }

    public function update($data, $id)
    {
        $sms = SmsMessageIn::find($id);
        return $sms->update($data);
    }
    public function destroy($id)
    {
        $sms = SmsMessageIn::find($id);
        $sms->delete();
    }
}
