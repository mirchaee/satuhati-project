<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function saveToken(Request $request)
{
    $user = auth()->user();

    $user->fcm_token = $request->token;
    $user->save();

    return response()->json([
        'success' => true,
        'user' => $user->name,
        'role' => $user->role,
        'token_saved' => $user->fcm_token,
    ]);
}
}