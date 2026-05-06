@extends('layouts.backend')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Role</h1>
        <p class="text-sm text-gray-500">Update role information.</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                    Role Name
                </label>

                <input type="text"
                       name="name"
                       id="name"
                       value="{{ old('name', $role->name) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">

                @error('name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('roles.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-semibold">
                    Cancel
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-700 text-sm font-semibold">
                    Update Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection