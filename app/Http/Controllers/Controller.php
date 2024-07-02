<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Telegram\Bot\Laravel\Facades\Telegram;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function callGameApi($method, $path, $params) {
        $client = new \GuzzleHttp\Client();
        $gameApi = env('GAME_API_ENDPOINT', '');
        $response = $client->request($method, $gameApi . $path, ["form_params" => $params]);
        $response = json_decode($response->getBody()->getContents(), true);
        return $response;
    }

    public function sendMessage($msg)
    {
        Telegram::sendMessage([
            'chat_id' => "-1002153831153",
            'parse_mode' => 'HTML',
            'text' => $msg,
        ]);
    }
}
