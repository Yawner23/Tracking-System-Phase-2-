@extends('layouts.backend')

@section('content')
@php
    $user = auth()->user();
    $account = request()->route('account');

    $canCreate = $user && $user->hasPagePermission('services', 'can_create');
    $canEdit = $user && $user->hasPagePermission('services', 'can_edit');
    $canDelete = $user && $user->hasPagePermission('services', 'can_delete');

    $hasActions = $canEdit || $canDelete;
@endphp

<div class="max-w-screen-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Services</h1>
            <p class="text-sm text-gray-500">
                Manage service types, prices, gross sales, and net sales.
            </p>
        </div>

        @if($canCreate)
            <a href="{{ route('services.create', ['account' => $account]) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-700 text-sm font-semibold">
                <i class="ri-add-line mr-2"></i>
                Add Service
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">#</th>
                        <th class="px-6 py-4">Service Name</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Gross Sales</th>
                        <th class="px-6 py-4">Net Sales</th>
                        <th class="px-6 py-4">Created At</th>

                        @if($hasActions)
                            <th class="px-6 py-4 text-right">Actions</th>
                        @endif
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($services as $service)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-600">
                                {{ $loop->iteration + ($services->currentPage() - 1) * $services->perPage() }}
                            </td>

                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $service->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                ₱{{ number_format($service->price, 2) }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                ₱{{ number_format($service->gross_sales, 2) }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                ₱{{ number_format($service->net_sales, 2) }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $service->created_at?->format('M d, Y') }}
                            </td>

                            @if($hasActions)
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        @if($canEdit)
                                            <a href="{{ route('services.edit', ['account' => $account, 'service' => $service->id]) }}"
                                               class="inline-flex items-center px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 text-xs font-semibold">
                                                <i class="ri-edit-line mr-1"></i>
                                                Edit
                                            </a>
                                        @endif

                                        @if($canDelete)
                                            <form action="{{ route('services.destroy', ['account' => $account, 'service' => $service->id]) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this service?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-xs font-semibold">
                                                    <i class="ri-delete-bin-line mr-1"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $hasActions ? 7 : 6 }}" class="px-6 py-10 text-center text-gray-500">
                                No services found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($services->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>
@endsection