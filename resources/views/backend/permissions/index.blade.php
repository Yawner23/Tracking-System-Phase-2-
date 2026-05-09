@extends('layouts.backend')

@section('content')
@php
    $user = auth()->user();
    $account = request()->route('account');

    $canCreate = $user && $user->hasPagePermission('privileges', 'can_create');
    $canEdit = $user && $user->hasPagePermission('privileges', 'can_edit');
    $canDelete = $user && $user->hasPagePermission('privileges', 'can_delete');

    $hasActions = $canEdit || $canDelete;
@endphp

<div class="max-w-screen-2xl mx-auto">
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Privileges</h1>
            <p class="mt-1 text-sm text-slate-500">
                Manage system privileges and permission actions.
            </p>
        </div>

        @if($canCreate)
            <a href="{{ route('privileges.create', ['account' => $account]) }}"
               class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                <i class="ri-add-line"></i>
                Add Privilege
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-700 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">#</th>
                        <th class="px-6 py-4">Privilege Name</th>
                        <th class="px-6 py-4">Group</th>
                        <th class="px-6 py-4">Created At</th>

                        @if($hasActions)
                            <th class="px-6 py-4 text-right">Actions</th>
                        @endif
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @forelse($permissions as $permission)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-5 text-slate-700">
                                {{ $loop->iteration + ($permissions->currentPage() - 1) * $permissions->perPage() }}
                            </td>

                            <td class="px-6 py-5 font-semibold text-slate-900">
                                {{ $permission->name }}
                            </td>

                            <td class="px-6 py-5 text-slate-700">
                                {{ $permission->group ?? 'General' }}
                            </td>

                            <td class="px-6 py-5 text-slate-700">
                                {{ $permission->created_at?->format('M d, Y') }}
                            </td>

                            @if($hasActions)
                                <td class="px-6 py-5">
                                    <div class="flex justify-end gap-2">
                                        @if($canEdit)
                                            <a href="{{ route('privileges.edit', ['account' => $account, 'privilege' => $permission->id]) }}"
                                               class="inline-flex items-center rounded-lg bg-yellow-100 px-4 py-2 text-xs font-semibold text-yellow-700 hover:bg-yellow-200">
                                                <i class="ri-pencil-line mr-1"></i>
                                                Edit
                                            </a>
                                        @endif

                                        @if($canDelete)
                                            <form action="{{ route('privileges.destroy', ['account' => $account, 'privilege' => $permission->id]) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this privilege?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="inline-flex items-center rounded-lg bg-red-100 px-4 py-2 text-xs font-semibold text-red-700 hover:bg-red-200">
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
                            <td colspan="{{ $hasActions ? 5 : 4 }}" class="px-6 py-10 text-center text-slate-500">
                                No privileges found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($permissions->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $permissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection