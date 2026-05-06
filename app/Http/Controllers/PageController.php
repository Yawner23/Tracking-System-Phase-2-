<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    /**
     * Display a listing of pages.
     */
    public function index()
    {
        $pages = Page::latest()->paginate(10);

        return view('backend.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create()
    {
        return view('backend.pages.create');
    }

    /**
     * Store a newly created page.
     */
    public function store(Request $request)
    {
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
            ->route('pages.index')
            ->with('success', 'Page created successfully.');
    }

    /**
     * Display the specified page.
     */
    public function show(string $id)
    {
        return redirect()->route('pages.index');
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(string $id)
    {
        $page = Page::findOrFail($id);

        return view('backend.pages.edit', compact('page'));
    }

    /**
     * Update the specified page.
     */
    public function update(Request $request, string $id)
    {
        $page = Page::findOrFail($id);

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
            ->route('pages.index')
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Remove the specified page.
     */
    public function destroy(string $id)
    {
        $page = Page::findOrFail($id);

        $page->delete();

        return redirect()
            ->route('pages.index')
            ->with('success', 'Page deleted successfully.');
    }
}