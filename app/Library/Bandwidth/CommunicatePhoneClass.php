<?php

namespace App\Library\Bandwidth;

use App\Library\Bandwidth\ApiConnector;
use Exception;

class CommunicatePhoneClass extends ApiConnector
{
    private function client()
    {
        $config = new \BandwidthLib\Configuration(
            array(
                'messagingBasicAuthUserName' => $this->username,
                'messagingBasicAuthPassword' => $this->password,
                'voiceBasicAuthUserName' => $this->username,
                'voiceBasicAuthPassword' => $this->password,
            )
        );

        return new \BandwidthLib\BandwidthClient($config);
    }

    private function messagingClient()
    {
        return $this->client()->getMessaging()->getClient();
    }

    public function sendSMS( $to = [], $from, $message, $tag )
    {
        $messagingClient     = $this->messagingClient();
        $body                = new \BandwidthLib\Messaging\Models\MessageRequest();
        $body->applicationId = '0fa9cd55-2536-4af0-8318-aa20a530579f';
        $body->from          = $from;
        $body->to            = $to;
        $body->text          = $message;
        $body->tag          =  $tag;
        try {
            $response = $messagingClient->createMessage($this->accountId,$body);
            return $response->getResult();
        } catch (Exception $e) {
            return($e);
        }
    }
}
