<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    private function authorizeRolePrivilegePermission(string $permission): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasPagePermission('role-privileges', $permission)) {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Display a listing of role privileges.
     */
    public function index(string $account)
    {
        $this->authorizeRolePrivilegePermission('can_view');

        $roles = Role::query()
            ->select('roles.*')
            ->selectSub(function ($query) {
                $query->from('role_page_permission')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('role_page_permission.role_id', 'roles.id');
            }, 'total_permissions')
            ->latest()
            ->paginate(10);

        return view('backend.role_permissions.index', compact('roles'));
    }

    /**
     * Show the form for creating role privileges.
     */
    public function create(string $account)
    {
        $this->authorizeRolePrivilegePermission('can_create');

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
    public function store(Request $request, string $account)
    {
        $this->authorizeRolePrivilegePermission('can_create');

        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['nullable', 'array'],
            'permissions.*.*' => ['exists:permissions,id'],
        ]);

        $roleId = $validated['role_id'];
        $permissionMatrix = $validated['permissions'] ?? [];

        $validPageIds = Page::pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();

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
            ->route('role-privileges.index', [
                'account' => $account,
            ])
            ->with('success', 'Role privileges saved successfully.');
    }

    /**
     * Display the specified role privileges.
     */
    public function show(string $account, string $role_privilege)
    {
        $this->authorizeRolePrivilegePermission('can_view');

        $role = Role::findOrFail($role_privilege);
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
    public function edit(string $account, string $role_privilege)
    {
        $this->authorizeRolePrivilegePermission('can_edit');

        $role = Role::findOrFail($role_privilege);
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
    public function update(Request $request, string $account, string $role_privilege)
    {
        $this->authorizeRolePrivilegePermission('can_edit');

        $role = Role::findOrFail($role_privilege);

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['nullable', 'array'],
            'permissions.*.*' => ['exists:permissions,id'],
        ]);

        $permissionMatrix = $validated['permissions'] ?? [];

        $validPageIds = Page::pluck('id')
            ->map(fn ($pageId) => (string) $pageId)
            ->toArray();

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
            ->route('role-privileges.index', [
                'account' => $account,
            ])
            ->with('success', 'Role privileges updated successfully.');
    }

    /**
     * Remove all privileges from the role.
     */
    public function destroy(string $account, string $role_privilege)
    {
        $this->authorizeRolePrivilegePermission('can_delete');

        $role = Role::findOrFail($role_privilege);

        DB::table('role_page_permission')
            ->where('role_id', $role->id)
            ->delete();

        return redirect()
            ->route('role-privileges.index', [
                'account' => $account,
            ])
            ->with('success', 'Role privileges deleted successfully.');
    }
}