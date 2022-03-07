<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\ClientCompany;
use App\Models\User;
use App\Traits\FindAPI;


class ClientRepository
{
    use FindAPI;

    public function showClients($company_id)
    {
        return Client::where('company_id',$company_id)->get();
    }

    public function createClient($data)
    {
        return Client::create($data);
    }

    public function findClient($id)
    {
        return Client::find($id);
    }

    public function findClientByEmail($email,$company_id)
    {
        return Client::where('email',$email)->where('company_id',$company_id)->where('status','!=',6)->get();
    }

    public function findClientByUuid($id)
    {
        return Client::where('uu_id',$id)->first();
    }

    public function removeClientCompany($client)
    {
        return ClientCompany::where('client_id',$client->id)->where('company_id',$client->company_id)?ClientCompany::where('client_id',$client->id)->where('company_id',$client->company_id)->delete():"";
    }

    public function deleteClient($id)
    {
        $client = Client::where('uu_id',$id);
        return $this->destroyResource($client);
    }

    public function findClientCompany($client_ids)
    {
        return  ClientCompany::whereIn('client_id',$client_ids)->get();
    }

    public function deleteClientCompany($client_id)
    {
        $client = ClientCompany::where('client_id',$client_id);
        return $this->destroyResource($client);
    }

    public function clientIdsOfSameUser($user_id)
    {
        $client = Client::where('user_id',$user_id)->pluck('id')->toArray();
        return $client;
    }


}
