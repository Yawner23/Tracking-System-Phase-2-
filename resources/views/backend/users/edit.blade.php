@extends('layouts.backend')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
        <p class="text-sm text-gray-500">
            Update user account information.
        </p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                    Name
                </label>

                <input type="text"
                       name="name"
                       id="name"
                       value="{{ old('name', $user->name) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">

                @error('name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                    Email
                </label>

                <input type="email"
                       name="email"
                       id="email"
                       value="{{ old('email', $user->email) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">

                @error('email')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="contact_number" class="block text-sm font-semibold text-gray-700 mb-2">
                    Contact Number
                </label>

                <input type="text"
                       name="contact_number"
                       id="contact_number"
                       value="{{ old('contact_number', $user->contact_number) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">

                @error('contact_number')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="branch_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    Branch
                </label>

                <select name="branch_id"
                        id="branch_id"
                        class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                    <option value="">No Branch</option>

                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected(old('branch_id', $user->branch_id) == $branch->id)>
                            {{ $branch->address }}
                        </option>
                    @endforeach
                </select>

                @error('branch_id')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Roles
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($roles as $role)
                        <label class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 cursor-pointer hover:bg-gray-100">
                            <input type="checkbox"
                                   name="role_ids[]"
                                   value="{{ $role->id }}"
                                   class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                                   @checked(in_array($role->id, old('role_ids', $selectedRoles)))>

                            <span class="text-sm font-medium text-gray-700">
                                {{ $role->name }}
                            </span>
                        </label>
                    @endforeach
                </div>

                @error('role_ids')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                    New Password
                </label>

                <input type="password"
                       name="password"
                       id="password"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">

                <p class="text-xs text-gray-500 mt-1">
                    Leave blank if you do not want to change the password.
                </p>

                @error('password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                    Confirm New Password
                </label>

                <input type="password"
                       name="password_confirmation"
                       id="password_confirmation"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('users.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-semibold">
                    Cancel
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-700 text-sm font-semibold">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection