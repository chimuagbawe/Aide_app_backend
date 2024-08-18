<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        return response()->json(['user' => $user]);
    }

    public function updateProfile(UpdateProfileRequest $request){
        $id = Auth::id();
        $user = User::find($id);

        // Update the fields present in the request
        if ($request->filled('name')) {
            $user->name = $request->input('name');
        }

        if ($request->filled('phone_number')) {
            $user->phone_number = $request->input('phone_number');
        }

        // Handle profile photo if present
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            // Delete the old profile photo if it exists
            if ($user->photo && file_exists(public_path('upload/user_images/'.$user->photo))) {
                @unlink(public_path('upload/user_images/'.$user->photo));
            }
            $filename = date('YmdHi').uniqid().$file->getClientOriginalName();
            $file->move(public_path('upload/user_images'), $filename);
            $user->photo = $filename;
        }

        $user->save();

        // Log updated user data for debugging
        Log::info('User updated:', $user->toArray());

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }


    public function deleteAccount(Request $request) {
        $request->validate([
            'password' => 'required|string',
        ]);

         $id = Auth::id();
         $user = User::find($id);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Wrong Password.'
            ], 403);
        }

        $user->delete();

        // Return a response or redirect to a different page
        return response()->json([
            'message' => 'Account deleted successfully'
        ], 200);
    }

}