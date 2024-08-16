<?php

namespace App\Http\Controllers;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function authenticate(RegisterUserRequest $request){

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if (Hash::check($request->password,$user->password)) {
                $token = $user->createToken('Initial Token')->plainTextToken;
                return response()->json(['message' => 'Login successful', 'user' => $user, 'token' => $token]);
            } else {
                return response()->json(['message' => 'Wrong password'], 401);
            }
        } else {
            $validatedData = $request->validated();
            $user = User::create($validatedData);
            $token = $user->createToken('Initial Token')->plainTextToken;
            return response()->json(['message' => 'User registered successfully.', 'user' => $user, 'token' => $token ], 201);
        }

    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function forgetPassword(Request $request){
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if($user){
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json(['message' => __($status)], 200);
            } else {
                return response()->json(['error' => __($status)], 400);
            }
        }else{
            return response()->json(['message' => 'This User does not exist'], 401);
        }

    }

    public function updatePassword(ResetPasswordRequest $request){

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)], 200);
        } else {
            return response()->json(['error' => __($status)], 400);
        }
    }

}