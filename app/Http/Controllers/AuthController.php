<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('username', $request->username)->first();
        if(!$user) {
            return response()->json(['code'=>2,'message'=>'User not found']);
        }
        if(Hash::check($request->password, $user->password)) {
            $user->api_token = str_random(80);
            $user->save();
            return response()->json(['code'=>0, 'message'=>'successfull', 'user'=> $user]);
        }
        return response()->json(['code'=> 500, 'message'=> 'Server error']);
    }

    public function logout(Request $request)
    {
        $api_token = $request->header('Authorization');
        $user = User::where('api_token', $api_token)->first();
        if(!$user) {
            return response()->json(['code'=>2]);
        }
        $user->api_token = null;
        $user->save();
        return response()->json(['code'=> 0, 'message'=> 'user logged out successfuly']);
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name'=> 'required',
                'username'=> 'required|unique:users',
                'email'=> 'required|email|unique:users',
                'phone'=> 'unique:users'
            ]);
            $user = User::create([
                'username'=> $request->username,
                'name'=> $request->name,
                'email'=> $request->email,
                'phone'=> $request->phone,
                'password'=> Hash::make($request->password),
                'api_token'=> str_random(50)
            ]);
            return response()->json(['user'=> $user, 'code'=> 200]);
        } catch(Exception $e) {
            return response()->json(['code'=>500, 'message'=>'Server error']);
        }
    }
}
