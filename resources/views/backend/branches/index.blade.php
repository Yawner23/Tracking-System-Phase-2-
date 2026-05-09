@extends('layouts.backend')

@section('content')
@php
    $user = auth()->user();
    $account = request()->route('account');

    $canCreate = $user && $user->hasPagePermission('branches', 'can_create');
    $canEdit = $user && $user->hasPagePermission('branches', 'can_edit');
    $canDelete = $user && $user->hasPagePermission('branches', 'can_delete');

    $hasActions = $canEdit || $canDelete;
@endphp

<div class="max-w-screen-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Branches</h1>
            <p class="text-sm text-gray-500">
                Manage branch locations and contact information.
            </p>
        </div>

        @if($canCreate)
            <a href="{{ route('branches.create', ['account' => $account]) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-700 text-sm font-semibold">
                <i class="ri-add-line mr-2"></i>
                Add Branch
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
                        <th class="px-6 py-4">Address</th>
                        <th class="px-6 py-4">Contact Number</th>
                        <th class="px-6 py-4">Created At</th>

                        @if($hasActions)
                            <th class="px-6 py-4 text-right">Actions</th>
                        @endif
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($branches as $branch)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-600">
                                {{ $loop->iteration + ($branches->currentPage() - 1) * $branches->perPage() }}
                            </td>

                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $branch->address }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $branch->contact_number ?? 'N/A' }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $branch->created_at?->format('M d, Y') }}
                            </td>

                            @if($hasActions)
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        @if($canEdit)
                                            <a href="{{ route('branches.edit', ['account' => $account, 'branch' => $branch->id]) }}"
                                               class="inline-flex items-center px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 text-xs font-semibold">
                                                <i class="ri-edit-line mr-1"></i>
                                                Edit
                                            </a>
                                        @endif

                                        @if($canDelete)
                                            <form action="{{ route('branches.destroy', ['account' => $account, 'branch' => $branch->id]) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this branch?');">
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
                            <td colspan="{{ $hasActions ? 5 : 4 }}" class="px-6 py-10 text-center text-gray-500">
                                No branches found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($branches->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $branches->links() }}
            </div>
        @endif
    </div>
</div>
@endsection