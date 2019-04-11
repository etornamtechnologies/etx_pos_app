<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Exception;
use App\User;

class UserController extends Controller
{
    public function getAuthUser(Request $request)
    {
        $result = [];
        try{
            $authUser = Auth::user()->with('roles')->first();
            $result["code"] = 0;
            $result["user"] = $authUser;
        } catch(Exception $e) {
            $result["code"] = 1;
            $result["message"] = "Could not fetch logged in user";
        }
        return response()->json($result);
    }

    public function index(Request $request)
    {
        $users = User::all();
        return response()->json(['code'=> 0, 'users'=> $users], 200);
    }

    public function getAuthUserInfo(Request $request) {
        $user = User::getAuthUser($request);
        if($user) {
            return response()->json(['code'=> 0, 'user'=> $user], 200);
        }
        return abort(401, 'not logged in');
    }


}
