<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');
        if(!$token) {
            return response()->json(['code'=>401, 'message'=>'Not Logged in'], 401);
        }
        $user = User::where('api_token', $token)->first();
        if(!$user) {
            return response()->json(['code'=>401, 'message'=>'Not Logged in'], 401);
        }
        return $next($request);
    }
}
