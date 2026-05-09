<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    private function authorizeBranchPermission(string $permission): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasPagePermission('branches', $permission)) {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $account)
    {
        $this->authorizeBranchPermission('can_view');

        $branches = Branch::latest()->paginate(10);

        return view('backend.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $account)
    {
        $this->authorizeBranchPermission('can_create');

        return view('backend.branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $account)
    {
        $this->authorizeBranchPermission('can_create');

        $validated = $request->validate([
            'address' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255'],
        ]);

        Branch::create($validated);

        return redirect()
            ->route('branches.index', [
                'account' => $account,
            ])
            ->with('success', 'Branch created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $account, string $branch)
    {
        return redirect()
            ->route('branches.index', [
                'account' => $account,
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $account, string $branch)
    {
        $this->authorizeBranchPermission('can_edit');

        $branch = Branch::findOrFail($branch);

        return view('backend.branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $account, string $branch)
    {
        $this->authorizeBranchPermission('can_edit');

        $branch = Branch::findOrFail($branch);

        $validated = $request->validate([
            'address' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255'],
        ]);

        $branch->update($validated);

        return redirect()
            ->route('branches.index', [
                'account' => $account,
            ])
            ->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $account, string $branch)
    {
        $this->authorizeBranchPermission('can_delete');

        $branch = Branch::findOrFail($branch);

        $branch->delete();

        return redirect()
            ->route('branches.index', [
                'account' => $account,
            ])
            ->with('success', 'Branch deleted successfully.');
    }
}