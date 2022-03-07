<?php


namespace App\Library\Bandwidth;

class ApiConnector
{

    public function __construct()
    {
        set_time_limit(600);

        $this->username      = config('bandwidth.credentials.username');
        $this->password      = config('bandwidth.credentials.password');
        $this->accountId     = config('bandwidth.credentials.accountid');
        $this->applicationId = config('bandwidth.credentials.appid');
        $this->dashboard    = config('bandwidth.credentials.dashboard');
        $this->getMessageURL = config('bandwidth.credentials.messageURL');

    }
}
