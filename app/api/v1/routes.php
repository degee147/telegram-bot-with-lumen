<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\api\v1\Controllers\TelegramController;



$router->get('/users', 'UsersController@index');
$router->get('users/{id}', 'UsersController@show');
$router->get('/botinfo', 'TelegramController@getBotInfo');
$router->get('/getupdates', 'TelegramController@getUpdates');
$router->post('/sendmessage', 'TelegramController@sendMessage');
$router->post('/sendmessagetoall', 'TelegramController@sendMessageToAllUsers');
$router->post('/invitelink', 'TelegramController@createChatInviteLink');
$router->post('/webhook', 'TelegramController@webhook');
$router->post('/setwebhook', 'TelegramController@setWebhook');
$router->get('/webhookinfo', 'TelegramController@getWebhookInfo');
$router->get('/deletewebhook', 'TelegramController@deleteWebhook');

