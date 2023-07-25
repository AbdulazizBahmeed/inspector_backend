<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        try
        {
            $is_data_valid = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string|'
            ]);
            if ($is_data_valid->fails() | !Auth::attempt($request->only(['email', 'password'])))
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            } else
            {
                return response()->json([
                    'status' => true,
                    'message' => 'User Logged In Successfully',
                    'token' => auth()->user()->createToken("API TOKEN")->plainTextToken
                ], 401);
            }
    
            
        } catch(Exception $erorr)
        {
            Log::error($erorr);
            return response()->json([
                'status' => false,
                'message' => 'some error happened in the server please try again later',
            ], 501);
        }
    }

    public function logout()
    {
        
        
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Tokens Revoked'
        ];
    }
}