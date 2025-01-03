<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\WelcomeUser;
use App\Mail\VerifyEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\kyc_validation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\authenticateProvider;
use App\Http\Requests\kycValidationRequest;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthenticationController extends Controller
{
    public function authenticate(RegisterUserRequest $request){
        try {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken('Initial Token')->plainTextToken;
                    return response()->json(['message' => 'Login successful', 'user' => $user, 'token' => $token, 'isNewUser' => false]);
                } else {
                    return response()->json(['message' => 'Wrong password'], 401);
                }
            } else {
                $password = $request->password;
                $validatedData = $request->validated();
                $user = User::create($validatedData);
                $token = $user->createToken('Initial Token')->plainTextToken;
                // Mail::to($user->email)->send(new WelcomeUser($user, $password));
                // $user->sendEmailVerificationNotification();
                return response()->json(['message' => 'User registered successfully.', 'user' => $user, 'token' => $token, 'isNewUser' => true], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'There was an error during authentication.', 'details' => $e->getMessage()], 500);
        }
    }

    public function authenticateUser(RegisterUserRequest $request){
        try {
        $user = User::where('email', $request->email)->first();
            if($user){
                return response()->json(['error' => 'An account with this email already exists. Please sign in to continue.'], 500);
            }else{
                $password = $request->password;
                $validatedData = $request->validated();
                $user = User::create($validatedData);
                $token = $user->createToken('Initial Token')->plainTextToken;
                // Mail::to($user->email)->send(new WelcomeUser($user, $password));
                // $user->sendEmailVerificationNotification();
                return response()->json(['message' => 'User registered successfully.', 'user' => $user, 'token' => $token, 'isNewUser' => true], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'There was an error during authentication.', 'details' => $e->getMessage()], 500);
        }
    }

    public function login(request $request){
        try {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken('Initial Token')->plainTextToken;
                    return response()->json(['message' => 'Login successful', 'user' => $user, 'token' => $token, 'isNewUser' => false]);
                } else {
                    return response()->json(['message' => 'Wrong password'], 401);
                }
            } else {
                return response()->json(['message' => 'This user does not exist.'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'There was an error during authentication.', 'details' => $e->getMessage()], 500);
        }
    }

    public function authenticateProvider(authenticateProvider $request){
        try {
            $user = User::where('email', $request->email)->first();

            // Handle photo upload
            $filename = null;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');

                // Delete existing photo if it exists
                if ($user && $user->photo && file_exists(public_path('upload/user_images/' . $user->photo))) {
                    @unlink(public_path('upload/user_images/' . $user->photo));
                }

                // Save the new photo
                $filename = date('YmdHi') . uniqid() . $file->getClientOriginalName();
                $file->move(public_path('upload/user_images'), $filename);
            }

            if ($user) {
                $user->update([
                    'photo' => $filename ?: $user->photo,
                    'role' => 'provider',
                    'updated_at' => now(),
                ]);

                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken('Initial Token')->plainTextToken;
                    return response()->json([
                        'message' => 'Login successful',
                        'user' => $user,
                        'token' => $token,
                        'isNewUser' => false,
                    ]);
                } else {
                    return response()->json(['message' => 'Wrong password'], 401);
                }
            } else {
                // Register a new user
                $validatedData = $request->validated();

                // Add the default role and photo path to the validated data
                $validatedData['role'] = 'provider';
                $validatedData['photo'] = $filename;
                $validatedData['password'] = Hash::make($validatedData['password']);

                // Create the new user
                $user = User::create($validatedData);

                // Generate a token for the new user
                $token = $user->createToken('Initial Token')->plainTextToken;

                return response()->json([
                    'message' => 'User registered successfully.',
                    'user' => $user,
                    'token' => $token,
                    'isNewUser' => true,
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'There was an error during authentication.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function kycValidation(kycValidationRequest $request){
        try {
            $filename = null;
            if ($request->hasFile('document')) {
                    $file = $request->file('document');
                    $filename = date('YmdHi').uniqid().$file->getClientOriginalName();
                    $file->move(public_path('upload/kyc_documents'), $filename);
            }
            $filename2 = null;
            if ($request->hasFile('selfie')) {
                $file = $request->file('selfie');
                $filename2 = date('YmdHi').uniqid().$file->getClientOriginalName();
                $file->move(public_path('upload/kyc_selfies'), $filename2);
            }
            kyc_validation::create([
                'address' => $request->address,
                'document' => $filename,
                'selfie' => $filename2
            ]);
            return response()->json(['message' => 'KYC submitted successfully.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'There was an error during submission.', 'details' => $e->getMessage()], 500);
        }
    }

    public function redirect($client){
        try {
            $validClients = ['google', 'facebook', 'apple'];
            if (!in_array($client, $validClients)) {
                return response()->json(['error' => 'Invalid client specified.'], 400);
            }

            return Socialite::driver($client)->stateless()->redirect();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to redirect for authentication.', 'details' => $e->getMessage()], 500);
        }
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
                        $user->save();
                    }
                } else {
                    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                    $password = substr(str_shuffle($characters), 0, 8); // 8-character password

                     $user = User::create([
                        'name' => $clientUser->getName(),
                        'email' => $clientUser->getEmail(),
                        'email_verified_at' => now(),
                        'password' => $password,
                        $clientId => $clientUser->getId()
                    ]);

                    Mail::to($user->email)->send(new WelcomeUser($user, $password));
                }
            }
            $token = $user->createToken('Initial Token')->plainTextToken;
            $message = $user->wasRecentlyCreated ? 'User registered successfully.' : 'Logged in successfully.';

            return redirect()->away('https://aideapp.com.ng/profile?token=' . urlencode($token) . '&message=' . urlencode($message));

        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to handle authentication callback.', 'details' => $e->getMessage()], 500);
        }
    }

    public function verify(Request $request, $id, $hash){
        try {
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
            return response()->json(['message' => 'Email verified successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to verify email.', 'details' => $e->getMessage()], 500);
        }
    }

    public function resend(Request $request){
        try {
            if ($request->user()->hasVerifiedEmail()) {
                return response()->json(['message' => 'Email already verified.'], 400);
            }
            $request->user()->sendEmailVerificationNotification();
            return response()->json(['message' => 'Verification email sent.']);
        }catch (\Exception $e) {
            return response()->json(['error' => 'Unable to resend verification email.', 'details' => $e->getMessage()], 500);
        }
    }

    public function forgetPassword(Request $request){
        try {
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
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to process password reset.', 'details' => $e->getMessage()], 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request, $token, $email){
        try {
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
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to reset password.', 'details' => $e->getMessage()], 500);
        }
    }

}
