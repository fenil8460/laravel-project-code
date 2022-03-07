<?php

namespace App\Services;

use App\Repositories\ClientRepository;
use Illuminate\Support\Collection;

class ClientService
{
    protected $client_repository,$register_service;

    public function __construct()
    {
        $this->client_repository = new ClientRepository;
    }

    public function showClients($company_id)
    {
        return $this->client_repository->showClients($company_id);
    }

    public function createClient($data)
    {
        return $this->client_repository->createClient($data);
    }

    public function findClient($id)
    {
        return $this->client_repository->findClient($id);
    }

    public function findClientByUuid($id)
    {
        return $this->client_repository->findClientByUuid($id);
    }
    
    public function findClientByEmail($email,$company_id)
    {
        return $this->client_repository->findClientByEmail($email,$company_id);
    }
    
    public function removeClientCompany($client)
    {
        return $this->client_repository->removeClientCompany($client);

    }

    public function deleteClient($id)
    {
        return $this->client_repository->deleteClient($id);
    }

    public function findClientCompany($id)
    {
        return $this->client_repository->findClientCompany($id);
    }

    public function deleteClientCompany($id)
    {
        return $this->client_repository->deleteClientCompany($id);
    }

    public function clientIdsOfSameUser($user_id)
    {
        return $this->client_repository->clientIdsOfSameUser($user_id);
    }
    

}
