<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private function authorizeUserPermission(string $permission): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasPagePermission('users', $permission)) {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $account)
    {
        $this->authorizeUserPermission('can_view');

        $users = User::with(['branch', 'roles'])
            ->latest()
            ->paginate(10);

        return view('backend.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $account)
    {
        $this->authorizeUserPermission('can_create');

        $branches = Branch::orderBy('address')->get();
        $roles = Role::orderBy('name')->get();

        return view('backend.users.create', compact('branches', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $account)
    {
        $this->authorizeUserPermission('can_create');

        $validated = $request->validate([
            'branch_id' => [
                'nullable',
                'exists:branches,id',
                Rule::unique('users', 'branch_id')->whereNotNull('branch_id'),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'contact_number' => [
                'nullable',
                'string',
                'max:30',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'role_ids' => [
                'nullable',
                'array',
            ],
            'role_ids.*' => [
                'exists:roles,id',
            ],
        ]);

        $user = User::create([
            'branch_id' => $validated['branch_id'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        $user->roles()->sync($validated['role_ids'] ?? []);

        return redirect()
            ->route('users.index', [
                'account' => $account,
            ])
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $account, User $user)
    {
        return redirect()
            ->route('users.index', [
                'account' => $account,
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $account, User $user)
    {
        $this->authorizeUserPermission('can_edit');

        $user->load('roles');

        $branches = Branch::orderBy('address')->get();
        $roles = Role::orderBy('name')->get();

        $selectedRoles = $user->roles->pluck('id')->toArray();

        return view('backend.users.edit', compact(
            'user',
            'branches',
            'roles',
            'selectedRoles'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $account, User $user)
    {
        $this->authorizeUserPermission('can_edit');

        $validated = $request->validate([
            'branch_id' => [
                'nullable',
                'exists:branches,id',
                Rule::unique('users', 'branch_id')
                    ->ignore($user->id)
                    ->whereNotNull('branch_id'),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'contact_number' => [
                'nullable',
                'string',
                'max:30',
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'role_ids' => [
                'nullable',
                'array',
            ],
            'role_ids.*' => [
                'exists:roles,id',
            ],
        ]);

        $data = [
            'branch_id' => $validated['branch_id'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        $user->roles()->sync($validated['role_ids'] ?? []);

        return redirect()
            ->route('users.index', [
                'account' => $account,
            ])
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $account, User $user)
    {
        $this->authorizeUserPermission('can_delete');

        if (auth()->id() === $user->id) {
            return redirect()
                ->route('users.index', [
                    'account' => $account,
                ])
                ->with('success', 'You cannot delete your own account.');
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()
            ->route('users.index', [
                'account' => $account,
            ])
            ->with('success', 'User deleted successfully.');
    }
}