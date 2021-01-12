<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\Mail;
use Auth;
use App\Providers\AuthServiceProvider;

class ExampleController extends Controller
{

    public function __construct()
    {
        //
    }
    
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:30',
            'email' => 'required|email|max:50|',
            'password' => 'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/',
            'retypepassword' => 'required|same:password',//password_confirmation
        ]);
        // if($request->password != $request->retypepassword){
        //     return response()->json(['Password Mismatch'],422); // add error code
        // }

        $temp = USER::where('email' , $request->email) -> first();
        if($temp !== null){
            if($temp->verify === 1){
                return response()->json(['Account already exists'],422);
            }
            else{
                $temp->delete();
            }
        }

        $users = new User;
        $users->name = $request->name;
        $users->email = $request->email;
        $users->password = HASH::make($request->password);
        $users->verify = 0;

        $users-> save();

        $this->verify_request($request);

        return response()->json(['Account Created'],201);

    }

    public function create_user(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|max:30',
            'email' => 'required|email|max:50|unique:users',
            'password' => 'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/',
            'retypepassword' => 'required|same:password',//password_confirmation
        ]);
        // if($request->password != $request->retypepassword){
        //     return response()->json(['Password Mismatch'],422); // add error code
        // }

        // $token = $request->bearerToken();
        // $purpose = 'login';

        // try{
        //     $temp = $this->decode($request ,$token , $purpose);
        // }
        // catch(TokenNotFoundException $e ){
        //     return response()->json(['Invalid Token'],500);
        // }

        // if($temp->role != 'admin'){
        //     return response()->json(['error'],500);
        // }

        $users = $request->users;

        $users = new User;
        $users->name = $request->name;
        $users->email = $request->email;
        $users->password = HASH::make($request->password);
        $users->verify = 1;

        $users-> save();

        Mail::raw("Account has been created with email : $request->email and password : $request->password"
        , function ($message) {
            $message->to('viratkohlisg@gmail.com')
              ->subject('Account Creation Mail');
          });
          if (Mail::failures()) {
            return response()->json(["Mail can't be sent "],417);
            } 

        //$this->verify_request($request);

        return response()->json(['Account Created'],201);

    }



    public function login(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|exists:users',
            'password' => 'required',
        ]);

        $email = $request->email;
        $password = $request->password;

        $users = User::where('email', $email) -> first();
        
        $verify = $users->verify;
        if($verify === 0){
             return response()->json(['Please verify your email first'],401) ;
         }

        if(HASH::check($password , $users->password)){  
            $purpose = 'login';
            $token = $this->encode($request , $purpose);
            return response()->json(['token' => $token],202); // 
        }
        else{
            return response()->json(['Incorrect Password'], 400);
        }

    }

    public function test(Request $request)
    {
        // $token = $request->bearerToken();
        
        // $purpose = 'login';

        $users = $request->users;

        // try{
        //     $users = $this->decode($request ,$token, $purpose);
        // }
        // catch(TokenNotFoundException $e ){
        //     return response()->json(['Invalid Token'],500);
        // }

        return response()->json($users,202);


    }

    public function alluser(Request $request) 
    {
        //$token = $request->bearerToken();
        $que = $request->que;
        $check = $request->check;
        $users = $request->users;
        $noOfUsers = (int)$request->noOfUsers;
        //$noOfUsers = 
        // echo "Backend";
        // echo $que;
       // $purpose = 'login';
        //echo "$purpose";
        // try{
        //     $users = $this->decode($request ,$token , $purpose);
        // }
        // catch(TokenNotFoundException $e ){
        //     return response()->json(['Invalid Token'],500);
        // }
        $users = $request->users;

        if($users->role !== 'admin'){
            $check = false;
        }

        //if($users->role == 'admin')
        {   
            if($check == false)
            {
                $display = User::where('name' , '<>' , 'ADMIN')
                ->where('verify' , 1)
                -> where( function($q) use($que){
                    $q->where('email' ,'like', '%'. $que .'%')
                    ->orWhere('name' ,'like', '%'. $que .'%')
                    ->orWhere('id' ,'=',$que);
                })
                ->paginate($noOfUsers);
            }
            else{
                $display = User::where('name' , '<>' , 'ADMIN')
                -> where('email' ,'like', '%'. $que .'%') 
                -> orwhere('name' ,'like', '%'. $que .'%')
                -> orwhere('id' ,'=',$que)
                -> paginate($noOfUsers);
                
            }

            return response()->json($display,200);  //
            //}
        } 
        // else {
        //     return response()->json(['You are not the ADMIN'], 401); 
        // }
    }

     public function destroy(Request $request)
     {
        // $purpose = 'login';
        $this->validate($request, [
            'id'    => 'required|exists:users',
        ]);

        $id = $request->id;
        // $token = $request->bearerToken();
        // //echo "$id";
        // //echo "destroy called on server side";
        // //echo "$token";
        // try{
        //     $users = $this->decode($request , $token, $purpose);
        // }
        // catch(TokenNotFoundException $e ){
        //     return response()->json(['Invalid Token'],500);
        // }
        //echo("$id");
        $users = $request->users;

        if($users->role !== 'admin'){
            return resposne()->json(['Unauthorised'],403);
        }

        $users = $request->users;

        //if($users->role == 'admin'){ 
            // if($id === null){
            //     return response()->json(["please provide id to be deleted"] , 400);
            // }

            $temp = User::find($id);
            //$temp = User::where('id', $id) -> first();
            $temp->delete();   ////////handle error
            return response()->json(['deleted'],202);
        //}
        // $users->delete();   
        // return response()->json(['deleted'],202);
     }


     public function update(Request $request)
     {
        $this->validate($request, [
            'password' => 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/',
        ]);

        // $token = $request->bearerToken();
        // $purpose = 'login';

        // try{
        //     $users = $this->decode($request , $token , $purpose);
        // }
        // catch(TokenNotFoundException $e ){
        //     return response()->json(['Invalid Token'],500);
        // }

        // if($users->role == 'admin'){
        //     $this->validate($request, [
        //         'id' => 'required|int',
        //         'verify' => 'int',
        //     ]);
        //     $id = $request->id;
        //     $temp = User::where('id', $id) -> first();
        //     $name = $request->input('name');
        //     $email = $request->input('email');
        //     $password = $request->input('password');
        //     $verify = $request->input('verify');
        //     $role = $request->input('role');
        //     if($name != ""){
        //         $temp->name= $name;
        //     }
        //     if($email != ""){
        //         $temp->email= $email;
        //         $temp->verify = 1;
        //     }
        //     if($verify != ""){
        //         $temp->verify = $verify;
        //     }
        //     if($password != null){
        //         $temp->password= HASH::make($password);
        //     }
        //     if($role != null){
        //         $temp->role = $role;
        //     }
        //     $temp->save();
        //     return response()->json(['updated'], 201);
        // }
        
        $users = $request->users;



        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $password_confirmation = $request->input('retypepassword');
        ////check password two times
        if($name != ""){   // === equator
            $users->name= $name;
        }
        if($email != ""){
            $users->email= $email;
            $users->verify = 0;
            //$this->verify_request($request);
        }
        if($password != null){
            $users->password= HASH::make($password);
        }

        $users->save();
        
        return response()->json(['Updated'], 201);
     }


    public function verify_request(Request $request){

        $this->validate($request, [
            'email' => 'required|email|max:50',
        ]);
        $email = $request->email;
        $users = User::where('email', $email) -> first();
        if($users->verify == 1){
            return response()->json(["Email already verfied"],400);
        }
        $purpose = 'emailverify';
        $jwt = $this->encode($request , $purpose);
        $temp = "localhost:3000/verify?token=";
        $link =$temp.$jwt;
        
        Mail::raw('Please verify your identity by clicking on the below link. '.$link
        , function ($message) use($email) {
            $message->to('viratkohlisg@gmail.com')
              ->subject('Verification Mail');
          });
          if (Mail::failures()) {
            return response()->json(["Verification mail can't be sent "]);
            }   
         
        return response()->json(["Mail sent"],202);

        //return response()->json([$link],202);


    }

    public function verify(Request $request){

        $this->validate($request, [
            'token' => 'required|string',
        ]);
        $token = $request->token;
        $purpose = 'emailverify';

        try{
        $users = $this->decode($request , $token , $purpose);
        }
        catch(TokenNotFoundException $e ){
            return response()->json(['Invalid Token'],401);
        }
        
        $users->verify = 1;
        $users->save();

        return response()->json(['Account Verified'],201);
        //echo "verified";//201  
        
    }

    public function forgotpassword(Request $request){
        $purpose = 'passchange';
        $this->validate($request, [
            'email' => 'required|email|exists:users',
        ]);
        $email = $request->email;
        $jwt = $this->encode($request , $purpose);
        $temp = "http://localhost:3000/changepassword?token=";
        $link =$temp.$jwt;

        Mail::raw("Change password by clicking on following link \n $link "
        , function ($message) use($email) {
            $message->to('ishankishansg@gmail.com')
              ->subject('Verification Mail');
          });
          if (Mail::failures()) {
            return response()->json(["Verification mail can't be sent "]);
          } 

          return response()->json(['Link sent'],202);

    }

    public function changepassword(Request $request){
        $this->validate($request, [
            //'token' => 'required|string',
            'password' => 'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/',
            'retypepassword' => 'required|same:password|string',
        ]);
        $token = $request->bearerToken();

        // if($request->password != $request->retypepassword){
        //     return response()->json(['Password Mismatch'],401); // add error code
        // }
        
        //$token = $request->token;
        $purpose = 'passchange';
        $password = $request->password;

        try{
            $users = $this->decode($request , $token , $purpose);
        }
        catch(TokenNotFoundException $e ){
            return response()->json(['Invalid Token'],401);
        }
        //$users  = $this->decode($request , $purpose);

        $users->password= HASH::make($password);

        $users->save();

        return response()->json(['Password Changed Successfully'],201);

    }
    ////  admin can create user
    ///   

    public function testing(Request $request){

        $this->validate($request, [
            'email' => 'required|email',
        ]);
        $email = $request->email;
        $token = AUTH::attempt($request->only('email'));
        echo $token;

    }

}
