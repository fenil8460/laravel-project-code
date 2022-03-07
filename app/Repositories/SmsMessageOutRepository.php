<?php

namespace App\Repositories;

use App\Models\SmsMessageOut;
use App\Traits\FindAPI;

class SmsMessageOutRepository
{
    use FIndAPI;

    public function getData($company_id)
    {
        $messages = SmsMessageOut::where('company_id',$company_id);
        if($messages->exists())
        {
            return [
                'status' => true,
                'Message' => $messages->paginate(20),
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

    public function getMessageOut($company_id)
    {
        $messages = SmsMessageOut::where('company_id',$company_id);
        if($messages->exists())
        {
            return $this->findResource($messages);
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
        return SmsMessageOut::create($data);
    }
    public function show($company_id,$message)
    {
        $message = SmsMessageOut::where('company_id',$company_id)->where('message', 'like', '%' . $message . '%');
        return $this->findResource($message);
    }

    public function update($data, $id)
    {
        $company = SmsMessageOut::find($id);
        return $company->update($data);
    }

    public function find($id,$company_id)
    {
        $company = SmsMessageOut::where('id',$id)->where('company_id',$company_id)->get();
        return $company;
    }

    public function destroy($id)
    {
        $company = SmsMessageOut::find($id);
        $company->delete();
    }

    public function sendSMS($data)
    {
      $is_array=is_array($data["to_number"]);
        if (!$is_array) {
            $processData["is_group"] = 0;
            $processData["created_by_id"] = $data["created_by_id"];
            $processData["company_id"] = $data["company_id"];
            $processData["phone_number_id"] = $data["phone_number_id"];
            $processData["to_number"] = $data["to_number"];
            $processData["message"] = $data["message"];
            $processData["status"] = $data["status"];
            $processData["created_at"] = now();
            $processData["updated_at"] = now();
        } else {
         foreach($data["to_number"]  as  $to_number){
         $processData[] =[
                    "is_group"=>1,
                    "created_by_id"=>$data["created_by_id"],
                    "company_id"=> $data["company_id"],
                    "phone_number_id" => $data["phone_number_id"],
                    "to_number" => $to_number,
                    "message"=> $data["message"],
                    "status" => $data["status"],
                    "created_at" => now(),
                    "updated_at" => now()
                   ];
            }
        }
        return SmsMessageOut::insert($processData);
    }
}
