<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'photo' => $user->photo,
            'name' => $user->name,
            'phone_number' => $user->phone_number,
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request){
        // Get the currently authenticated user
        $id = Auth::user()->id;
        $user = User::find($id);

        // Update user details
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            // Delete the old profile photo if it exists
            if ($user->photo && file_exists(public_path('upload/user_images/'.$user->photo))) {
                @unlink(public_path('upload/user_images/'.$user->photo));
            }
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/user_images'), $filename);
            $user->photo = $filename;
        }

        // Save changes to the database
        $user->save();

        // Return a JSON response indicating success
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    public function deleteAccount(Request $request) {
        $request->validate([
            'password' => 'required|string',
        ]);

         $id = Auth::user()->id;
         $user = User::find($id);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided password does not match our records.'
            ], 403);
        }

        $user->delete();
        $request->user()->currentAccessToken()->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Return a response or redirect to a different page
        return response()->json([
            'message' => 'Account deleted successfully'
        ], 200);
    }

}
