<?php

namespace App\Repositories;

use App\Models\MessageTemplate;
use App\Traits\FindAPI;

class MessageTemplateRepository
{
    use FindAPI;

    public function store($data)
    {
        return MessageTemplate::create($data);
    }

    public function getData($company_id)
    {
        return MessageTemplate::where('company_id',$company_id)->get();
    }

    public function show($id,$company_id,$user_id)
    {
        $message_template = MessageTemplate::where('uu_id',$id)->where('company_id',$company_id)->where('user_id',$user_id);
        return $this->findResource($message_template);
    }

    public function find($id,$user_id)
    {
        $message_template = MessageTemplate::where('id',$id)->where('user_id',$user_id)->first();
        return $message_template;
    }

    public function update($data, $id, $user_id)
    {
        $message_template = MessageTemplate::where('uu_id',$id)->where('user_id',$user_id);
        return $this->updateResource($message_template,$data);
    }

    public function destroy($id,$user_id)
    {
        $message_template = MessageTemplate::where('uu_id',$id)->where('user_id',$user_id);
        return $this->destroyResource($message_template);
    }

    public function getMessageTemplateCompany($id, $user_id)
    {
        $message_template = MessageTemplate::where('company_id',$id)->where('user_id',$user_id);
        return $this->findResource($message_template);
    }


}
