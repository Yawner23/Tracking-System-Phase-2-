@extends('layouts.backend')

@section('content')
<div class="max-w-screen-2xl mx-auto">
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">View Role Privileges</h1>
            <p class="mt-1 text-sm text-slate-500">
                Permissions assigned to <span class="font-semibold">{{ $role->name }}</span>.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('role-privileges.edit', $role->id) }}"
               class="inline-flex items-center rounded-lg bg-yellow-100 px-4 py-2 text-sm font-semibold text-yellow-700 hover:bg-yellow-200">
                <i class="ri-pencil-line mr-1"></i>
                Edit
            </a>

            <a href="{{ route('role-privileges.index') }}"
               class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                Back
            </a>
        </div>
    </div>

    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <label class="mb-2 block text-sm font-semibold text-slate-700">
            Role
        </label>

        <input type="text"
               value="{{ $role->name }}"
               disabled
               class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-700">
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Permissions</h2>

            <span class="text-sm text-slate-500">
                View only
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border border-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-700">
                    <tr>
                        <th class="border border-slate-200 px-4 py-3 text-left font-semibold">
                            Page
                        </th>

                        @foreach($permissions as $permission)
                            <th class="border border-slate-200 px-4 py-3 text-center font-semibold">
                                {{ $permission->name }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @forelse($pages as $page)
                        <tr class="hover:bg-slate-50">
                            <td class="border border-slate-200 px-4 py-4 font-medium text-slate-900">
                                {{ $page->description }}
                            </td>

                            @foreach($permissions as $permission)
                                <td class="border border-slate-200 px-4 py-4 text-center">
                                    @if(in_array($permission->id, $selectedPermissions[$page->id] ?? []))
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-green-100 text-green-700 font-bold">
                                            ✓
                                        </span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $permissions->count() + 1 }}"
                                class="border border-slate-200 px-4 py-10 text-center text-slate-500">
                                No pages found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <a href="{{ route('role-privileges.edit', $role->id) }}"
               class="rounded-xl bg-yellow-100 px-5 py-3 text-sm font-semibold text-yellow-700 hover:bg-yellow-200">
                Edit Privileges
            </a>

            <a href="{{ route('role-privileges.index') }}"
               class="rounded-xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                Back
            </a>
        </div>
    </div>
</div>
@endsection