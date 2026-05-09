<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    private function authorizeRolePermission(string $permission): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasPagePermission('roles', $permission)) {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $account)
    {
        $this->authorizeRolePermission('can_view');

        $roles = Role::latest()->paginate(10);

        return view('backend.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $account)
    {
        $this->authorizeRolePermission('can_create');

        return view('backend.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $account)
    {
        $this->authorizeRolePermission('can_create');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
        ]);

        Role::create([
            'name' => $validated['name'],
        ]);

        return redirect()
            ->route('roles.index', [
                'account' => $account,
            ])
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $account, Role $role)
    {
        return redirect()
            ->route('roles.index', [
                'account' => $account,
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $account, Role $role)
    {
        $this->authorizeRolePermission('can_edit');

        return view('backend.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $account, Role $role)
    {
        $this->authorizeRolePermission('can_edit');

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
        ]);

        $role->update([
            'name' => $validated['name'],
        ]);

        return redirect()
            ->route('roles.index', [
                'account' => $account,
            ])
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $account, Role $role)
    {
        $this->authorizeRolePermission('can_delete');

        $role->delete();

        return redirect()
            ->route('roles.index', [
                'account' => $account,
            ])
            ->with('success', 'Role deleted successfully.');
    }
}