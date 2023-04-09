<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;


class TelegramService
{
    protected $token;

    public function __construct($token = null)
    {
        $this->token = $token ?? env('TELEGRAM_BOT_TOKEN');
    }

    public function getMe()
    {
        return $this->execute('getMe');
    }

    public function getUpdates(int $offset = 0)
    {
        return $this->execute('getUpdates', []);
    }

    public function createChatInviteLink(array $body)
    {
        return $this->execute('createChatInviteLink', $body);
    }

    public function sendMessage(array $body)
    {
        return $this->execute('sendMessage', $body);
    }
    public function setWebhook(array $body)
    {
        return $this->execute('setWebhook', $body);
    }
    public function getWebhookInfo()
    {
        return $this->execute('getWebhookInfo');
    }
    public function deleteWebhook()
    {
        return $this->execute('deleteWebhook', [
            'drop_pending_updates' => true
        ]);
    }

    protected function execute($method, $params = [])
    {
        $url = 'https://api.telegram.org/bot' . $this->token . '/' . $method;
        $request = Http::post($url, $params);
        return $request->json('result', []);
    }
}
