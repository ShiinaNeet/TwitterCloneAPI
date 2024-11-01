<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\SharedFunctions\ResponseBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return $this->userRedirect($user,$token);
    }
    public function userRedirect($user, $token){
        if($user->role == User::ROLE_ADMIN){
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged in',
                'redirect' => '/admin/dashboard',
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]        
            ], 200);
        }
        else if($user->role == User::ROLE_MODERATOR){
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged in',
                'redirect' => '/moderator/dashboard',
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]  
            ], 200);
        }
        else{
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged in',
                'redirect' => '/user/newsfeed',
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]  
            ], 200);
        }
    }
    public function registerPublicUser(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::firstOrCreate(
            [
                'email' => $request->email,
                'username' => $request->username,
            ],
            [
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'role' => User::ROLE_USER,
            ]
        );

        if (!$user->wasRecentlyCreated) {
            return response()->json([
                'status' => 'failed',
                'message' => 'There is an existing user with this email and username.',
            ], 400);
        }

        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return $this->userRedirect($user, $token);
    }
    public function createUserByAdmin(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required'
        ]);

        switch ($request->role) {
            case 'admin':
                $role = User::ROLE_ADMIN;
                break;
            case 'moderator':
                $role = User::ROLE_MODERATOR;
                break;
            default:
                $role = User::ROLE_USER;
                break;
        }

        $user = User::firstOrCreate(
            [
                'email' => $request->email,
                'username' => $request->username,
            ],
            [
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'role' => $role,
            ]
        );

        if (!$user->wasRecentlyCreated) {
            return response()->json([
                'status' => 'failed',
                'message' => 'There is an existing user with this email and username.',
            ], 400);
        }

        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return $this->userRedirect($user, $token);
    }
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }
    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
    public function updateUserInformation(Request $request){

        $request->validate([
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $request->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->id,
            'password' => 'required|string',
            'bio' => 'string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = User::find($request->id);

        if (!$user) {
            return ResponseBuilder::buildResponse(null, 'User not found', 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return ResponseBuilder::buildResponse(null, 'Password is incorrect', 401);
        }

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->bio = $request->bio;

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                $oldImagePath = storage_path('app/public/' . $user->profile_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('profile_image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $imagePath = Storage::putFileAs('public/images', $image, $imageName);
            $user->profile_image = 'images/' . $imageName;
        }
        
        if ($user->save()) {
            return ResponseBuilder::buildResponse($user, 'User updated successfully', 200);
        } else {
            return ResponseBuilder::buildResponse(null, 'Failed to update user', 500);
        }
    }
    public function updateUserInformationByAdmin(Request $request){

        $request->validate([
            'id' => 'required|exists:users,id',
            'role' => 'required'
        ]);

        $user = User::find($request->id);

        if (!$user) {
            return ResponseBuilder::buildResponse(null, 'User not found', 404);
        }


        $user->role = $request->role;

        if ($user->save()) {
            return ResponseBuilder::buildResponse($user, 'User updated successfully', 200);
        } else {
            return ResponseBuilder::buildResponse(null, 'Failed to update user', 500);
        }
    }

    public function deleteUserAccountByUser(Request $request){
        $request->validate([
            'id' => 'required|exists:users,id',
            'password' => 'required|string',
        ]);

        $user = User::find($request->id);

        if (!$user) {
            return ResponseBuilder::buildResponse(null, 'User not found', 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return ResponseBuilder::buildResponse(null, 'Password is incorrect', 401);
        }

        if ($user->profile_image) {
            $oldImagePath = storage_path('app/public/' . $user->profile_image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        if ($user->delete()) {
            return ResponseBuilder::buildResponse(null, 'User deleted successfully', 200);
        } else {
            return ResponseBuilder::buildResponse(null, 'Failed to delete user', 500);
        }
    }
}
