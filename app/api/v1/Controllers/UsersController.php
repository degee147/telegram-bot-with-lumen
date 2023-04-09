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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(10);
        return response()->json($users, 200);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
