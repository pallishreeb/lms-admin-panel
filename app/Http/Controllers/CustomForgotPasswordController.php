<?php 
  
namespace App\Http\Controllers; 

use Illuminate\Http\Request; 
use DB; 
use Carbon\Carbon; 
use App\Models\User; 
use Mail; 
use Hash;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
class CustomForgotPasswordController extends Controller
{
      /**
       * Write code on Method
       *
       * @return response()
       */
       // Override the showLinkRequestForm method
       public function showForgotPasswordForm()
       {
           return view('auth.email');
       }
      /**
       * Write code on Method
       *
       * @return response()
       */
      public function sendResetLinkEmail(Request $request)
      {
          $request->validate([
              'email' => 'required|email|exists:users',
          ]);
  
          $token = Str::random(64);

            try {
                DB::table('password_reset_tokens')->insert([
                    'email' => $request->email, 
                    'token' => $token, 
                    'created_at' => Carbon::now()
                ]);
            } catch (QueryException $e) {
                if ($e->errorInfo[1] == 1062) { // Check if the error code corresponds to a duplicate entry
                    return redirect()->route('password.request')->with('error', 'Password reset tokens still exist in your email.');
                } else {
                    // Handle other query exceptions if needed
                    return redirect()->route('password.request')->with('error', 'An error occurred while processing your request.');
                }
            }
          Mail::mailer('ses')->send('auth.forgotPassword', ['token' => $token], function($message) use($request){
              $message->from('support@sohojpora.com', 'Sahoj Pora');
              $message->to($request->email);
              $message->subject('Reset Password');
          });
  
          return back()->with('message', 'We have e-mailed your password reset link!');
      }
      /**
       * Write code on Method
       *
       * @return response()
       */
      public function showResetForm($token) { 
         return view('auth.resetPassword', ['token' => $token]);
      }
  
      /**
       * Write code on Method
       *
       * @return response()
       */
      public function reset(Request $request)
      {
          $request->validate([
              'email' => 'required|email|exists:users',
              'password' => 'required|string|min:6|confirmed',
              'password_confirmation' => 'required'
          ]);
  
          $updatePassword = DB::table('password_reset_tokens')
                              ->where([
                                'email' => $request->email, 
                                'token' => $request->token
                              ])
                              ->first();
  
          if(!$updatePassword){
              return back()->withInput()->with('error', 'Invalid token!');
          }
  
          $user = User::where('email', $request->email)
                      ->update(['password' => Hash::make($request->password)]);
 
          DB::table('password_reset_tokens')->where(['email'=> $request->email])->delete();
  
          return redirect('/login')->with('message', 'Your password has been changed!');
      }
}