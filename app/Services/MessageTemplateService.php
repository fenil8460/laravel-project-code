<?php

namespace App\Services;

use App\Repositories\MessageTemplateRepository;
use Illuminate\Support\Facades\Auth;

class MessageTemplateService
{
    protected $group_repository;

    public function __construct()
    {
        $this->message_template_repository = new MessageTemplateRepository;
    }

    public function store($data)
    {
        return $this->message_template_repository->store($data);
    }

    public function show($id,$company_id)
    {
        return $this->message_template_repository->show($id,$company_id,Auth::user()->id);
    }

    public function find($id)
    {
        return $this->message_template_repository->find($id,Auth::user()->id);
    }

    public function update($data, $id)
    {
        return $this->message_template_repository->update($data, $id,Auth::user()->id);
    }

    public function getData($company_id)
    {
        return $this->message_template_repository->getData($company_id);
    }

    public function destroy($id)
    {
        return $this->message_template_repository->destroy($id,Auth::user()->id);
    }

    public function getMessageTemplateCompany($id)
    {
        return $this->message_template_repository->getMessageTemplateCompany($id,Auth::user()->id);
    }

}
