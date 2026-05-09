<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    private function authorizePagePermission(string $permission): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasPagePermission('pages', $permission)) {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Display a listing of pages.
     */
    public function index(string $account)
    {
        $this->authorizePagePermission('can_view');

        $pages = Page::latest()->paginate(10);

        return view('backend.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(string $account)
    {
        $this->authorizePagePermission('can_create');

        return view('backend.pages.create');
    }

    /**
     * Store a newly created page.
     */
    public function store(Request $request, string $account)
    {
        $this->authorizePagePermission('can_create');

        $validated = $request->validate([
            'description' => [
                'required',
                'string',
                'max:255',
                'unique:pages,description',
            ],
        ]);

        Page::create([
            'description' => strtolower(str_replace(' ', '-', $validated['description'])),
        ]);

        return redirect()
            ->route('pages.index', [
                'account' => $account,
            ])
            ->with('success', 'Page created successfully.');
    }

    /**
     * Display the specified page.
     */
    public function show(string $account, Page $page)
    {
        return redirect()
            ->route('pages.index', [
                'account' => $account,
            ]);
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(string $account, Page $page)
    {
        $this->authorizePagePermission('can_edit');

        return view('backend.pages.edit', compact('page'));
    }

    /**
     * Update the specified page.
     */
    public function update(Request $request, string $account, Page $page)
    {
        $this->authorizePagePermission('can_edit');

        $validated = $request->validate([
            'description' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pages', 'description')->ignore($page->id),
            ],
        ]);

        $page->update([
            'description' => strtolower(str_replace(' ', '-', $validated['description'])),
        ]);

        return redirect()
            ->route('pages.index', [
                'account' => $account,
            ])
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Remove the specified page.
     */
    public function destroy(string $account, Page $page)
    {
        $this->authorizePagePermission('can_delete');

        $page->delete();

        return redirect()
            ->route('pages.index', [
                'account' => $account,
            ])
            ->with('success', 'Page deleted successfully.');
    }
}