<?php

namespace App\Traits;
use Pusher\Pusher;

trait PusherTrait
{

    public function sendNotification($msg)
    {
        // Create a UUID to the model if it does not have one
        $options = array(
            'cluster' => 'ap2',
            'encrypted' => true
        );
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'), $options
        );
        $message= $msg;
        $pusher->trigger('my-channel', 'my-event', $message);
    }
}
