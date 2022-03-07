<?php

namespace App\Services;

use App\Repositories\SmsMessageInRepository;
use Illuminate\Support\Facades\Auth;

class SmsMessageInService
{
    protected $sms_messagein_repository;
    public function __construct()
    {
        $this->sms_messagein_repository = new SmsMessageInRepository;
    }
    public function getData($company_id)
    {
        $messages = $this->sms_messagein_repository->getData($company_id);
        return $messages;
    }
    public function store($data)
    {
        return $this->sms_messagein_repository->store($data);
    }


    public function show($company_id,$message)
    {
        return $this->sms_messagein_repository->show($company_id,$message);
    }

    public function update($data,$id)
    {
        return $this->sms_messagein_repository->update($data,$id);
    }
    public function destroy($id){
        return $this->sms_messagein_repository->destroy($id);

    }

}
