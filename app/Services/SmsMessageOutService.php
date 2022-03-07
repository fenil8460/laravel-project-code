<?php

namespace App\Services;

use App\Repositories\SmsMessageOutRepository;
use Illuminate\Support\Facades\Auth;
class SmsMessageOutService
{
    protected $sms_messageout_repository;

    public function __construct()
    {
        $this->sms_messageout_repository = new SmsMessageOutRepository;
    }
    public function getData($company_id)
    {
        $messages = $this->sms_messageout_repository->getData($company_id);
        return $messages;
    }

    public function getMessageOut($company_id)
    {
        $messages = $this->sms_messageout_repository->getMessageOut($company_id);
        return $messages;
    }

    public function store($data)
    {
        return $this->sms_messageout_repository->store($data);
    }
    public function show($company_id,$message)
    {
        return $this->sms_messageout_repository->show($company_id,$message);
    }

    public function find($id, $company_id){
        return $this->sms_messageout_repository->find($id,$company_id);
    }

    public function update($data,$id)
    {
        return $this->sms_messageout_repository->update($data,$id);
    }
    public function destroy($id)
    {
        return $this->sms_messageout_repository->destroy($id);
    }

    public function sendSMS($data)
    {
        return $this->sms_messageout_repository->sendSMS($data);
    }


}
