@extends('layouts.backend')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Service</h1>
        <p class="text-sm text-gray-500">
            Add a new service type and price.
        </p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form action="{{ route('services.store') }}" method="POST">
            @csrf

            <div class="mb-5">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                    Service Name
                </label>

                <input type="text"
                       name="name"
                       id="name"
                       value="{{ old('name') }}"
                       placeholder="Example: Full Re-glue"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">

                @error('name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">
                    Price
                </label>

                <input type="number"
                       step="0.01"
                       min="0"
                       name="price"
                       id="price"
                       value="{{ old('price', 0) }}"
                       placeholder="0.00"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">

                @error('price')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="gross_sales" class="block text-sm font-semibold text-gray-700 mb-2">
                    Gross Sales
                </label>

                <input type="number"
                       step="0.01"
                       min="0"
                       name="gross_sales"
                       id="gross_sales"
                       value="{{ old('gross_sales', 0) }}"
                       placeholder="0.00"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">

                @error('gross_sales')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="net_sales" class="block text-sm font-semibold text-gray-700 mb-2">
                    Net Sales
                </label>

                <input type="number"
                       step="0.01"
                       min="0"
                       name="net_sales"
                       id="net_sales"
                       value="{{ old('net_sales', 0) }}"
                       placeholder="0.00"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">

                @error('net_sales')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('services.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-semibold">
                    Cancel
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-700 text-sm font-semibold">
                    Save Service
                </button>
            </div>
        </form>
    </div>
</div>
@endsection