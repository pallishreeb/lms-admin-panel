<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
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
}