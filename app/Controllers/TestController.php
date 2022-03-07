<?php

namespace App\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use App\Services\PhoneNumberService;
use App\Services\SmsMessageInService;
use App\Services\SmsMessageOutService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Ogt\Developer\DeveloperLibrary;


class TestController extends Controller
{
    protected $company_service;
    protected $phone_number_service;
    protected $sms_messagein_service;
    protected $sms_messageout_service;
    protected $webhook_service;
    public function __construct()
    {
        $this->company_service = new CompanyService;
        $this->phone_number_service = new PhoneNumberService;
        $this->sms_messagein_service = new SmsMessageInService;
        $this->sms_messageout_service = new SmsMessageOutService;
        $this->webhook_service = new WebhookService;
    }
    public function index()
    {
        // return $this->company_service->getData();
        // return $this->phone_number_service->getData();
        // return $this->sms_messagein_service->getData();
        // return $this->sms_messageout_service->getData();
        return $this->webhook_service->getData();
    }

    public function store()
    {
        $data = [];
        // $data = [
        //     'user_id' => 1,
        //     'name' => 'aswin'
        // ];
        // $data=[
        //     'created_by_id'=>1,
        //     'company_id'=>1,
        //     'phone_number'=>"123456789",
        //     'nick_name'=>"sample1",
        //     'status'=>1
        // ];
        // $data=[
        //     'created_by_id'=>1,
        //     'company_id'=>1,
        //     'phone_number_id'=>1,
        //     'from_number'=>"123456789",
        //     'message'=>"test",
        //     'status'=>1
        // ];
        // $data=[
        //     'created_by_id'=>1,
        //     'company_id'=>1,
        //     'phone_number_id'=>1,
        //     'to_number'=>"123456789",
        //     'groupor_single'=>1,
        //     'message'=>"test",
        //     'status'=>1
        // ];
        // $data=[
        //     'created_by_id'=>1,
        //     'company_id'=>1,
        //     'action'=>"test",
        //     'url'=>"test"
        // ];
        // return $this->company_service->store();
        // return $this->phone_number_service->store();
        // return $this->sms_messagein_service->store();
        // return $this->sms_messageout_service->store();
        return $this->webhook_service->store($data);
    }

    public function show($id)
    {
        // return $this->company_service->show($id);
        //   return $this->phone_number_service->show($id);
        // return $this->sms_messagein_service->show($id);
        // return $this->sms_messageout_service->show($id);
        return $this->webhook_service->show($id);
    }

    public function update($id)
    {
        $data = [];
        // $data = [
        //     'name' => 'arjun'
        // ];
        // $data = [
        //     'nick_name' => 'sample'
        // ];
        // $data = [
        //     'from_number' => "123546789",
        //     'message' => "test1",

        // ];
        // $data=[
        //     'to_number'=>"123546789",
        //     'message'=>"test1",

        //     ];
        // $data=[
        //     'action'=>"test1",
        //     'url'=>"test1"

        //     ];
        // return $this->company_service->update($id);
        // return $this->phone_number_service->update($id);
        // return $this->sms_messagein_service->update($id);
        // return $this->sms_messageout_service->update($id);
        return $this->webhook_service->update($data, $id);
    }

    public function destroy($id)
    {
        // return $this->company_service->destroy($id);
        // return $this->phone_number_service->destroy($id);
        // return $this->sms_messagein_service->destroy($id);
        // return $this->sms_messageout_service->destroy($id);
        return $this->webhook_service->destroy($id);
    }
    public function sendSMS()
    {
        $data = [
            'created_by_id'=>1,
            'company_id'=>1,
            'phone_number_id'=>1,
            'to_number'=>"44444444444",
            'message'=>"Test",
            'status'=>1,
            'is_group'=>0
        ];

        return $this->sms_messageout_service->sendSMS($data);
    }

    public function buyNumber()
    {
        $client = new \Iris\Client("sabeer@onlinecheckwriter.com", "Zilmoney!123", ['url' => 'https://dashboard.bandwidth.com/api/']);
        $account = new \Iris\Account("5006433", $client);
        $numbers = $account->availableNumbers([ "areaCode" => "903", "quantity" => 1 ]);
        $sites = $account->sites();
        // $items = $sites->getList();
        print_r($sites);
    }

    public function test(){
        $data = new DeveloperLibrary();
        $test = $data->getReceivedMessage('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5NTY2ZmYyMC00OGYwLTQwYTAtYjRmNy0yMzg2NzBiMjQ5YjgiLCJqdGkiOiI0NjY5NzU1YzI4NDdlNTQxOGYxNjIyNjFiMGY3N2NkMzE5MTg0N2NiYTA5MmJjNmU5YTI4NTBmMWZiOTVkMWE5NzU2Y2U5YmI1ZDJmZGQwOSIsImlhdCI6MTY0NjI5NzMzOS45MDE2NDEsIm5iZiI6MTY0NjI5NzMzOS45MDE2NTUsImV4cCI6MTY2MjE5NDkzOS41OTkwOTYsInN1YiI6IjIxIiwic2NvcGVzIjpbXX0.fircvI1K-me4M-bIlbAyAad1oI7NjRVnjI0Kc0hNftzkFzKEKw2zbMZqeOtqGNaMhptHUYuzuZcdLyhlTYRp84YwRBozNBMqHT_lmnc36PIPBsricbztgYK9DwCG3qfkWXzZWuEpqPR2c8pkiVHtDvh2vB1BNHMY6QPn_2_TxfVoBfKI4QkEZqBczbNUdU9MeypXcKbJJljBdtUj3zk1a9FULPtf9KGkTDpTpB_fGpgDKuIDKSDttlr8c5Nh81k95WNdbuKq1fFaq8A0KOamlMBPCu1f1NLXg6fCF2_7bpAg4JD-Ug6Kl8_PAYvcIC0qe4w04bTTZz1t25lWDvpzThZ5U6xlejhbA2kArZ-8q-Xy5SivNwwScrjUTxqLjiZvfoHVeUCGMmaDAEEDGXwlQbhnW9a8yWA3kTLpaIGh62IqHzlcRw-Umpd6cfwSj6zwPMgtDRQoWtpSzgdXVRoYdkSu9QEQLSgi0CzRKgqw2d_Na2J_4XpQouRZFQHO1T19fgUcsFdfWa9DM5XakNlnc00ncvC9pYxNLHhQDDybmzQ_9NSG0fk0brrf_txomMTTYWRF4iHuZSAwGxn03EXjIQig_M28Mzt9jJtBfzYfRPvnZv9VpDa5WB32e46GJsK9qxQXlvojkEIlsB9CoDSWq9vHkZbCjHNdz1z-B4HYhsQ','fNHUoskjp3gLqdOrKbo7zYraxiME1Ow0Evcgxqg4tQUJ5MHqrxG1lnA1YxTg',"45b1cfb3-9695-4053-ab19-655e4624c1bd");
        return $test;
    }
}
