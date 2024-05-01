<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\AppConfigurations;
use Hash;
use Twilio\Rest\Client;
use App\Mail\OtpMail;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name'=>'required|min:2|max:100',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6|max:100',
            'mobile_number'=> 'required',
            'device_id'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=>'Validations fails',
                'errors'=>$validator->errors()
            ],422);
        }

        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'mobile_number'=> $request->mobile_number,
            'device_id'=>$request->device_id
        ]);

       // sendOtp
       if ($user) {
        // Generate OTP
        $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // Store OTP and its expiration time in the user's record
        $user->update([
            'otp' => $otp,
            'otp_valid_until' => now()->addMinutes(5),
        ]);
        // $notificationPreference = AppConfigurations::where('config_key', 'notification_preference')->value('config_value');
        // Send OTP via email or sms
        //    if($notificationPreference === 'email'){
        //      Mail::to($user->email)->send(new OtpMail($otp));
        //    }else{
             // Use Twilio to send the OTP via SMS
                //     $account_sid = getenv('TWILIO_ACCOUNT_SID');
                //     $auth_token = getenv('TWILIO_AUTH_TOKEN');
                //     $twilio_number = getenv('TWILIO_PHONE_NUMBER');

                //     $client = new Client($account_sid, $auth_token);
                //     $client->messages->create(
                //         // Where to send a text message
                //         '+918144128737',
                //         array(
                //             'from' => $twilio_number,
                //             'body' => 'Your OTP is: ' . $otp,
                //         )
                //     );
        //    }

        return response()->json(['message' => 'Registration successfull. OTP sent to email and mobile number', 'otp'=>$otp]);
    }
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users',
            'otp' => 'required|string|size:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=>'Validation fails',
                'errors'=>$validator->errors()
            ],422);
        }
        $user=User::where('email',$request->email)->first();

        if ($user && $user->otp === $request->otp && now()->lt($user->otp_valid_until)) {
            $user->update(['email_verified_at' => now(), 'otp' => null, 'otp_valid_until' => null]);
            return response()->json(['message' => 'OTP verified successfully.'],200);
        }

        return response()->json(['message' => 'Invalid OTP or expired.'], 422);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'password'=>'required',
            'device_id'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=>'Validation fails',
                'errors'=>$validator->errors()
            ],422);
        }
        $user=User::where('email',$request->email)->first();
        if($user){
        // Check if the provided device_id matches the stored device_id
        $deviceMatch = $request->device_id === $user->device_id;
            if ($deviceMatch == false) {
                // Generate OTP
                $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
                // Store OTP and its expiration time in the user's record
                $user->update([
                    'otp' => $otp,
                    'otp_valid_until' => now()->addMinutes(5),
                    'device_id' =>$request->device_id
                ]);
                //$notificationPreference = AppConfigurations::where('config_key', 'notification_preference')->value('config_value');
                // Send OTP via email or SMS
                // if($notificationPreference === 'email'){
                //     Mail::to($user->email)->send(new OtpMail($otp));
                // }else{
                //       // Use Twilio to send the OTP via SMS
                //     $account_sid = getenv('TWILIO_ACCOUNT_SID');
                //     $auth_token = getenv('TWILIO_AUTH_TOKEN');
                //     $twilio_number = getenv('TWILIO_PHONE_NUMBER');

                //     $client = new Client($account_sid, $auth_token);
                //     $client->messages->create(
                //         // Where to send a text message
                //         '+918144128737',
                //         array(
                //             'from' => $twilio_number,
                //             'body' => 'Your OTP is: ' . $otp,
                //         )
                //     );
                //     //sendSms($user->phone,'Your One Time Password is: '. $otp);
                // }

                if(Hash::check($request->password,$user->password)){
                    $token=$user->createToken('auth-token')->plainTextToken;
                return response()->json([
                    'message' => 'Login successfull. OTP sent to email and mobile number',
                    'otp'=>$otp,                    
                    'token'=>$token,
                    'data'=>$user],200);
                }else{
                    return response()->json([
                        'message'=>'Incorrect credentials',
                    ],400); 
                }

            }

            if(Hash::check($request->password,$user->password)){
                $token=$user->createToken('auth-token')->plainTextToken;

                return response()->json([
                    'message'=>'Login Successfull',
                    'token'=>$token,
                    'data'=>$user
                ],200); 

            }else{
                return response()->json([
                    'message'=>'Incorrect credentials',
                ],400); 
            }
        }else{
            return response()->json([
                'message'=>'Incorrect credentials',
            ],400); 
        }
    }

    public function user(Request $request){
        return response()->json([
            'message'=>'User successfully fetched',
            'data'=>$request->user()
        ],200); 
    }

    public function logout(Request $request){

       $request->user()->currentAccessToken()->delete(); 
        return response()->json([
            'message'=>'User successfully logged out',
        ],200); 
    }

    public function sendOtp(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Generate OTP
            $otp = Str::random(6);

            // Store OTP and its expiration time in the user's record
            $user->update([
                'otp' => $otp,
                'otp_valid_until' => now()->addMinutes(5),
            ]);

            // $notificationPreference = AppConfigurations::where('config_key', 'notification_preference')->value('config_value');
            //Send OTP via email and mobile
            // if($notificationPreference === 'email'){
            //     Mail::to($user->email)->send(new OtpMail($otp));
            // }else{
            //     $user->notify((new OtpNotification($user->mobile_number,$otp)));
            // }
    
            return response()->json(['message' => 'OTP sent successfully', 'OTP' => $otp]);
        }

        return response()->json(['message' => 'User not found'], 404);
    }
   
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('otp_valid_until', '>', now())
            ->first();

        if ($user) {
            // Reset password and remove OTP details
            $user->update([
                'password' => Hash::make($request->password),
                'otp' => null,
                'otp_valid_until' => null,
            ]);

            return response()->json(['message' => 'Password reset successfully']);
        }

        return response()->json(['message' => 'Invalid OTP or expired'], 422);
    }
}