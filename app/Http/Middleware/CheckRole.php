<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next, ...$myroles)
    {
        $token = $request->header('Authorization');
        $user = User::where('api_token', $token)->first();
        if(!$user) {
            return abort(401, 'User not logged in');
        }
        $roles = isset($myroles) ? $myroles : null;
        if($user && $user->hasAnyRole($roles) || !$roles) {
            return $next($request);
        }
        return abort(403, 'insufficient permission');
        //return response()->json(['message'=> 'insufficeient permission', 'code'=> 2], 401);   
        //return redirect()->back();    
    }
}
