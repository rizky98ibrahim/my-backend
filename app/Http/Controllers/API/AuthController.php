<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use App\Models\User;
use App\Mail\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{

    // ! Profile Picture
    private function uploadProfilePicture(Request $request)
    {
        if ($request->hasFile('profile_picture')) {
            $profile_picture = $request->file('profile_picture');
            $fileName = time() . '_' . $profile_picture->getClientOriginalName();
            $profile_picture->storeAs('public/user', $fileName);
            $profile_picture->move(public_path('images/user'), $fileName);
        } else {
            $fileName = 'default.png';
        }

        return $fileName;
    }

    // ! Normalize Phone Number
    private function normalizePhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (substr($phoneNumber, 0, 1) === '0' || substr($phoneNumber, 0, 1) === '8') {
            $phoneNumber = '62' . substr($phoneNumber, 1);
        } else {
            $phoneNumber = null;
        }
        return $phoneNumber;
    }
    public function register(Request $request)
    {
        try {
            // * Validate Request
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|unique:users',
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone_number' => 'required|string|unique:users',
                'address' => 'required|string',
                'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 400);
            }

            // * Upload Profile Picture
            $profile_picture = $this->uploadProfilePicture($request);

            // * Create User
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => strtolower($request->email),
                'phone_number' => $this->normalizePhoneNumber($request->phone_number),
                'address' => $request->address,
                'profile_picture' => $profile_picture,
                'password' => Hash::make($request->password),
                'status' => 'active'
            ]);

            // * Assign Role
            $role = Role::findByName('user');
            $user->assignRole($role);

            if ($user) {
                $verify2 = DB::table('password_reset_tokens')->where([
                    ['email', $request->all()['email']]
                ]);

                if ($verify2->exists()) {
                    $verify2->delete();
                }
                $pin = rand(100000, 999999);
                DB::table('password_reset_tokens')->insert([
                    'email' => $request->email,
                    'token' => $pin,
                    'created_at' => now()
                ]);
            }

            // * Send Email
            Mail::to($request->email)->send(new VerifyEmail($pin));

            // * Create Token
            $token = $user->createToken('auth_token')->plainTextToken;

            // * Response
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully created user',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 400);
        }
    }

    // ! Verify Email
    public function verifyEmail(Request $request)
    {
        try {
            // * Validate Request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'token' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 400);
            }

            // * Verify Email
            $verify = DB::table('password_reset_tokens')->where([
                ['email', $request->all()['email']],
                ['token', $request->all()['token']]
            ]);

            if ($verify->exists()) {
                $verify->delete();
                $user = User::where('email', $request->all()['email'])->first();
                $user->update([
                    'email_verified_at' => now()
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Successfully verified email'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token'
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 400);
        }
    }

    // ! Resend Pin
    public function resendPin(Request $request)
    {
        try {
            // * Validate Request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 400);
            }

            // * Resend Pin
            $user = User::where('email', $request->all()['email'])->first();
            if ($user) {
                $verify2 = DB::table('password_reset_tokens')->where([
                    ['email', $request->all()['email']]
                ]);

                if ($verify2->exists()) {
                    $verify2->delete();
                }
                $pin = rand(100000, 999999);
                DB::table('password_reset_tokens')->insert([
                    'email' => $request->email,
                    'token' => $pin,
                    'created_at' => now()
                ]);

                // * Send Email
                Mail::to($request->email)->send(new VerifyEmail($pin));

                return response()->json([
                    'status' => 'success',
                    'message' => 'Verification pin has been sent to your email'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 400);
        }
    }

    // ! Login
    public function login(Request $request)
    {
        try {

            // * Validate Request
            $validator = Validator::make($request->all(), [
                'credentials' => 'required|string',
                'password' => 'required|string'
            ]);

            // * Check Validation
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 400);
            }

            // * Login
            $credentials = $request->input('credentials');
            $input = filter_var($credentials, FILTER_VALIDATE_EMAIL)
                ? 'email'
                : (ctype_digit($credentials) ? 'phone_number' : 'username');
            if (empty($input)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials'
                ], 400);
            }

            $user = User::where($input, $credentials)->first();

            // Check if user is verified by DB user email_verified_at
            if (!$user->email_verified_at) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please verify your email first'
                ], 400);
            }
            $user->email_verified_at = Carbon::now()
                ->toDateTimeString();
            $user->save();

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password does not match'
                ], 401);
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged in',
                'data' => [
                    'token' => $token,
                    'user' => $user,
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 400);
        }
    }

    // ! Forgot Password
    public function forgotPassword(Request $request)
    {
        try {

            // * Validate Request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            // * Check Validation
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 400);
            }

            // * Forgot Password
            $user = User::where('email', $request->all()['email'])->first();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }
            $verify2 = DB::table('password_reset_tokens')->where([
                ['email', $request->all()['email']]
            ]);
            if ($verify2->exists()) {
                $verify2->delete();
            }
            $pin = rand(100000, 999999);
            $password_reset = DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $pin,
                'created_at' => now()
            ]);
            if ($password_reset) {
                Mail::to($request->email)->send(new ResetPassword($pin));
                return response()->json([
                    'status' => 'success',
                    'message' => 'Successfully sent reset password email'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send reset password email'
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 400);
        }
    }

    // ! Verify Pin
    public function verifyPin(Request $request)
    {
        try {

            // * Validate Request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'token' => 'required'
            ]);

            // * Check Validation
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 400);
            }

            // * Verify Pin
            $verify = DB::table('password_reset_tokens')->where([
                ['email', $request->all()['email']],
                ['token', $request->all()['token']]
            ]);
            if ($verify->exists()) {
                $differance = Carbon::now()->diffInMinutes($verify->first()->created_at);
                if ($differance > 5) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Token expired'
                    ], 400);
                } else {
                    DB::table('password_resets')->where([
                        ['email', $request->all()['email']],
                        ['token', $request->all()['token']],
                    ])->delete();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Successfully verified pin'
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token'
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 400);
        }
    }

    // ! Reset Password
    public function resetPassword(Request $request)
    {
        try {

            // * Validate Request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'token' => 'required',
                'password' => 'required|string|min:8|confirmed'
            ]);

            // * Check Validation
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 400);
            }

            // * Reset Password
            $user = User::where('email', $request->all()['email'])->first();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // * Token
            $token = $user->createToken('auth_token')->plainTextToken;

            // * Response
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully reset password',
                'data' => [
                    'token' => $token,
                    'user' => $user,
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 400);
        }
    }

    // ! Get User
    public function getUser(Request $request)
    {
        try {

            // * Get User
            $user = $request->user();

            // * Response
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully retrieved user data',
                'data' => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 400);
        }
    }

    // ! Logout
    public function logout(Request $request)
    {
        try {

            // * Logout
            $request->user()->currentAccessToken()->delete();

            // * Response
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
