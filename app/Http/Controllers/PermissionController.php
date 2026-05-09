<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    private function authorizePrivilegePermission(string $permission): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasPagePermission('privileges', $permission)) {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $account)
    {
        $this->authorizePrivilegePermission('can_view');

        $permissions = Permission::query()
            ->orderBy('group')
            ->orderBy('name')
            ->paginate(10);

        return view('backend.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $account)
    {
        $this->authorizePrivilegePermission('can_create');

        return view('backend.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $account)
    {
        $this->authorizePrivilegePermission('can_create');

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:permissions,name',
            ],
            'group' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        Permission::create([
            'name' => strtolower(str_replace(' ', '_', $validated['name'])),
            'group' => $validated['group'] ?? 'General',
        ]);

        return redirect()
            ->route('privileges.index', [
                'account' => $account,
            ])
            ->with('success', 'Privilege created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $account, Permission $privilege)
    {
        return redirect()
            ->route('privileges.index', [
                'account' => $account,
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $account, Permission $privilege)
    {
        $this->authorizePrivilegePermission('can_edit');

        $permission = $privilege;

        return view('backend.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $account, Permission $privilege)
    {
        $this->authorizePrivilegePermission('can_edit');

        $permission = $privilege;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($permission->id),
            ],
            'group' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $permission->update([
            'name' => strtolower(str_replace(' ', '_', $validated['name'])),
            'group' => $validated['group'] ?? 'General',
        ]);

        return redirect()
            ->route('privileges.index', [
                'account' => $account,
            ])
            ->with('success', 'Privilege updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $account, Permission $privilege)
    {
        $this->authorizePrivilegePermission('can_delete');

        $privilege->delete();

        return redirect()
            ->route('privileges.index', [
                'account' => $account,
            ])
            ->with('success', 'Privilege deleted successfully.');
    }
}