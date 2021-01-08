<?php

namespace App\Http\Middleware;

use Closure;
//use Illuminate\Contracts\Auth\Factory as Temp;

class Testing
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
        // $name = $request->name;
        // $email = $request->email;
        $password = $request->password;

        if(strlen($password) < 5 ){
            echo "short password";
            return redirect('signup');
        }
        return $next($request);



    }
}
