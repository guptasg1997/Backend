<?php

namespace App\Http\Middleware;

use Closure;
use \Firebase\JWT\JWT;
use App\Models\User;
use Exception;

class LoginMiddleware
{

    public function handle($request, Closure $next , $guard = null)
    {

        $token = $request->bearerToken();

        $purpose = 'login';
        $key =env('JWT_SECRET');

        try{  
            $payload = JWT::decode($token, $key, array('HS256'));
        }catch(Exception $e){
            return response()->json(["invalid token"]);
            echo "error";
        }

        if(time() - $payload->timevar  >= 30000){
            return response()->json(['Expired Token'],408); // request timeout
        }

        if($payload->purpose != 'login'){
            return response()->json(['Unauthorized'],401);
        }
        
        $email = $payload->email;
        $users = User::where('email' , $email)->first();

        $request->users = $users;

        return $next($request);
    }
}
