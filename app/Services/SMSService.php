<?php

namespace App\Services;

use App\Traits\UseExternalServiceTrait;

class SMSService {

    use UseExternalServiceTrait;

    public $baseUri;

    public function __construct()
    {
        $this->baseUri = config('services.sms.base_uri');
    }

    public function sendMessage($text, $msisdn)
    {
        $headers = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ];

        $body = [
            'channel'  => [
                'channel'   => config('services.sms.channel'),
                'password'  => config('services.sms.password'),
            ],

            'messages'  => [
                [
                    'text'      => $text,
                    'msisdn'    => $msisdn,
                    'source'    => config('services.sms.source'),
                ]
            ],
        ];

        $this->performRequest('POST', '/fasthub/messaging/json/api', $headers,  $body);
    }
}