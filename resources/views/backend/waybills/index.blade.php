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
            <h1 class="text-2xl font-bold text-gray-800">Logistics Tracking Information</h1>
            <p class="text-sm text-gray-500">
                Viewable waybills and logistics status.
            </p>
        </div>

        @if($canCreate)
            <a href="{{ route('waybills.create', ['account' => $account]) }}"
               class="inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-700 text-sm font-semibold">
                <i class="ri-add-line mr-2"></i>
                Create New Waybill
            </a>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET"
              action="{{ route('waybills.index', ['account' => $account]) }}"
              class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Search
                </label>

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Waybill, Reference, Client, Tracking"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Status
                </label>

                <select name="status"
                        class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                    <option value="">-- All Statuses --</option>

                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') == $status)>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Date From
                </label>

                <input type="date"
                       name="date_from"
                       value="{{ request('date_from') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Date To
                </label>

                <input type="date"
                       name="date_to"
                       value="{{ request('date_to') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800 w-full">
                    Filter
                </button>

                <a href="{{ route('waybills.index', ['account' => $account]) }}"
                   class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300 text-center w-full">
                    Reset
                </a>
            </div>
        </form>
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
                        <th class="py-3 px-4">3rd Party Provider</th>
                        <th class="py-3 px-4">3rd Party Waybill No.</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Accepted At</th>
                        <th class="py-3 px-4">Payment</th>
                        <th class="py-3 px-4">Created At</th>

                        @if($hasActions)
                            <th class="py-3 px-4 text-right w-[360px]">Action</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @forelse($waybills as $waybill)
                        @php
                            $logistics = $waybill->logistics;

                            $isThirdParty = optional($logistics)->logistics_type === 'third_party_logistics';

                            $logisticsLabel = $isThirdParty
                                ? '3rd Party Logistics'
                                : 'Impeccable Logistics System';

                            $logisticsAcceptedAt = optional($logistics)->logistics_accepted_at;
                            $mainHubAcceptedAt = optional($logistics)->main_hub_accepted_at;

                            $showLogisticsAcceptButton =
                                $isLogistics &&
                                $canEdit &&
                                !$isThirdParty &&
                                !$logisticsAcceptedAt &&
                                $waybill->current_status === 'Ready for Shipment';

                            $showViewButton =
                                $canView &&
                                (
                                    !$isLogistics ||
                                    ($isLogistics && $logisticsAcceptedAt)
                                );

                            $showEditButton =
                                $canEdit &&
                                (
                                    !$isLogistics ||
                                    ($isLogistics && $logisticsAcceptedAt)
                                );

                            $showDeleteButton =
                                $canDelete &&
                                (
                                    !$isLogistics ||
                                    ($isLogistics && $logisticsAcceptedAt)
                                );
                        @endphp

                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 font-semibold text-gray-800">
                                {{ $waybill->waybill_number ?? '-' }}
                            </td>

                            <td class="py-3 px-4">
                                {{ $waybill->reference_number ?? '-' }}
                            </td>

                            <td class="py-3 px-4">
                                @if($waybill->branch)
                                    Branch #{{ $waybill->branch->id }} — {{ $waybill->branch->address ?? 'No Address' }}
                                @else
                                    Branch #{{ $waybill->branch_id ?? '-' }}
                                @endif
                            </td>

                            <td class="py-3 px-4">
                                {{ $waybill->client_name ?? '-' }}
                            </td>

                            <td class="py-3 px-4">
                                {{ $waybill->pos_tracking_number ?? '-' }}
                            </td>

                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $isThirdParty ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $logisticsLabel }}
                                </span>
                            </td>

                            <td class="py-3 px-4">
                                {{ optional($logistics)->third_party_provider ?? '-' }}
                            </td>

                            <td class="py-3 px-4">
                                {{ optional($logistics)->third_party_waybill_number ?? '-' }}
                            </td>

                            <td class="py-3 px-4">
                                {{ $waybill->current_status ?? '-' }}
                            </td>

                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($mainHubAcceptedAt)
                                    <span class="text-green-700 font-semibold">
                                        Hub:
                                        {{ \Carbon\Carbon::parse($mainHubAcceptedAt)->timezone('Asia/Manila')->format('M d, Y h:i A') }}
                                    </span>
                                @elseif($logisticsAcceptedAt)
                                    <span class="text-blue-700 font-semibold">
                                        Logistics:
                                        {{ \Carbon\Carbon::parse($logisticsAcceptedAt)->timezone('Asia/Manila')->format('M d, Y h:i A') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Not accepted</span>
                                @endif
                            </td>

                            <td class="py-3 px-4">
                                {{ $waybill->payment_status ?? 'Unpaid' }}
                            </td>

                            <td class="py-3 px-4 whitespace-nowrap">
                                {{ optional($waybill->created_at)->timezone('Asia/Manila')->format('M d, Y h:i A') }}
                            </td>

                            @if($hasActions)
                                <td class="py-3 px-4">
                                    <div class="flex justify-end items-center gap-2 flex-nowrap whitespace-nowrap">
                                        @if($showLogisticsAcceptButton)
                                            <form method="POST"
                                                  action="{{ route('waybills.accept-logistics', ['account' => $account, 'waybill' => $waybill->id]) }}"
                                                  onsubmit="return confirm('Accept this waybill for logistics?')">
                                                @csrf

                                                <button type="submit"
                                                        class="inline-flex items-center justify-center px-3 py-1 rounded-md bg-green-600 text-white hover:bg-green-700">
                                                    Accept
                                                </button>
                                            </form>
                                        @endif

                                        @if($showViewButton)
                                            <a href="{{ route('waybills.show', ['account' => $account, 'waybill' => $waybill->id]) }}"
                                               class="inline-flex items-center justify-center px-3 py-1 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                                                View
                                            </a>
                                        @endif

                                        @if($showEditButton)
                                            <a href="{{ route('waybills.edit', ['account' => $account, 'waybill' => $waybill->id]) }}"
                                               class="inline-flex items-center justify-center px-3 py-1 rounded-md bg-gray-900 text-white hover:bg-gray-800">
                                                Edit
                                            </a>
                                        @endif

                                        @if($showDeleteButton)
                                            <form method="POST"
                                                  action="{{ route('waybills.destroy', ['account' => $account, 'waybill' => $waybill->id]) }}"
                                                  onsubmit="return confirm('Are you sure you want to delete this waybill?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="inline-flex items-center justify-center px-3 py-1 rounded-md bg-red-600 text-white hover:bg-red-700">
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
                            <td colspan="{{ $hasActions ? 13 : 12 }}"
                                class="py-6 px-4 text-center text-gray-500">
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