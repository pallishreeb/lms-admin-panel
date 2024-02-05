<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Twilio\Rest\Client;
class AuthController extends Controller
{
// Show Register/Create Form
public function create() {
    return view('auth.register');
}

// Create New User
public function store(Request $request) {
    $formFields = $request->validate([
        'name' => ['required', 'min:3'],
        'email' => ['required', 'email', Rule::unique('users', 'email')],
        'mobile_number'=> ['required'],
        'password' => 'required|confirmed|min:6'
    ]);

    // Hash Password
    $formFields['password'] = bcrypt($formFields['password']);
    try {
    // Create User
    $user = User::create($formFields);
    }catch (QueryException $e) {
        if ($e->errorInfo[1] == 1062) { // Check if the error code corresponds to a duplicate entry
            return redirect()->route('auth.register')->with('error', 'Email already exists.');
        } else {
            // Handle other query exceptions if needed
            return redirect()->route('auth.register')->with('error', 'An error occurred while processing your request.');
        }
    }
    // Login
    auth()->login($user);

    return redirect('/admin/dashboard')->with('message', 'User created and logged in');
}

// Logout User
public function logout(Request $request) {
    auth()->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login')->with('message', 'You have been logged out!');

}

// Show Login Form
public function login() {
    return view('auth.login');
}

// Authenticate User
// public function authenticate(Request $request) {
//     $formFields = $request->validate([
//         'email' => ['required', 'email'],
//         'password' => 'required',
//         'device_id'=>'required'
//     ]);
//     // dd($request);
//     $user = User::where('email', $request->email)->first();
//     if($user){
//          // Check if the provided device_id matches the stored device_id
//          $deviceMatch = $request->device_id === $user->device_id;
//         if ($deviceMatch == false) {
//             // Generate OTP
//             $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    
//             // Store OTP and its expiration time in the user's record
//             $user->update([
//                 'otp' => $otp,
//                 'otp_valid_until' => now()->addMinutes(2),
//                 'device_id' =>$request->device_id
//             ]);
    
//             // Send OTP via email
//             Mail::to($user->email)->send(new OtpMail($otp));
//             // Store email in the session
//             $request->session()->put('otp_email', $user->email);
//             // Use Twilio to send the OTP via SMS
//             // $account_sid = getenv('TWILIO_ACCOUNT_SID');
//             // $auth_token = getenv('TWILIO_AUTH_TOKEN');
//             // $twilio_number = getenv('TWILIO_PHONE_NUMBER');
    
//             // $client = new Client($account_sid, $auth_token);
//             // $client->messages->create(
//             //     // Where to send a text message
//             //     '+918144128737',
//             //     array(
//             //         'from' => $twilio_number,
//             //         'body' => 'Your OTP is: ' . $otp,
//             //     )
//             // );
//             if(auth()->attempt($formFields)) {
//                 $request->session()->regenerate();
//                 return redirect()->route('verify.otp.form');
//             }
//         }
//         if(auth()->attempt($formFields)) {
//             $request->session()->regenerate();
//             return redirect('/admin/dashboard')->with('message', 'You are now logged in!'); 
//         }
//     }else{
//         return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
//     }
  
// }

public function authenticate(Request $request) {
    $formFields = $request->validate([
        'email' => ['required', 'email'],
        'password' => 'required'
    ]);

    if(auth()->attempt($formFields)) {
        $request->session()->regenerate();

        return redirect('/')->with('message', 'You are now logged in!');
    }

    return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
}
//verify otp form
public function showOtpVerificationForm(Request $request)
{

    $email = $request->session()->get('otp_email');

    return view('auth.verify-otp', compact('email'));
}
//verify otp
public function verifyOtp(Request $request)
{
     $request->validate([
        'email' => 'required|email',
        'otp' => 'required',
    ]);

    $user=User::where('email',$request->email)->first();

    if ($user && $user->otp === $request->otp && now()->lt($user->otp_valid_until)) {
    $user->update(['email_verified_at' => now(), 'otp' => null, 'otp_valid_until' => null]);
        return redirect('/admin/dashboard')->with('message', 'You are now logged in!');    
    }else {
        return back()->with('error', 'Invalid OTP. Please try again.');
    }
}

public function index(Request $request)
{
    $query = $request->input('q');
    
    $users = User::when($query, function ($queryBuilder) use ($query) {
        return $queryBuilder->where('name', 'like', '%' . $query . '%')
                            ->orWhere('email', 'like', '%' . $query . '%');
        // Add more fields as needed
    }, function ($queryBuilder) {
        // If no query, return all users
        return $queryBuilder;
    })->get();

    return view('users.index', ['users' => $users]);
}
 public function edit(User $user)
{
  // Fetch the user roles from your role management system
  $roles = Role::all(); // Assuming Role is a model for managing roles

  return view('users.edit', compact('user', 'roles'));
}

public function update(Request $request, $id)
{
// Update user record
  $user = User::find($id);
  $user->update([
      'role' => $request->input('role'),
      // Add other fields to update if needed
  ]);

  return redirect()->route('users.edit', $user->id)->with('success', 'User updated successfully!');
}
public function deleteConfirmation(User $user)
{
  return view('users.delete-confirmation', [
      'item' => $user,
      'type' => 'user',
      'route' => route('users.destroy', $user->id),
      'backRoute' => route('admin.users.index'),
  ]);
}
public function destroy(User $user)
{
  $user->delete();

  return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
}
}