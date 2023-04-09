<?php

namespace App\api\v1\Controllers;

// namespace App

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\TelegramService;
use App\Ultainfinity\Ultainfinity;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    use Ultainfinity;
    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Get users",
     *     description="Returns a paginated list of users saved from interactions between the users and the bot, inclusing channels and groups bot has admin access to",
     *     @OA\Response(
     *         response=200,
     *         description="Everything OK"
     *     )
     * )
     */
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(10);
        return response()->json($users, 200);
    }



     /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Get Single users",
     *     description="Returns a single user info",
     *     @OA\Response(
     *         response=200,
     *         description="Everything OK"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User not found"
     *     )
     * )
     */
    public function show($id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json($user, 200);
        }
        return $this->AppResponse('failed', 'User not found', 404);
    }

}
