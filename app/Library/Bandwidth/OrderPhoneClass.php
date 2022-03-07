<?php

namespace App\Library\Bandwidth;

use App\Library\Bandwidth\ApiConnector;

class OrderPhoneClass extends ApiConnector
{

    public function client()
    {
        return new \Iris\Client($this->username, $this->password, ['url' => $this->dashboard]);
    }
    public function account()
    {
        return new \Iris\Account($this->accountId, $this->client());
    }

    public function getAvailableNumbers($input)
    {
        $data['quantity'] = isset($input['quantity']) ? $input['quantity'] : 100;
        if(isset($input['areaCode'])){
            $data['areaCode']=$input['areaCode'];
        }
        if(isset($input['city'])){
            $data['city']=$input['city'];
        }
        if(isset($input['lata'])){
            $data['lata']=$input['lata'];
        }
        if(isset($input['rateCenter'])){
            $data['rateCenter']=$input['rateCenter'];
        }
        if(isset($input['state'])){
            $data['state']=$input['state'];
        }
        if(isset($input['tollFreeWildCardPattern'])){
            $data['tollFreeWildCardPattern']=$input['tollFreeWildCardPattern'];
        }
        if(isset($input['zip'])){
            $data['zip']=$input['zip'];
        }
        
        return $this->account()->availableNumbers($data);//[0]->TelephoneNumber;
    }

    public function buyNumber($numbers = [], $name="Test", $siteid = "61075",$customerOrderId)
    {
        $orderingNumbers = [];
        foreach($numbers as $number){
            $orderingNumbers['TelephoneNumber'] = $number;
        }
        $order = $this->account()->orders()->create([
            "Name" => $name,
            "SiteId" => $siteid,
            "CustomerOrderId" => $customerOrderId,
            "ExistingTelephoneNumberOrderType" => [
                "TelephoneNumberList" => $orderingNumbers
            ]
        ]);
        sleep(2);
        $response = $this->account()->orders()->order($order->id, true);
        return $response;
    }


    public function getNumbers()
    {
        return $this->account()->inserviceNumbers()->TelephoneNumber;
    }

    public function disconnectNumber($numbers = [], $name="DisconnectOrder",$customer_order_id)
    {
        $disconnectingNumbers = [];
        foreach($numbers as $number){
            $disconnectingNumbers[]['TelephoneNumber'] = $number;
        }
        $disconnect = $this->account()->disconnects()->create([
            "name" => $name,
            "CustomerOrderId" => $customer_order_id,
            "DisconnectTelephoneNumberOrderType" => [
                "TelephoneNumberList" => $disconnectingNumbers
            ]
        ]);
        return $disconnect;

    }

    public function getDisconnectedNumbers($id)
    {
        $disconnect = $this->account()->disconnects()->disconnect($id,true);
        return $disconnect->DisconnectTelephoneNumberOrderType;
    }

    public function getAllDisconnectedNumbers()
    {
        return $this->account()->disnumbers()->TelephoneNumber;
    }



}
