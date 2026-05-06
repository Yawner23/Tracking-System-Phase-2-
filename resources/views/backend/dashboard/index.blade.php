@extends('layouts.backend')

@section('content')
<div class="max-w-screen-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Welcome Dashboard</h1>
        <p class="text-sm text-gray-500">
            Overview of incoming, transit, repair, completed, unprocessed, and back job waybills
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        {{-- Total Incoming Waybills --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Total Incoming Waybills</p>
                    <h2 class="text-3xl font-bold text-gray-900 mt-1">0</h2>
                </div>

                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="ri-inbox-archive-line text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        {{-- Waybills In Transit --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Waybills In Transit</p>
                    <h2 class="text-3xl font-bold text-gray-900 mt-1">0</h2>
                </div>

                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                    <i class="ri-truck-line text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        {{-- Waybills Under Repair --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Waybills Under Repair</p>
                    <h2 class="text-3xl font-bold text-gray-900 mt-1">0</h2>
                </div>

                <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                    <i class="ri-tools-line text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>

        {{-- Completed Waybills --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Completed Waybills</p>
                    <h2 class="text-3xl font-bold text-gray-900 mt-1">0</h2>
                </div>

                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="ri-checkbox-circle-line text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        {{-- Unprocessed Waybills --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Unprocessed Waybills</p>
                    <h2 class="text-3xl font-bold text-gray-900 mt-1">0</h2>
                </div>

                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="ri-arrow-go-back-line text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        {{-- Back Job Waybills --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Back Job Waybills</p>
                    <h2 class="text-3xl font-bold text-gray-900 mt-1">0</h2>
                </div>

                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="ri-restart-line text-2xl text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-xl">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Welcome Dashboard</h2>

        <ul class="list-disc pl-6 text-gray-700 space-y-2">
            <li>
                Total Incoming Waybills
                <ul class="list-disc pl-6 mt-1 space-y-1">
                    <li>Waybills In Transit</li>
                </ul>
            </li>

            <li>
                Waybills Under Repair
                <ul class="list-disc pl-6 mt-1 space-y-1">
                    <li>Completed Waybills</li>
                    <li>Unprocessed Waybills</li>
                    <li>Back Job Waybills</li>
                </ul>
            </li>
        </ul>
    </div>
</div>
@endsection