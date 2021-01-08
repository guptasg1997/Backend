<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
class EmailController extends Controller
{
  public function sendEmail()
  {
    Mail::raw("This is the email body. $email ", function ($message) use($email){
      $message->to($email)
        ->subject('Lumen email test');
    });
    if (Mail::failures()) {
      return 'Sorry! Please try again latter :(';
    } else {
      return 'Great! eMail successfully sent ;)';
    }
  }
}