<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\SharedFunctions\ResponseBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function getProfileData(Request $request){
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|integer',
        ]);

        if($validation->fails()){
            return ResponseBuilder::buildResponse(null, 'Validation failed, '. $validation->errors(), 400);
        }

        $user = User::where('id', $request->user_id)->first();
        
        if(!$user){
            return ResponseBuilder::buildResponse(null, 'User not found', 404);
        }
       return ResponseBuilder::buildResponse($user, 'User retrieved successfully', 200);
    }
    public function store(Request $request){
        $validation = Validator::make($request->all(),[
            'name' => 'required|string',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if($validation->fails()){
            return ResponseBuilder::buildResponse(null, 'Validation failed, '. $validation->errors(), 400);
        }
        $profileImagePath = null;
        if($request->hasFile('profile_image')){
            $profileImagePath = $request->file('profile_image')->store('profile_pictures', 'public');
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
                'bio' => $request->bio,
                'profile_image' => $profileImagePath,
            ]);

            return ResponseBuilder::buildResponse($user, 'User created successfully', 201);
        } catch (\Exception $e) {
            // Delete the uploaded profile image if user creation fails
            if ($profileImagePath) {
                Storage::disk('public')->delete($profileImagePath);
            }

            return ResponseBuilder::buildResponse(null, 'Failed to create user, ' . $e->getMessage(), 500);
        }
    }
    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'sometimes|required|string',
            'username' => 'sometimes|required|string|unique:users,username,' . $id,
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validation->fails()) {
            return ResponseBuilder::buildResponse(null, 'Validation failed, ' . $validation->errors(), 400);
        }

        $user = User::find($id);
        if (!$user) {
            return ResponseBuilder::buildResponse(null, 'User not found', 404);
        }

        $profileImagePath = $user->profile_image;
        if ($request->hasFile('profile_image')) {
            // Delete the old profile image if it exists
            if ($profileImagePath) {
                Storage::disk('public')->delete($profileImagePath);
            }
            // Store the new profile image
            $profileImagePath = $request->file('profile_image')->store('profile_pictures', 'public');
        }

        try {
            $user->update([
                'name' => $request->get('name', $user->name),
                'username' => $request->get('username', $user->username),
                'email' => $request->get('email', $user->email),
                'password' => $request->get('password', $user->password),
                'bio' => $request->get('bio', $user->bio),
                'profile_image' => $profileImagePath,
            ]);

            return ResponseBuilder::buildResponse($user, 'User updated successfully', 200);
        } catch (\Exception $e) {
            return ResponseBuilder::buildResponse(null, 'Failed to update user, ' . $e->getMessage(), 500);
        }
    }
    public function getAllActiveUser(){
        //Get active users that are not disabled
        $users = User::whereNull('deleted_at')->get();
        return ResponseBuilder::buildResponse($users, 'Active users retrieved successfully', 200);
    }
    public function getAllDisabledUsers(){
        //Get active users that are not disabled
        $users = User::whereNotNull('deleted_at')->get();
        return ResponseBuilder::buildResponse($users, 'Disabled users retrieved successfully', 200);
    }
}
