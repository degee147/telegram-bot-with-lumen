<?php

namespace App\api\v1\Controllers;

// namespace App

use App\Models\User;
use League\Flysystem\File;
use Illuminate\Http\Request;
use App\Services\TelegramService;
use App\Ultainfinity\Ultainfinity;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class TelegramController extends Controller
{
    use Ultainfinity;

    public function getUpdates()
    {
        $updates = (new TelegramService())->getUpdates();
        return response()->json($updates, 200);
    }
    public function getBotInfo()
    {
        $updates = (new TelegramService())->getMe();
        return response()->json($updates, 200);
    }

    public function sendMessage(Request $request)
    {
        $this->validate($request, [
            'chat_id' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string'],
        ]);
        $message = (new TelegramService())->sendMessage($request->all());
        return response()->json($message, 200);
    }
    public function createChatInviteLink(Request $request)
    {
        $this->validate($request, [
            'chat_id' => ['required', 'string', 'max:255'],
        ]);
        $message = (new TelegramService())->createChatInviteLink($request->all());
        return response()->json($message, 200);
    }


    public function sendMessageToAllUsers(Request $request)
    {
        $this->validate($request, [
            'text' => ['required', 'string'],
        ]);
        $data = $request->all();
        $users = User::all();
        foreach ($users as $key => $user) {
            $data['chat_id'] = $user->telegram_id;
            $message = (new TelegramService())->sendMessage($data);
        }
        return $this->AppResponse('success', "message sent", 200);
    }

    public function webhook(Request $request)
    {
        // avaialable object properties: https://core.telegram.org/bots/api#update
        $data = $request->all();
        $userData = $data['message']['from'] ?? [];
        // $messageTxt = $data['message']['text'] ?? ''; //for when needed
        if (!empty($userData)) {
            $user = User::where('telegram_id', $userData['id'])->first();
            if (!$user) {
                $input = [
                    "first_name" => $userData['first_name'],
                    "last_name" => $userData['last_name'],
                    "telegram_id" => $userData['id']
                ];
                User::create($input);
            }
        }
        return response()->json("success", 200);
    }

    public function setWebhook(Request $request)
    {
        $this->validate($request, [
            'url' => ['required', 'string'],
        ]);
        $message = (new TelegramService())->setWebhook($request->all());
        return response()->json($message, 200);
    }
    public function getWebhookInfo()
    {
        $info = (new TelegramService())->getWebhookInfo();
        return response()->json($info, 200);
    }
    public function deleteWebhook()
    {
        $message = (new TelegramService())->deleteWebhook();
        return response()->json($message, 200);
    }
}
