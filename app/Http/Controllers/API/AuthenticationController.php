<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    /**
     * Login user via email and password
     * 
     * @param  \App\Http\Request\LoginRequest $request;
     * 
     * @return \Illuminate\Http\JsonResponse 
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();

        if(!Auth::attempt($credentials) || is_null($user))
        {
            return  response()->json([
                'status' => 'Failed',
                "message" => "The provided credentials do not match in our records."
            ], 401);
        }

        // if(!is_null($request->user()))
        // {
        //     $request->user()->currentAccessToken()->delete();
        // }
               
        return response()->json([
            'token' => $user->createToken('api_token')->plainTextToken,
            'token_type' => 'Bearer',
            'data' => $user,
            'status' => 'OK',
            "message" => "You have successfully logged in!"
        ], 200);
       
    }

    /**
     * Logout user, revoke the token that was used to authenticate the current request
     * 
     */
    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'OK',
            "message" => "Logout successfully",
        ], 200);
    }

    /**
     * Logout user, revoke the token that was used to authenticate the current request
     * 
     * @return \Illuminate\Http\JsonResponse 
     */
    public function logoutAllDevices()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => 'OK',
            "message" => "Logout all devices successfully",
        ], 200);
    }
}
