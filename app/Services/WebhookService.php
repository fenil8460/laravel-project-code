<?php

namespace App\Services;

use App\Repositories\WebhookRepository;

class WebhookService
{
    protected $webhook_repository;
    public function __construct()
    {
        $this->webhook_repository = new WebhookRepository;
    }
    public function getData()
    {
        return $this->webhook_repository->getData();
    }
    public function store($data)
    {
        return $this->webhook_repository->store($data);
    }
    public function show($id)
    {
        return $this->webhook_repository->show($id);
    }

    public function update($data, $id)
    {
        return $this->webhook_repository->update($data, $id);
    }
    public function destroy($id)
    {
        return $this->webhook_repository->destroy($id);
    }
    public function getWebhookCall()
    {
        return $this->webhook_repository->getWebhookCall();
    }

    public function deleteWebhookCall($id)
    {
        return $this->webhook_repository->deleteWebhookCall($id);
    }
}
