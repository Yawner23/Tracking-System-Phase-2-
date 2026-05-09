@extends('layouts.backend')

@section('content')
@php
    $account = request()->route('account');
@endphp

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Branch</h1>
        <p class="text-sm text-gray-500">
            Add a new branch location.
        </p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form action="{{ route('branches.store', ['account' => $account]) }}" method="POST">
            @csrf

            <div class="mb-5">
                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                    Address
                </label>

                <input type="text"
                       name="address"
                       id="address"
                       value="{{ old('address') }}"
                       placeholder="Example: Main Branch"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">

                @error('address')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="contact_number" class="block text-sm font-semibold text-gray-700 mb-2">
                    Contact Number
                </label>

                <input type="text"
                       name="contact_number"
                       id="contact_number"
                       value="{{ old('contact_number') }}"
                       placeholder="Example: 09123456789"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">

                @error('contact_number')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('branches.index', ['account' => $account]) }}"
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-semibold">
                    Cancel
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-700 text-sm font-semibold">
                    Save Branch
                </button>
            </div>
        </form>
    </div>
</div>
@endsection