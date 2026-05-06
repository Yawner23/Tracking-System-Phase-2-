@extends('layouts.backend')

@section('content')
<div class="max-w-screen-2xl mx-auto">
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Role Privileges</h1>
            <p class="mt-1 text-sm text-slate-500">
                Manage role permissions and assignments.
            </p>
        </div>

        <a href="{{ route('role-privileges.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">
            <i class="ri-add-line"></i>
            Assign Role Privileges
        </a>
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
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Total Permissions</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @forelse($roles as $role)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-5 text-slate-700">
                                {{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}
                            </td>

                            <td class="px-6 py-5 font-semibold text-slate-900">
                                {{ $role->name }}
                            </td>

                            <td class="px-6 py-5 text-slate-700">
                                {{ $role->total_permissions ?? 0 }}
                            </td>

                            <td class="px-6 py-5">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('role-privileges.show', $role->id) }}"
                                       class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">
                                        View
                                    </a>

                                    <a href="{{ route('role-privileges.edit', $role->id) }}"
                                       class="inline-flex items-center rounded-lg bg-yellow-100 px-4 py-2 text-xs font-semibold text-yellow-700 hover:bg-yellow-200">
                                        <i class="ri-pencil-line mr-1"></i>
                                        Edit
                                    </a>

                                    <form action="{{ route('role-privileges.destroy', $role->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete all privileges for this role?');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="inline-flex items-center rounded-lg bg-red-100 px-4 py-2 text-xs font-semibold text-red-700 hover:bg-red-200">
                                            <i class="ri-delete-bin-line mr-1"></i>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">
                                No role privileges found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($roles->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection