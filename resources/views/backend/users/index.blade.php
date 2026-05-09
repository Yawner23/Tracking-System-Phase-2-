@extends('layouts.backend')

@section('content')
@php
    $authUser = auth()->user();
    $account = request()->route('account');

    $canCreate = $authUser && $authUser->hasPagePermission('users', 'can_create');
    $canEdit = $authUser && $authUser->hasPagePermission('users', 'can_edit');
    $canDelete = $authUser && $authUser->hasPagePermission('users', 'can_delete');

    $hasActions = $canEdit || $canDelete;
@endphp

<div class="max-w-screen-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Users</h1>
            <p class="text-sm text-gray-500">
                Manage system users, roles, and branch assignments.
            </p>
        </div>

        @if($canCreate)
            <a href="{{ route('users.create', ['account' => $account]) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-700 text-sm font-semibold">
                <i class="ri-add-line mr-2"></i>
                Add User
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
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Contact Number</th>
                        <th class="px-6 py-4">Branch</th>
                        <th class="px-6 py-4">Roles</th>
                        <th class="px-6 py-4">Created At</th>

                        @if($hasActions)
                            <th class="px-6 py-4 text-right">Actions</th>
                        @endif
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-600">
                                {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                            </td>

                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $user->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $user->email }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $user->contact_number ?? 'N/A' }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $user->branch?->address ?? 'No Branch' }}
                            </td>

                            <td class="px-6 py-4">
                                @if($user->roles->count())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($user->roles as $role)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400">No role</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $user->created_at?->format('M d, Y') }}
                            </td>

                            @if($hasActions)
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        @if($canEdit)
                                            <a href="{{ route('users.edit', ['account' => $account, 'user' => $user->id]) }}"
                                               class="inline-flex items-center px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 text-xs font-semibold">
                                                <i class="ri-edit-line mr-1"></i>
                                                Edit
                                            </a>
                                        @endif

                                        @if($canDelete)
                                            <form action="{{ route('users.destroy', ['account' => $account, 'user' => $user->id]) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this user?');">
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
                            <td colspan="{{ $hasActions ? 8 : 7 }}" class="px-6 py-10 text-center text-gray-500">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection