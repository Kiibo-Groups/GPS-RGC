<?php

// app/Services/TwilioService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TwilioService
{
    protected $sid;
    protected $token;
    protected $from;

    public function __construct()
    {
        $this->sid = env('TWILIO_SID');
        $this->token = env('TWILIO_AUTH_TOKEN');
        $this->from = env('TWILIO_PHONE');
    }

    public function sendMessageSMS($destination, $message)
    {
        // $client = new \GuzzleHttp\Client();
        $client = new \GuzzleHttp\Client([
            'verify' => false // Desactiva la verificación SSL
        ]);
        $response = $client->request('POST', "https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json", [
            'auth' => [$this->sid, $this->token],
            'form_params' => [
                'From'  =>  $this->from,
                'To'    => '+52'.$destination,
                'Body'  => $message
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function sendMessageWhatsapp($destination, $message)
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', "https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json", [
            'auth' => [$this->sid, $this->token],
            'form_params' => [
                'From'  =>  'whatsapp:' . $this->from,
                'To'    => $destination,
                'Body'  => $message
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}