<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleController extends Controller
{
    // ! Constructor
    public function __construct()
    {
        $this->middleware("auth:sanctum");
        $this->middleware('permission:can-view-role')->only(['index', 'show']);
        $this->middleware('permission:can-create-role')->only(['store']);
        $this->middleware('permission:can-update-role')->only(['update']);
        $this->middleware('permission:can-delete-role')->only(['destroy']);
    }


    // * Create new role
    public function store(Request $request)
    {
        // * Validation
        $this->validate($request, [
            'name' => 'required|string|unique:roles,name',
            'guard_name' => 'required|string',
            'permissions' => 'sometimes|array',
        ]);

        // * Create new role with name and guard_name
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name
        ]);

        // * Attach permissions to the newly created role
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        }


        return response()->json([
            'status' => 'success',
            'message' => 'Successfully created role',
            'data' => $role
        ], 201);
    }

    // * View all roles with show permission
    public function index(Request $request)
    {
        $roles = Role::query();

        // * Query Search by name, guard_name or created_at
        $search = $request->query('search');
        if ($search) {
            $roles->where('name', 'like', '%' . $search . '%')
                ->orWhere('guard_name', 'like', '%' . $search . '%')
                ->orWhere('created_at', 'like', '%' . $search . '%')
                ->orWhere('updated_at', 'like', '%' . $search . '%');
        }

        // * Query Sort by name, guard_name or created_at
        $sort = $request->query('sort');
        if ($sort) {
            $roles->orderBy($sort, 'asc');
        }
        $roles = $roles->paginate(10);
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully retrieved roles data',
            'data' => $roles
        ], 200);
    }

    // * View role by id
    public function show($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully retrieved role data',
            'data' => $role
        ], 200);
    }

    // * Update role by id
    public function update(Request $request, $id)
    {
        // * Validation
        $this->validate($request, [
            'name' => 'required|string|unique:roles,name,' . $id,
            'guard_name' => 'required|string',
            'permissions' => 'required|array'
        ]);

        // * Find role by id
        $role = Role::find($id);

        // * Check role
        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found'
            ], 404);
        }

        // * Update role
        $role->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name
        ]);

        $permissions = Permission::where('id', $request->permissions)->get(['name'])->toArray();

        $role->syncPermissions($permissions);

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully updated role',
            'data' => $role
        ], 200);
    }

    // * Delete role by id
    public function destroy($id)
    {
        // * Find role by id
        $role = Role::find($id);

        // * Check role
        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found'
            ], 404);
        }

        // * Delete role
        $role->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully deleted role'
        ], 200);
    }
}
