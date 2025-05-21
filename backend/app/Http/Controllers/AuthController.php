<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Models\Employer;
use App\Models\Candidate;
// DB
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
//    public function register(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'name' => 'required|string|max:255',
//             'email' => 'required|string|email|max:255|unique:users',
//             'password' => 'required|string|min:8|confirmed',
//             'role' => 'required|in:employer,candidate,admin', // employer or candidate
//         ]);


//         if ($validator->fails()) {
//             $errors = $validator->errors();

//             if ($errors->has('email') && User::where('email', $request->email)->exists()) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Email already exists',
//                     'errors' => ['email' => 'This email is already registered']
//                 ], 422);
//             }

//             return response()->json([
//                 'success' => false,
//                 'message' => 'Validation failed',
//                 'errors' => $errors->all()
//             ], 422);
//         }

//         $user = User::create([
//             'name' => $request->name,
//             'email' => $request->email,
//             'password' => Hash::make($request->password),
//             'role' => $request->role // employer or candidate
//         ]);

//         return response()->json([
//             'success' => true,
//             'user' => $user,
//             'token' => $user->createToken('auth_token')->plainTextToken
//         ], 201);

//     }




public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|in:employer,candidate,admin',
        // 'company_name' => 'required_if:role,employer|string|max:255', // Add validation
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::beginTransaction();

        // Create user first
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        // Create role-specific profile
        if ($user->role === 'employer') {
            $employer = new Employer();
            $employer->id = $user->id; // Explicitly set ID
            $employer->company_name = "company_name";
            $employer->created_at = now();
            $employer->updated_at = now();
            $employer->save(); // Use save() instead of create()
        } elseif ($user->role === 'candidate') {
            Candidate::create([
                'id' => $user->id,
                // Add candidate-specific fields
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Registration failed',
            'error' => $e->getMessage()
        ], 500);
    }
}






 public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->all()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'invalid username or password',
                'errors' => ['email' => 'These credentials do not match our records']
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }



    public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();
    return response()->json([
        'success' => true,
        'message' => 'Logged out successfully'
    ], 200);
}



   public function user(Request $request)
{
    if (!$request->user()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated'
        ], 401);
    }
    return response()->json($request->user());
}


    public function update(Request $request)
    {
        $user = $request->user();
        $user->update($request->all());
        return response()->json($user, 200);
    }



public function sendResetEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        // Logic to send password reset email
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['message' => 'Password reset  email sent'], 200);
    }
    public function  get_current_user(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        return response()->json($user, 200);
    }


    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed'
            // 'token' => 'required'
        ]);
        // Logic to reset password
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        // Verify the token and reset the password
        // This is a placeholder for actual password reset logic
        return response()->json(['message' => 'Password reset successfully'], 200);
    }
      public function checkemail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if ($user) {
            return response()->json(['message' => 'Now You Can Change Password '], 200);
        }

        return response()->json(['error' => 'Email does not exist'], 404);
    }
}


