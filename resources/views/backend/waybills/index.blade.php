@extends('layouts.backend')

@section('content')
@php
    $user = auth()->user();
    $account = request()->route('account');

    $canView = $user && $user->hasPagePermission('logistics-tracking', 'can_view');
    $canCreate = $user && $user->hasPagePermission('logistics-tracking', 'can_create');
    $canEdit = $user && $user->hasPagePermission('logistics-tracking', 'can_edit');
    $canDelete = $user && $user->hasPagePermission('logistics-tracking', 'can_delete');

    $isSuperAdmin = $user && $user->hasRole('Super Admin');
    $isAdmin = $user && $user->hasRole('Admin');
    $isLogistics = $user && $user->hasRole('Logistics');

    $hasActions = $canView || $canEdit || $canDelete;
@endphp

<div class="max-w-screen-2xl mx-auto">

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Logistics Tracking Information
            </h1>
            <p class="text-sm text-gray-500">
                Viewable waybills and logistics status.
            </p>
        </div>

        @if($canCreate)
            <a href="{{ route('waybills.create', ['account' => $account]) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-700 text-sm font-semibold">
                <i class="ri-add-line mr-2"></i>
                Create New Waybill
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm min-w-[1450px]">

                <thead class="bg-gray-50 border-b">
                    <tr class="text-left text-gray-700">
                        <th class="py-3 px-4">Waybill Number</th>
                        <th class="py-3 px-4">Reference Number</th>
                        <th class="py-3 px-4">Branch</th>
                        <th class="py-3 px-4">Client Name</th>
                        <th class="py-3 px-4">POS Tracking</th>
                        <th class="py-3 px-4">Logistics Type</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Accepted At</th>
                        <th class="py-3 px-4">Payment</th>
                        <th class="py-3 px-4">Created At</th>

                        @if($hasActions)
                            <th class="py-3 px-4 text-right">Action</th>
                        @endif
                    </tr>
                </thead>

                <tbody>

                @forelse($waybills as $waybill)

                    @php
                        $logistics = $waybill->logistics;

                        $isThirdParty =
                            optional($logistics)->logistics_type === 'third_party_logistics';

                        $logisticsLabel = $isThirdParty
                            ? '3rd Party Logistics'
                            : 'Impeccable Logistics System';

                        $logisticsAcceptedAt =
                            optional($logistics)->logistics_accepted_at;

                        $mainHubAcceptedAt =
                            optional($logistics)->main_hub_accepted_at;

                        /*
                        |--------------------------------------------------------------------------
                        | BUTTON CONDITIONS
                        |--------------------------------------------------------------------------
                        */

                        // Logistics accepts first
                        $showLogisticsAcceptButton =
                            $isLogistics &&
                            $canEdit &&
                            !$isThirdParty &&
                            !$logisticsAcceptedAt &&
                            $waybill->current_status === 'Ready for Shipment';

                        // Admin / Super Admin accepts after logistics
                        $showMainHubAcceptButton =
                            ($isSuperAdmin || $isAdmin) &&
                            $canEdit &&
                            !$isThirdParty &&
                            $logisticsAcceptedAt &&
                            !$mainHubAcceptedAt &&
                            $waybill->current_status === 'In Transit to Main Hub';

                        // View/Edit/Delete only AFTER main hub accepted
                        $canAccessAfterHubAccepted =
                            $mainHubAcceptedAt || $isThirdParty;

                        $showViewButton =
                            $canView && $canAccessAfterHubAccepted;

                        $showEditButton =
                            $canEdit && $canAccessAfterHubAccepted;

                        $showDeleteButton =
                            $canDelete && $canAccessAfterHubAccepted;
                    @endphp

                    <tr class="border-b hover:bg-gray-50">

                        <td class="py-3 px-4 font-semibold">
                            {{ $waybill->waybill_number }}
                        </td>

                        <td class="py-3 px-4">
                            {{ $waybill->reference_number }}
                        </td>

                        <td class="py-3 px-4">
                            {{ optional($waybill->branch)->address ?? '-' }}
                        </td>

                        <td class="py-3 px-4">
                            {{ $waybill->client_name }}
                        </td>

                        <td class="py-3 px-4">
                            {{ $waybill->pos_tracking_number ?? '-' }}
                        </td>

                        <td class="py-3 px-4">
                            <span class="px-2 py-1 rounded-md text-xs font-medium
                                {{ $isThirdParty
                                    ? 'bg-yellow-100 text-yellow-800'
                                    : 'bg-blue-100 text-blue-800' }}">
                                {{ $logisticsLabel }}
                            </span>
                        </td>

                        <td class="py-3 px-4 font-medium">
                            {{ $waybill->current_status }}
                        </td>

                        <td class="py-3 px-4 whitespace-nowrap">

                            @if($mainHubAcceptedAt)
                                <span class="text-green-700 font-semibold">
                                    Main Hub Accepted
                                </span>

                            @elseif($logisticsAcceptedAt)
                                <span class="text-blue-700 font-semibold">
                                    Logistics Accepted
                                </span>

                            @else
                                <span class="text-gray-400">
                                    Pending
                                </span>
                            @endif

                        </td>

                        <td class="py-3 px-4">
                            {{ $waybill->payment_status }}
                        </td>

                        <td class="py-3 px-4 whitespace-nowrap">
                            {{ $waybill->created_at->timezone('Asia/Manila')->format('M d, Y h:i A') }}
                        </td>

                        @if($hasActions)
                        <td class="py-3 px-4">
                            <div class="flex justify-end gap-2 flex-wrap">

                                {{-- Logistics Accept --}}
                                @if($showLogisticsAcceptButton)
                                    <form method="POST"
                                          action="{{ route('waybills.accept-logistics', [
                                              'account' => $account,
                                              'waybill' => $waybill->id
                                          ]) }}">
                                        @csrf

                                        <button
                                            onclick="return confirm('Accept this waybill?')"
                                            class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                            Accept
                                        </button>
                                    </form>
                                @endif

                                {{-- Main Hub Accept --}}
                                @if($showMainHubAcceptButton)
                                    <form method="POST"
                                          action="{{ route('waybills.accept-main-hub', [
                                              'account' => $account,
                                              'waybill' => $waybill->id
                                          ]) }}">
                                        @csrf

                                        <button
                                            onclick="return confirm('Accept this at Main Hub?')"
                                            class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700">
                                            Accept Main Hub
                                        </button>
                                    </form>
                                @endif

                                {{-- View --}}
                                @if($showViewButton)
                                    <a href="{{ route('waybills.show', [
                                        'account' => $account,
                                        'waybill' => $waybill->id
                                    ]) }}"
                                       class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                                        View
                                    </a>
                                @endif

                                {{-- Edit --}}
                                @if($showEditButton)
                                    <a href="{{ route('waybills.edit', [
                                        'account' => $account,
                                        'waybill' => $waybill->id
                                    ]) }}"
                                       class="px-3 py-1 bg-gray-900 text-white rounded hover:bg-gray-800">
                                        Edit
                                    </a>
                                @endif

                                {{-- Delete --}}
                                @if($showDeleteButton)
                                    <form method="POST"
                                          action="{{ route('waybills.destroy', [
                                              'account' => $account,
                                              'waybill' => $waybill->id
                                          ]) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            onclick="return confirm('Delete this waybill?')"
                                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                            Delete
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </td>
                        @endif

                    </tr>

                @empty

                    <tr>
                        <td colspan="11"
                            class="py-6 text-center text-gray-500">
                            No waybills found.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        @if($waybills->hasPages())
            <div class="p-4 border-t">
                {{ $waybills->links() }}
            </div>
        @endif

    </div>

</div>
@endsection