<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Exception;
use App\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('api_auth');
        $this->middleware('api_role:supervisor,manager,admin')->except(['index','show']);
        $this->middleware('api_role:admin')->only(['store', 'update']);
    }

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

    public function store(Request $request)
    {
        $request->validate([
            'name'=> 'required',
            'username'=> 'required|unique:users',
            'password'=> 'required',
            'phone'=> 'required'
        ]);
        $user = User::create([
            'name'=> $request->get('name'),
            'username'=> $request->get('username'),
            'phone'=> $request->get('phone'),
            'email'=> $request->get('email'),
            'password'=> bcrypt($request->get('password'))
        ]);
        return response()->json(['code'=> 0, 'message'=> 'User created successfully', 'user'=> $user], 200);
    }

    public function update(Request $request, $id)
    {
        //dd($request->all());
        $request->validate([
            'name'=> 'required',
            'username'=> 'required|unique:users,username,'.$id.'id',
            'phone'=> 'required'
        ]);
        $user = User::where('id', $id)->update([
            'name'=> $request->get('name'),
            'username'=> $request->get('username'),
            'phone'=> $request->get('phone'),
            'email'=> $request->get('email'),
        ]);
        return response()->json(['code'=> 0, 'message'=> 'User updated successfully', 'users'=> User::all()], 200);
    }

}

