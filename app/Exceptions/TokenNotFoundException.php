<?php

namespace App\Exceptions;
use Exception;

class TokenNotFoundException extends Exception{
    public function report(){

    }

    public function render(){
        return "Invalid token";

    }
}