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

    /**
     * @OA\Get(
     *     path="/getupdates",
     *     summary="Get bot updates",
     *     description="Returns incoming updates using long polling. Returns an Array of 'Update' objects. More info at https://core.telegram.org/bots/api#getupdates",
     *     @OA\Response(
     *         response=200,
     *         description="Everything OK"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Returns false, if webhook is active"
     *     )
     * )
     */
    public function getUpdates()
    {
        $updates = (new TelegramService())->getUpdates();
        return response()->json($updates, 200);
    }

    /**
     * @OA\Get(
     *     path="/botinfo",
     *     summary="Get bot info",
     *     description="Returns information about the bot",
     *     @OA\Response(
     *         response=200,
     *         description="Everything OK"
     *     )
     * )
     */
    public function getBotInfo()
    {
        $updates = (new TelegramService())->getMe();
        return response()->json($updates, 200);
    }

    /**
     * @OA\Post(
     *     path="/sendmessage",
     *     summary="Send a message to user, group or channel",
     *     operationId="sendmessage",
     *     description="Send params in form-data. Add Accept:application/json in header",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Parameter(
     *         name="chat_id",
     *         in="query",
     *         description="accepts chat_id from user model. Channels use the @ prefix e.g @official_bot",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="text",
     *         in="query",
     *         description="text message to send",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * )
     */
    public function sendMessage(Request $request)
    {
        $this->validate($request, [
            'chat_id' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string'],
        ]);
        $message = (new TelegramService())->sendMessage($request->all());
        return response()->json($message, 200);
    }

     /**
     * @OA\Post(
     *     path="/invitelink",
     *     summary="Used to generate invitation link to join channel or group. Bots can simply use default links for invitation, e.g https://t.me/{botusername}",
     *     operationId="invitelink",
     *     description="Send params in form-data. Add Accept:application/json in header",
     *     @OA\Response(
     *         response=200,
     *         description="Return newly generated link",
     *     ),
     *     @OA\Parameter(
     *         name="chat_id",
     *         in="query",
     *         description="accepts chat_id from user model. Channels use the @ prefix e.g @official_bot",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     )
     * )
     */
    public function createChatInviteLink(Request $request)
    {
        $this->validate($request, [
            'chat_id' => ['required', 'string', 'max:255'],
        ]);
        $message = (new TelegramService())->createChatInviteLink($request->all());
        return response()->json($message, 200);
    }

  /**
     * @OA\Post(
     *     path="/sendmessagetoall",
     *     summary="Send message to all subscribed users",
     *     operationId="sendmessagetoall",
     *     description="Recieves message text. Add Accept:application/json in header",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Parameter(
     *         name="text",
     *         in="query",
     *         description="text message to send",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * )
     */
    public function sendMessageToAllUsers(Request $request)
    {
        $this->validate($request, [
            'text' => ['required', 'string'],
        ]);
        $data = $request->all();
        $users = User::all();
        foreach ($users as $key => $user) {
            $data['chat_id'] = $user->chat_id;
            $message = (new TelegramService())->sendMessage($data);
        }
        return $this->AppResponse('success', "message sent", 200);
    }

    public function webhook(Request $request)
    {
        // avaialable object properties: https://core.telegram.org/bots/api#update
        $data = $request->all();
        $userData = $data['message']['from'] ?? [];
        $chat_id = $data['message']['chat']['id'] ?? '';
        $chat_type = $data['message']['chat']['type'] ?? '';
        // $messageTxt = $data['message']['text'] ?? ''; //for when needed
        if (!empty($userData)) {
            $user = User::where('telegram_id', $userData['id'])->first();
            if (!$user) {
                $input = [
                    "first_name" => $userData['first_name'],
                    "last_name" => $userData['last_name'],
                    "telegram_id" => $userData['id'],
                    "chat_id" => $chat_id,
                    "chat_type" => $chat_type,
                ];
                User::create($input);
            }
        }
        return response()->json("success", 200);
    }

    /**
     * @OA\Post(
     *     path="/setwebhook",
     *     summary="Enable webhook for bot",
     *     operationId="setwebhook",
     *     description="Send params in form-data. Add Accept:application/json in header",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Parameter(
     *         name="url",
     *         in="query",
     *         description="accepts url to receive webhook update from Telegram",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     )
     * )
     */
    public function setWebhook(Request $request)
    {
        $this->validate($request, [
            'url' => ['required', 'string'],
        ]);
        $message = (new TelegramService())->setWebhook($request->all());
        return response()->json($message, 200);
    }

     /**
     * @OA\Get(
     *     path="/webhookinfo",
     *     summary="Get Webhook info",
     *     description="Use this method to get current webhook status. Requires no parameters. On success, returns a WebhookInfo object. If the bot is using getUpdates, will return an object with the url field empty",
     *     @OA\Response(
     *         response=200,
     *         description="Everything OK"
     *     )
     * )
     */
    public function getWebhookInfo()
    {
        $info = (new TelegramService())->getWebhookInfo();
        return response()->json($info, 200);
    }

     /**
     * @OA\Get(
     *     path="/deletewebhook",
     *     summary="remove webhook integration",
     *     description="Use this method to remove webhook integration if you decide to switch back to getUpdates. Returns True on success.",
     *     @OA\Response(
     *         response=200,
     *         description="Everything OK"
     *     )
     * )
     */
    public function deleteWebhook()
    {
        $message = (new TelegramService())->deleteWebhook();
        return response()->json($message, 200);
    }
}
