<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:can-view-user')->only(['index', 'show']);
        $this->middleware('permission:can-create-user')->only(['store']);
        $this->middleware('permission:can-update-user')->only(['update']);
        $this->middleware('permission:can-delete-user')->only(['destroy']);
    }

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

    // * View all users
    public function index(Request $request)
    {
        $users = User::query();

        // * Query Search by name, email or created_at
        $search = $request->query('search');
        if ($search) {
            $users->where('username', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('phone_number', 'like', '%' . $search . '%')
                ->orWhere('address', 'like', '%' . $search . '%')
                ->orWhere('place_of_birth', 'like', '%' . $search . '%')
                ->orWhere('date_of_birth', 'like', '%' . $search . '%')
                ->orWhere('bio', 'like', '%' . $search . '%')
                ->orWhere('job', 'like', '%' . $search . '%')
                ->orWhere('created_at', 'like', '%' . $search . '%')
                ->orWhere('updated_at', 'like', '%' . $search . '%');
        }
        // * Query Sort by name, email or created_at
        $sort = $request->query('sort');
        if ($sort) {
            $users->orderBy($sort, 'asc');
        }
        $users = $users->paginate(10);
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully retrieved users data',
            'data' => $users
        ], 200);
    }

    // * View user by id
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully retrieved user data',
            'data' => $user
        ], 200);
    }

    // * Create new user
    public function store(Request $request)
    {
        // * Validation
        $this->validate($request, [
            'username' => 'required|string|max:50|unique:users',
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'phone_number' => 'required|string|max:20|unique:users',
            'address' => 'sometimes|string',
            'place_of_birth' => 'sometimes|string',
            'date_of_birth' => 'sometimes|date',
            'profile_picture' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'religion' => 'sometimes',
            'gender' => 'sometimes',
            'status' => 'sometimes',
        ]);

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

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully created user',
            'data' => $user
        ], 201);
    }

    // * Update user by id
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'username' => 'required',
            'name' => 'required',
            'email' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
            'place_of_birth' => 'required',
            'date_of_birth' => 'required',
            'profile_picture' => 'required',
            'religion' => 'sometimes',
            'gender' => 'sometimes',
            'status' => 'sometimes',
        ]);
        $profile_picture = $this->uploadProfilePicture($request);
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => strtolower($request->email),
            'phone_number' => $this->normalizePhoneNumber($request->phone_number),
            'address' => $request->address,
            'profile_picture' => $profile_picture,
            'password' => Hash::make($request->password),
            'status' => 'active'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully updated user',
            'data' => $user
        ], 200);
    }

    // * Delete user by id
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
        $user->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully deleted user'
        ], 200);
    }
}
