<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Mail\WelcomeUser;
use App\Mail\VerifyEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\ResetPasswordRequest;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
            // The user will be allowed to the platform before verifying his/her email address but will only be able to update his/her profile...
            $user->sendEmailVerificationNotification();
            return response()->json(['message' => 'User registered successfully.', 'user' => $user, 'token' => $token ], 201);
        }

    }
    public function redirect($client){
        // Validate client (provider)
        $validClients = ['google', 'facebook', 'apple'];
        if (!in_array($client, $validClients)) {
            return response()->json(['error' => 'Invalid client specified.'], 400);
        }
        return Socialite::driver($client)->stateless()->redirect();
    }

    public function handleCallback($client){
        try {
            $validClients = ['google', 'facebook', 'apple'];
            if (!in_array($client, $validClients)) {
                return response()->json(['error' => 'Invalid client specified.'], 400);
            }
            $clientUser = Socialite::driver($client)->stateless()->user();
            $clientId = "{$client}_id";
            $user = User::where($clientId, $clientUser->getId())->first();

            if (!$user) {
                $user = User::where('email', $clientUser->getEmail())->first();
                if ($user) {
                    $user->update([$clientId => $clientUser->getId()]);
                    if (empty($user->name)) {
                        $user->name = $clientUser->getName();
                        $user->save(); // Save changes to name if applicable
                    }
                } else {
                     $user = User::create([
                        'name' => $clientUser->getName(),
                        'email' => $clientUser->getEmail(),
                        $clientId => $clientUser->getId()
                    ]);
                    Mail::to($user->email)->send(new WelcomeUser($user));
                }
            }
            $token = $user->createToken('Initial Token')->plainTextToken;
            $message = $user->wasRecentlyCreated ? 'User registered successfully.' : 'Logged in successfully.';

            return redirect()->away('http://localhost:3000/profile?token=' . urlencode($token) . '&message=' . urlencode($message));

        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to authenticate.'], 500);
        }
    }

    public function verify(Request $request, $id, $hash){
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        Mail::to($user->email)->send(new WelcomeUser($user));

        return response()->json(['message' => 'Email verified successfully.']);
    }

    public function resend(Request $request){
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email sent.']);
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

    public function resetPassword(ResetPasswordRequest $request, $token, $email){
        if (!$token || !$email) {
            return response()->json(['error' => 'Token and email are required.'], 400);
        }

        $status = Password::reset(
            [
                'email' => $email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $token,
            ],
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