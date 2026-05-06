<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of role privileges.
     */
    public function index()
    {
        $roles = Role::query()
            ->select('roles.*')
            ->selectSub(function ($query) {
                $query->from('role_page_permission')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('role_page_permission.role_id', 'roles.id');
            }, 'total_permissions')
            ->orderBy('roles.id', 'desc')
            ->paginate(10);

        return view('backend.role_permissions.index', compact('roles'));
    }

    /**
     * Show the form for creating role privileges.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $pages = Page::orderBy('description')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('backend.role_permissions.create', compact(
            'roles',
            'pages',
            'permissions'
        ));
    }

    /**
     * Store role privileges.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['nullable', 'array'],
            'permissions.*.*' => ['exists:permissions,id'],
        ]);

        $roleId = $validated['role_id'];
        $permissionMatrix = $validated['permissions'] ?? [];

        $validPageIds = Page::pluck('id')->map(fn ($id) => (string) $id)->toArray();

        DB::transaction(function () use ($roleId, $permissionMatrix, $validPageIds) {
            DB::table('role_page_permission')
                ->where('role_id', $roleId)
                ->delete();

            foreach ($permissionMatrix as $pageId => $permissionIds) {
                if (!in_array((string) $pageId, $validPageIds, true)) {
                    continue;
                }

                foreach ($permissionIds as $permissionId) {
                    DB::table('role_page_permission')->insertOrIgnore([
                        'role_id' => $roleId,
                        'page_id' => $pageId,
                        'permission_id' => $permissionId,
                    ]);
                }
            }
        });

        return redirect()
            ->route('role-privileges.index')
            ->with('success', 'Role privileges saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::findOrFail($id);
        $pages = Page::orderBy('description')->get();
        $permissions = Permission::orderBy('name')->get();

        $selectedPermissions = DB::table('role_page_permission')
            ->where('role_id', $role->id)
            ->get()
            ->groupBy('page_id')
            ->map(function ($items) {
                return $items->pluck('permission_id')->toArray();
            })
            ->toArray();

        return view('backend.role_permissions.show', compact(
            'role',
            'pages',
            'permissions',
            'selectedPermissions'
        ));
    }

    /**
     * Show the form for editing role privileges.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        $pages = Page::orderBy('description')->get();
        $permissions = Permission::orderBy('name')->get();

        $selectedPermissions = DB::table('role_page_permission')
            ->where('role_id', $role->id)
            ->get()
            ->groupBy('page_id')
            ->map(function ($items) {
                return $items->pluck('permission_id')->toArray();
            })
            ->toArray();

        return view('backend.role_permissions.edit', compact(
            'role',
            'pages',
            'permissions',
            'selectedPermissions'
        ));
    }

    /**
     * Update role privileges.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['nullable', 'array'],
            'permissions.*.*' => ['exists:permissions,id'],
        ]);

        $permissionMatrix = $validated['permissions'] ?? [];
        $validPageIds = Page::pluck('id')->map(fn ($pageId) => (string) $pageId)->toArray();

        DB::transaction(function () use ($role, $permissionMatrix, $validPageIds) {
            DB::table('role_page_permission')
                ->where('role_id', $role->id)
                ->delete();

            foreach ($permissionMatrix as $pageId => $permissionIds) {
                if (!in_array((string) $pageId, $validPageIds, true)) {
                    continue;
                }

                foreach ($permissionIds as $permissionId) {
                    DB::table('role_page_permission')->insertOrIgnore([
                        'role_id' => $role->id,
                        'page_id' => $pageId,
                        'permission_id' => $permissionId,
                    ]);
                }
            }
        });

        return redirect()
            ->route('role-privileges.index')
            ->with('success', 'Role privileges updated successfully.');
    }

    /**
     * Remove all privileges from the role.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);

        DB::table('role_page_permission')
            ->where('role_id', $role->id)
            ->delete();

        return redirect()
            ->route('role-privileges.index')
            ->with('success', 'Role privileges removed successfully.');
    }
}