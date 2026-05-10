@extends('layouts.backend')

@section('content')
@php
    $user = auth()->user();

    $canEdit = $user && $user->hasPagePermission('logistics-tracking', 'can_edit');

    $isSuperAdmin = $user && $user->hasRole('Super Admin');
    $isAdmin = $user && $user->hasRole('Admin');
    $isLogistics = $user && $user->hasRole('Logistics');
    $isBranch = $user && $user->hasRole('Branch');
    $isOperator = $user && $user->hasRole('Operator');

    $canUpdateStatus = $canEdit && ($isSuperAdmin || $isAdmin || $isLogistics || $isBranch || $isOperator);

    $photos = $waybill->photos->sortByDesc('id');
@endphp

<div class="max-w-screen-2xl mx-auto">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Waybill Details</h1>
            <p class="text-sm text-gray-500 break-all">{{ $waybill->waybill_number }}</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('waybills.index') }}"
               class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800">
                Back
            </a>

            @if($canEdit)
                <a href="{{ route('waybills.edit', $waybill->id) }}"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800">
                    Edit
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Waybill Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Branch</p>
                        <p class="font-semibold break-words">
                            @if($waybill->branch)
                                Branch #{{ $waybill->branch->id }} — {{ $waybill->branch->address ?? 'No Address' }}
                            @else
                                Branch #{{ $waybill->branch_id ?? '-' }}
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Client Name</p>
                        <p class="font-semibold">{{ $waybill->client_name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Client Contact Number</p>
                        <p class="font-semibold">{{ $waybill->client_contact_number ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">POS Tracking Number</p>
                        <p class="font-semibold break-all">{{ $waybill->pos_tracking_number ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">POS Receipt Number</p>
                        <p class="font-semibold break-all">{{ $waybill->pos_receipt_number ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Waybill Number</p>
                        <p class="font-semibold break-all">{{ $waybill->waybill_number ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Reference Number</p>
                        <p class="font-semibold break-all">{{ $waybill->reference_number ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Status</p>
                        <p class="font-semibold">{{ $waybill->current_status ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Mode of Payment</p>
                        <p class="font-semibold">{{ $waybill->mode_of_payment ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Payment Status</p>
                        <p class="font-semibold">{{ $waybill->payment_status ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Total Amount</p>
                        <p class="font-semibold">{{ number_format((float) ($waybill->total_amount ?? 0), 2) }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Total Amount Paid</p>
                        <p class="font-semibold">{{ number_format((float) ($waybill->total_amount_paid ?? 0), 2) }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Balance</p>
                        <p class="font-semibold">{{ number_format((float) ($waybill->balance ?? 0), 2) }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Created By</p>
                        <p class="font-semibold">{{ $waybill->creator?->name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Created At</p>
                        <p class="font-semibold">
                            {{ optional($waybill->created_at)->timezone('Asia/Manila')->format('M d, Y h:i A') }}
                        </p>
                    </div>
                </div>

                @if($waybill->additional_information)
                    <div class="mt-4">
                        <p class="text-gray-500 text-sm">Additional Information</p>
                        <p class="font-semibold break-words">{{ $waybill->additional_information }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Logistics</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Logistics Type</p>
                        <p class="font-semibold">
                            {{ $waybill->logistics?->logistics_type === 'third_party_logistics' ? '3rd Party Logistics' : 'Impeccable Logistics System' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">3rd Party Provider</p>
                        <p class="font-semibold">{{ $waybill->logistics?->third_party_provider ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">3rd Party Waybill Number</p>
                        <p class="font-semibold">{{ $waybill->logistics?->third_party_waybill_number ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Logistics Accepted At</p>
                        <p class="font-semibold">
                            {{ $waybill->logistics?->logistics_accepted_at ? \Carbon\Carbon::parse($waybill->logistics->logistics_accepted_at)->timezone('Asia/Manila')->format('M d, Y h:i A') : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Main Hub Accepted At</p>
                        <p class="font-semibold">
                            {{ $waybill->logistics?->main_hub_accepted_at ? \Carbon\Carbon::parse($waybill->logistics->main_hub_accepted_at)->timezone('Asia/Manila')->format('M d, Y h:i A') : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Shoes Information</h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-[600px]">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="text-left py-3 px-4">Brand</th>
                                <th class="text-left py-3 px-4">Colorway</th>
                                <th class="text-left py-3 px-4">Service Needed</th>
                                <th class="text-left py-3 px-4">Price</th>
                                <th class="text-left py-3 px-4">Item Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($waybill->items as $item)
                                <tr class="border-b">
                                    <td class="py-3 px-4">{{ $item->shoe_brand }}</td>
                                    <td class="py-3 px-4">{{ $item->colorway ?? '-' }}</td>
                                    <td class="py-3 px-4">{{ $item->service?->name ?? '-' }}</td>
                                    <td class="py-3 px-4">{{ number_format((float) ($item->price ?? 0), 2) }}</td>
                                    <td class="py-3 px-4">{{ $item->item_status ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 px-4 text-center text-gray-500">
                                        No items found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Status History</h2>

                <div class="space-y-3">
                    @forelse($waybill->statusHistories->sortByDesc('id') as $history)
                        <div class="border rounded-lg p-3">
                            <p class="font-semibold break-words">{{ $history->status }}</p>
                            <p class="text-sm text-gray-500">
                                {{ optional($history->created_at)->timezone('Asia/Manila')->format('M d, Y h:i A') }}
                                @if($history->user)
                                    • {{ $history->user->name ?? 'User' }}
                                @endif
                            </p>

                            @if($history->remarks)
                                <p class="text-sm mt-1 break-words">{{ $history->remarks }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No status history yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            @if($canUpdateStatus && count($statuses))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Update Shoe Waybill Status</h2>

                    <form method="POST"
                          action="{{ route('waybills.status.update', $waybill->id) }}"
                          class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Status *</label>

                            <select name="status"
                                    class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                                    required>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" @selected($waybill->current_status == $status)>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Remarks</label>

                            <textarea name="remarks"
                                      rows="3"
                                      class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                                      placeholder="Optional remarks"></textarea>
                        </div>

                        <button type="submit"
                                class="w-full px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800">
                            Update Status
                        </button>
                    </form>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Uploaded Photos</h2>

                @if($photos->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-3">
                        @foreach($photos as $photo)
                            <a href="{{ asset('storage/' . $photo->file_path) }}"
                               target="_blank"
                               class="text-left border rounded-lg overflow-hidden block">
                                <img src="{{ asset($photo->file_path) }}"
                                     alt="Photo"
                                     class="w-full h-48 object-cover hover:opacity-90 transition">

                                <div class="p-2 text-xs text-gray-500">
                                    {{ ucwords(str_replace('_', ' ', $photo->type ?? 'photo')) }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No photo uploaded.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection