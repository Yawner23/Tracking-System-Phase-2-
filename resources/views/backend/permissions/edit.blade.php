@extends('layouts.backend')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900">Edit Privilege</h1>
        <p class="mt-1 text-sm text-slate-500">
            Update privilege information.
        </p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form action="{{ route('privileges.update', $permission->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">
                    Privilege Name
                </label>

                <input type="text"
                       name="name"
                       id="name"
                       value="{{ old('name', $permission->name) }}"
                       class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-400 focus:ring-slate-400">

                <p class="mt-1 text-xs text-slate-500">
                    Recommended format: can_view, can_create, can_edit, can_delete.
                </p>

                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="group" class="mb-2 block text-sm font-semibold text-slate-700">
                    Group
                </label>

                <input type="text"
                       name="group"
                       id="group"
                       value="{{ old('group', $permission->group ?? 'General') }}"
                       class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-400 focus:ring-slate-400">

                @error('group')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('privileges.index') }}"
                   class="rounded-xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                    Cancel
                </a>

                <button type="submit"
                        class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                    Update Privilege
                </button>
            </div>
        </form>
    </div>
</div>
@endsection