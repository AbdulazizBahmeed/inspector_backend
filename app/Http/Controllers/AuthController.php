<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'fullname' => 'required|string',
            'national_id' => 'required|string|size:10|unique:users,national_id',
            'password' => 'required|string|min:8',
            'personal_photo' => 'required|string'
        ]);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors(),
            ], 400);
        } else {
            $user = User::create([
                'fullname' => $request->fullname,
                'national_id' => $request->national_id,
                'password' => Hash::make($request->password),
                'personal_photo' => $request->personal_photo,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'user logged in Successfully',
                'token' => substr($user->createToken("API TOKEN")->plainTextToken, 2)
            ], 200);
        }
    }
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'national_id' => 'required|string|size:10',
            'password' => 'required|string'
        ]);
        if ($validation->fails() | !Auth::attempt($request->only(['national_id', 'password']))) {
            return response()->json([
                'status' => false,
                'message' => 'the user name and password does not match our records',
            ], 401);
        } else {
            return response()->json([
                'status' => true,
                'message' => 'user logged in Successfully',
                'token' => substr(auth()->user()->createToken("API TOKEN")->plainTextToken, 2)
            ], 200);
        }
    }

    public function logout()
    {
        if (auth()->user()) {
            auth()->user()->tokens()->delete();
        }
        return response()->json([
            'status' => true,
            'message' => 'user logged out successfully',
        ], 200);
    }
    public function unauthenticated()
    {
        return response()->json([
            'status' => false,
            'message' => 'the user is unauthenticated',
        ], 401);
    }
}
