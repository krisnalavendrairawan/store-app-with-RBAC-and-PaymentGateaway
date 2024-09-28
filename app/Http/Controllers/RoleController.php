<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleController extends Controller
{
    private $title = 'label.role';
    private $icon = 'mdi mdi-account';
    private $dir = 'backend.role.';

    public function index()
    {
        $user = User::all();
        return view($this->dir . 'index', [
            'title' => __($this->title),
            'icon' => $this->icon,
            'user' => $user,
        ]);
    }

    public function search(Request $request)
    {
        $term = $request->input('term'); // Input from the frontend

        // Fetch users matching name or email
        $users = User::where('name', 'LIKE', "%{$term}%")
            ->orWhere('email', 'LIKE', "%{$term}%")
            ->limit(10) // Limit results for performance
            ->get(['id', 'name', 'email']); // Fetch relevant columns

        return response()->json($users);
    }

    public function getRolesPermissions($id)
    {

        $user = User::findOrFail($id);
        $roles = Role::all();
        $permissions = Permission::all();

        $userRoles = $user->roles->pluck('name')->toArray();
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        Log::info($userPermissions);

        return response()->json([
            'roles' => $roles,
            'permissions' => $permissions,
            'userRoles' => $userRoles,
            'userPermissions' => $userPermissions,
        ]);
    }

    public function assignRolesPermissions(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->syncRoles($request->roles);
        $user->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Roles and Permissions assigned successfully',
        ]);
    }
}
