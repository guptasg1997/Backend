<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;//
use Laravel\Lumen\Routing\Controller as BaseController;
use \Firebase\JWT\JWT;
use App\Models\User;
use App\Exceptions\TokenNotFoundException;
use Exception;

class Controller extends BaseController
{

    

    protected function encode($request , $purpose)
    {
        $email = $request->email;
        $users = User::where('email', $email) -> first();
        $id = $users->id; 
        $timevar = time(); //
        $payload = ['email' => $email ,'id' => $id , 'purpose' => $purpose , 'timevar' => $timevar];

        $key = env('JWT_SECRET');
        return (JWT::encode($payload, $key));

    }

    protected function decode($request , $token , $purpose){
        //$token = $request->bearerToken;
        $key =env('JWT_SECRET');

        try{
            $payload = JWT::decode($token, $key, array('HS256'));
        }catch(Exception $e){
            throw new TokenNotFoundException("Token Expired");
        }
        //JWT::$leeway = 10; //
        $payload = JWT::decode($token, $key, array('HS256'));//try catch exception
        //throw new TokenNotFoundException("Token Expired");
        //exp time
        // if(time() - $payload->timevar  >= 300 ){
        //     throw new TokenNotFoundException("Token Expired");
        //     //return response()->json(['Token Expired'],500);
        // }
        if($payload->purpose != $purpose){
            throw new TokenNotFoundException("Invalid Token");
            //return response()->json(['Error'],500);
        }
        $email = $payload->email;
        $users = User::where('email', $email) -> first();

        return $users;

    }

}
