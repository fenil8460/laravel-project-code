<?php

namespace App\Repositories;

use App\Models\Webhook;
use App\Models\WebhookCall;

class WebhookRepository
{
    public function getData()
    {
        return Webhook::all();
    }
    public function store($data)
    {
        return Webhook::create($data);
    }

    public function show($id)
    {
        return Webhook::find($id);
    }

    public function update($data, $id)
    {
        $company = Webhook::find($id);
        return $company->update($data);
    }
    public function destroy($id)
    {
        $company = Webhook::find($id);
        $company->delete();
    }

    public function getWebhookCall()
    {
        $webHook = WebhookCall::get();
        return $webHook;
    }

    public function deleteWebhookCall($id)
    {
        $webHook = WebhookCall::find($id);
        return $webHook->delete();
    }
}
