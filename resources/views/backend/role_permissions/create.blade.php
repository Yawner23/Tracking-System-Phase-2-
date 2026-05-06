@extends('layouts.backend')

@section('content')
<div class="max-w-screen-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900">Create Role Privileges</h1>
        <p class="mt-1 text-sm text-slate-500">
            Assign permissions to a role.
        </p>
    </div>

    <form action="{{ route('role-privileges.store') }}" method="POST">
        @csrf

        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <label for="role_id" class="mb-2 block text-sm font-semibold text-slate-700">
                Role
            </label>

            <select name="role_id"
                    id="role_id"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-400 focus:ring-slate-400">
                <option value="">Select Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>

            @error('role_id')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">Permissions</h2>

                <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox"
                           id="toggle-all"
                           class="rounded border-slate-300 text-slate-900 focus:ring-slate-400">
                    <span>All Permissions</span>
                </label>
            </div>

            @error('permissions')
                <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @php
                $oldPermissions = old('permissions', []);
            @endphp

            <div class="overflow-x-auto">
                <table class="w-full border border-slate-200 text-sm">
                    <thead class="bg-slate-50 text-slate-700">
                        <tr>
                            <th class="border border-slate-200 px-4 py-3 text-left font-semibold">Page</th>
                            @foreach($permissions as $permission)
                                <th class="border border-slate-200 px-4 py-3 text-center font-semibold">
                                    {{ $permission->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($pages as $page)
                            <tr class="hover:bg-slate-50">
                                <td class="border border-slate-200 px-4 py-4 font-medium text-slate-900">
                                    {{ $page->description }}
                                </td>

                                @foreach($permissions as $permission)
                                    <td class="border border-slate-200 px-4 py-4 text-center">
                                        <input type="checkbox"
                                               name="permissions[{{ $page->id }}][]"
                                               value="{{ $permission->id }}"
                                               class="permission-checkbox rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                                               @checked(in_array($permission->id, $oldPermissions[$page->id] ?? []))>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                        class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                    Save Role Privileges
                </button>

                <a href="{{ route('role-privileges.index') }}"
                   class="rounded-xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('toggle-all').addEventListener('change', function () {
        document.querySelectorAll('.permission-checkbox').forEach((checkbox) => {
            checkbox.checked = this.checked;
        });
    });
</script>
@endsection