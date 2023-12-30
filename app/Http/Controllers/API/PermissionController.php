<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    // ! Constructor
    public function __construct()
    {
        $this->middleware("auth:sanctum");
        $this->middleware('permission:can-view-permission')->only(['index', 'show']);
        $this->middleware('permission:can-create-permission')->only(['store']);
        $this->middleware('permission:can-update-permission')->only(['update']);
        $this->middleware('permission:can-delete-permission')->only(['destroy']);
    }

    // * View all permissions with show permission
    public function index(Request $request)
    {
        $permissions = Permission::query();

        // * Query Search by name, guard_name or created_at
        $search = $request->query('search');
        if ($search) {
            $permissions->where('name', 'like', '%' . $search . '%')
                ->orWhere('guard_name', 'like', '%' . $search . '%')
                ->orWhere('created_at', 'like', '%' . $search . '%')
                ->orWhere('updated_at', 'like', '%' . $search . '%');
        }

        // * Query Sort by name, guard_name or created_at
        $sort = $request->query('sort');
        if ($sort) {
            $permissions->orderBy($sort, 'asc');
        }
        $permissions = $permissions->paginate(10);
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully retrieved permissions data',
            'data' => $permissions
        ], 200);
    }

    // * View permission by id
    public function show($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permission not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully retrieved permission data',
            'data' => $permission
        ], 200);
    }

    // * Create new permission
    public function store(Request $request)
    {
        // * Validation
        $this->validate($request, [
            'name' => 'required|string|unique:permissions,name',
            'guard_name' => 'required|string'
        ]);

        // * Create permission
        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name
        ]);

        // * Response
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully created permission',
            'data' => $permission
        ], 201);
    }

    // * Update permission
    public function update(Request $request, $id)
    {
        // * Validation
        $this->validate($request, [
            'name' => 'required|string|unique:permissions,name,' . $id,
            'guard_name' => 'required|string'
        ]);

        // * Update permission
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permission not found'
            ], 404);
        }
        $permission->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name
        ]);

        // * Response
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully updated permission',
            'data' => $permission
        ], 200);
    }

    // * Delete permission
    public function destroy($id)
    {
        // * Delete permission
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permission not found'
            ], 404);
        }
        $permission->delete();

        // * Response
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully deleted permission'
        ], 200);
    }
}
