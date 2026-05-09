<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    private function authorizeServicePermission(string $permission): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasPagePermission('services', $permission)) {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $account)
    {
        $this->authorizeServicePermission('can_view');

        $services = Service::latest()->paginate(10);

        return view('backend.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $account)
    {
        $this->authorizeServicePermission('can_create');

        return view('backend.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $account)
    {
        $this->authorizeServicePermission('can_create');

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:services,name',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'gross_sales' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'net_sales' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ]);

        Service::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'gross_sales' => $validated['gross_sales'] ?? 0,
            'net_sales' => $validated['net_sales'] ?? 0,
        ]);

        return redirect()
            ->route('services.index', [
                'account' => $account,
            ])
            ->with('success', 'Service created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $account, Service $service)
    {
        return redirect()
            ->route('services.index', [
                'account' => $account,
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $account, Service $service)
    {
        $this->authorizeServicePermission('can_edit');

        return view('backend.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $account, Service $service)
    {
        $this->authorizeServicePermission('can_edit');

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('services', 'name')->ignore($service->id),
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'gross_sales' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'net_sales' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ]);

        $service->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'gross_sales' => $validated['gross_sales'] ?? 0,
            'net_sales' => $validated['net_sales'] ?? 0,
        ]);

        return redirect()
            ->route('services.index', [
                'account' => $account,
            ])
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $account, Service $service)
    {
        $this->authorizeServicePermission('can_delete');

        $service->delete();

        return redirect()
            ->route('services.index', [
                'account' => $account,
            ])
            ->with('success', 'Service deleted successfully.');
    }
}