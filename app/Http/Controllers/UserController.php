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

    public function index()
    {
        $result = [];
        try {
            $users = User::all();
            $result['code'] = 0;
            $result['users'] = $users;
        } catch(Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Server error";
        }
        return response()->json($result);
    }
}
