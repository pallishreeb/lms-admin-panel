<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Hash;
use File;

class ProfileController extends Controller
{
    public function change_password(Request $request){
        $validator = Validator::make($request->all(), [
            'old_password'=>'required',
            'password'=>'required|min:6|max:100',
            'confirm_password'=>'required|same:password'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'=>'Validations fails',
                'errors'=>$validator->errors()
            ],422);
        }

        $user=$request->user();
        if(Hash::check($request->old_password,$user->password)){
            $user->update([
                'password'=>Hash::make($request->password)
            ]);
            return response()->json([
                'message'=>'Password successfully updated',
            ],200);
        }else{
            return response()->json([
                'message'=>'Old password does not matched',
            ],400);
        }

    }

    public function update_profile(Request $request){
        $validator = Validator::make($request->all(), [
            'name'=>'nullable|min:2|max:100',
            'mobile_number'=>'nullable|max:100',
            'address'=>'nullable|max:100',
            'password'=>'nullable|min:6|max:100',
            'profile_image' => 'nullable|image|max:20480' // Max file size: 2MB
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'=>'Validations fails',
                'errors'=>$validator->errors()
            ],422);
        } 

        //$user=$request->user();
        // $user->update([
        //     'name'=>$request->name,
        //     'mobile_number'=>$request->mobile_number,
        //     'address'=>$request->address
        // ]);
        // Update profile fields
        $user=$request->user();
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
        if ($request->has('mobile_number')) {
            $user->mobile_number = $request->mobile_number;
        }
        
        if ($request->has('address')) {
            $user->address = $request->address;
        }
        
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image');
            $imageName = 'user_profile_pics/' . $profileImage->getClientOriginalName();
            // Set the profile image URL in the user model
            Storage::disk('s3')->put($imageName, file_get_contents($profileImage));
            $user->profile_image = Storage::disk('s3')->url($imageName);
        }

        $user->save();

        return response()->json([
            'message'=>'Profile successfully updated',
        ],200);

    }

    public function delete_account(Request $request)
    {
        // Check if the token is provided
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                'message' => 'Please provide a token',
            ], 400);
        }

        // Attempt to find the token in the database
        $tokenData = PersonalAccessToken::findToken($token);
        
        // If the token is invalid, return an error
        if (!$tokenData) {
            return response()->json([
                'message' => 'Invalid token',
            ], 401);
        }

        // Get the user associated with the token
        $user = User::find($tokenData->tokenable_id);

        // Check if the user is found
        if (!$user) {
            return response()->json([
                'message' => 'No user found',
            ], 404);
        }

        // Delete the profile image from S3 if it exists
        if ($user->profile_image) {
            $imagePath = parse_url($user->profile_image, PHP_URL_PATH);
            Storage::disk('s3')->delete($imagePath);
        }

        // Delete the user account
        $user->delete();

        // Revoke the token so it can't be used again
        $tokenData->delete();

        return response()->json([
            'message' => 'Account successfully deleted',
        ], 200);
    }
    public function delete_account_user($userId) {
        // Find the user by ID
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
    
        // Delete the profile image from S3 if it exists
        if ($user->profile_image) {
            $imagePath = parse_url($user->profile_image, PHP_URL_PATH);
            Storage::disk('s3')->delete($imagePath);
        }
    
        // Delete the user account
        $user->delete();
    
        return response()->json([
            'message' => 'Account successfully deleted',
        ], 200);
    }
    
}