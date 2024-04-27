<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $token = auth()->attempt($request->validated());
        if($token){
            return $this->responseWithToken($token, auth()->user());
        }
        else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid credentials'
            ], 401);
        }
    }

    public function register(RegistrationRequest $request)
    {
        $requestData = $request->validated();
        $avatarPath =$this->checkForAvatar($request);
        $requestData['avatar'] = $avatarPath;
        
        $user = User::create($requestData);

        if($user){
            $token = auth()->login($user);
            return $this->responseWithToken($token, $user);
        }
        else{
            response()->json([
                'status' => 'failed',
                'message' => 'An error occure while trying to create user'
            ], 500);
        }
    }

    // Check for avatar
    public function checkForAvatar($request)
    {
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            // Handle uploaded image
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }
        elseif ($request->filled('avatar_id')) {
            $avatarFileName = $request->avatar_id;
            // Check if the avatar file exists in the storage
            if (Storage::disk('public')->exists("avatars/$avatarFileName.png")) {
                $avatarPath = "avatars/$avatarFileName.png";
            } else {
                return response()->json(['error' => 'Avatar not found'], 404);
            }
        }

        return $avatarPath;
    }



    // Return JWT access token
    public function responseWithToken($token, $user)
    {
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'access_token' => $token,
            'type' => 'bearer'
        ]);
    }

    public function logout()
    {
        // get token
        $token = JWTAuth::getToken();

        // invalidate token
        $invalidate = JWTAuth::invalidate($token);

        if($invalidate) {
            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Successfully logged out',
                ],
                'data' => [],
            ]);
        }
    }
}
