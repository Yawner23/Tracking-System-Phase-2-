@extends('layouts.backend')

@section('content')
@php
    $user = auth()->user();

    $canAssignBranch = $user->hasRole('Super Admin') || $user->hasRole('Admin');

    $branchDisplay = $user->branch
        ? 'Branch #' . $user->branch->id . ' — ' . ($user->branch->address ?? 'No Address')
        : 'Branch #' . ($user->branch_id ?? '-');

    $servicesForJs = ($services ?? collect())->map(function ($service) {
        return [
            'id' => $service->id,
            'name' => $service->name,
            'price' => (float) $service->price,
        ];
    })->values();
@endphp

<div class="max-w-screen-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Create New Waybill</h1>
            <p class="text-sm text-gray-500">
                Add waybill details, multiple shoes, payment, uploads, and logistics option.
            </p>
        </div>

        <a href="{{ route('waybills.index') }}"
           class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800">
            Back
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST"
              action="{{ route('waybills.store') }}"
              enctype="multipart/form-data"
              class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Branch *
                    </label>

                    @if($canAssignBranch)
                        <select name="branch_id"
                                class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                                required>
                            <option value="">-- Select Branch --</option>

                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>
                                    Branch #{{ $branch->id }} — {{ $branch->address ?? 'No Address' }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text"
                               value="{{ $branchDisplay }}"
                               class="w-full rounded-lg border-gray-200 bg-gray-100 text-gray-700"
                               readonly>

                        <input type="hidden" name="branch_id" value="{{ $user->branch_id }}">
                    @endif
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Client Name *
                </label>

                <input type="text"
                       name="client_name"
                       value="{{ old('client_name') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                       required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Client Contact Number
                    </label>

                    <input type="text"
                           name="client_contact_number"
                           value="{{ old('client_contact_number') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        POS Tracking Number
                    </label>

                    <input type="text"
                           name="pos_tracking_number"
                           value="{{ old('pos_tracking_number') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    POS Receipt Number
                </label>

                <input type="text"
                       name="pos_receipt_number"
                       value="{{ old('pos_receipt_number') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Waybill Number Automated
                    </label>

                    <input type="text"
                           value="{{ old('waybill_number', $generatedWaybill ?? '') }}"
                           class="w-full rounded-lg border-gray-200 bg-gray-100 text-gray-700"
                           readonly>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Reference Number Automated
                    </label>

                    <input type="text"
                           value="{{ old('reference_number', $generatedReference ?? '') }}"
                           class="w-full rounded-lg border-gray-200 bg-gray-100 text-gray-700"
                           readonly>
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-700 mb-3">Logistics</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Logistics Type *
                        </label>

                        <select name="logistics_type"
                                id="logistics_type"
                                class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                                required>
                            <option value="">-- Select Logistics --</option>

                            @foreach($logisticsTypes as $value => $label)
                                <option value="{{ $value }}" @selected(old('logistics_type') == $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="thirdPartyFields" class="{{ old('logistics_type') === 'third_party_logistics' ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                3rd Party Provider Name *
                            </label>

                            <input type="text"
                                   name="third_party_provider"
                                   value="{{ old('third_party_provider') }}"
                                   placeholder="JNT, LALAMOVE, LBC"
                                   class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                3rd Party Waybill Number *
                            </label>

                            <input type="text"
                                   name="third_party_waybill_number"
                                   value="{{ old('third_party_waybill_number') }}"
                                   placeholder="Enter third party waybill number"
                                   class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                        </div>
                    </div>
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold text-gray-700">Shoes Information</p>

                    <button type="button" id="addItemBtn"
                            class="px-3 py-1 rounded-md bg-gray-900 text-white hover:bg-gray-800 text-sm">
                        + Add Shoe
                    </button>
                </div>

                <div id="itemsWrap" class="space-y-4">
                    <div class="itemRow border rounded-lg p-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                    Brand *
                                </label>

                                <input type="text"
                                       name="items[0][shoe_brand]"
                                       value="{{ old('items.0.shoe_brand') }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                    Colorway
                                </label>

                                <input type="text"
                                       name="items[0][colorway]"
                                       value="{{ old('items.0.colorway') }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                    Service Needed *
                                </label>

                                <select name="items[0][service_id]"
                                        class="serviceSelect w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                                        required>
                                    <option value="">-- Select Service --</option>

                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" @selected(old('items.0.service_id') == $service->id)>
                                            {{ $service->name }} - {{ number_format($service->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                    Price
                                </label>

                                <input type="text"
                                       class="priceDisplay w-full rounded-lg border-gray-200 bg-gray-100 text-gray-700"
                                       readonly>
                            </div>
                        </div>

                        <div class="mt-3 flex justify-end">
                            <button type="button"
                                    class="removeItemBtn px-3 py-1 rounded-md bg-red-600 text-white hover:bg-red-700 text-sm"
                                    style="display:none;">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-sm text-gray-700 flex items-center justify-between">
                    <span class="font-semibold">Shoes Subtotal:</span>
                    <span class="font-bold" id="itemsSubtotalText">0.00</span>
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-700 mb-3">Payment</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Mode of Payment *
                        </label>

                        <select name="mode_of_payment"
                                class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                                required>
                            <option value="">-- Select --</option>
                            <option value="G-Cash" @selected(old('mode_of_payment') == 'G-Cash')>G-Cash</option>
                            <option value="Bank Transfer" @selected(old('mode_of_payment') == 'Bank Transfer')>Bank Transfer</option>
                            <option value="Cash" @selected(old('mode_of_payment') == 'Cash')>Cash</option>
                        </select>
                    </div>

                   <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Payment Status *
                        </label>

                        <input type="text"
                            value="Unpaid"
                            class="w-full rounded-lg border-gray-200 bg-gray-100 text-gray-700"
                            readonly>

                        <input type="hidden" name="payment_status" value="Unpaid">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Status *
                    </label>

                    <select name="current_status"
                            class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                            required>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(old('current_status', 'Ready for Shipment') == $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Additional Information
                </label>

                <textarea name="additional_information"
                          rows="4"
                          class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">{{ old('additional_information') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Upload Photos
                </label>

                <input type="file" name="job_photo[]" class="w-full" multiple accept=".jpg,.jpeg,.png">

                <p class="text-xs text-gray-500 mt-1">
                    You can upload multiple jpg/jpeg/png images, up to 2MB each.
                </p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800">
                    Save
                </button>

                <a href="{{ route('waybills.index') }}"
                   class="px-5 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const itemsWrap = document.getElementById('itemsWrap');
    const addItemBtn = document.getElementById('addItemBtn');
    const subtotalText = document.getElementById('itemsSubtotalText');
    const logisticsType = document.getElementById('logistics_type');
    const thirdPartyFields = document.getElementById('thirdPartyFields');

    const services = @json($servicesForJs);
    const serviceMap = {};

    services.forEach(service => {
        serviceMap[String(service.id)] = {
            name: service.name,
            price: parseFloat(service.price || 0)
        };
    });

    function toggleThirdPartyFields() {
        if (!logisticsType || !thirdPartyFields) return;

        if (logisticsType.value === 'third_party_logistics') {
            thirdPartyFields.classList.remove('hidden');
        } else {
            thirdPartyFields.classList.add('hidden');
        }
    }

    function refreshRemoveButtons() {
        const rows = itemsWrap.querySelectorAll('.itemRow');

        rows.forEach(row => {
            const btn = row.querySelector('.removeItemBtn');
            btn.style.display = rows.length > 1 ? 'inline-flex' : 'none';
        });
    }

    function renumberNames() {
        const rows = itemsWrap.querySelectorAll('.itemRow');

        rows.forEach((row, index) => {
            row.querySelectorAll('input, select').forEach(el => {
                if (!el.name) return;
                el.name = el.name.replace(/items\[\d+\]/, `items[${index}]`);
            });
        });
    }

    function setPriceDisplay(row) {
        const serviceSelect = row.querySelector('.serviceSelect');
        const priceDisplay = row.querySelector('.priceDisplay');

        if (!serviceSelect || !priceDisplay) return;

        const selectedId = serviceSelect.value;
        const selectedService = serviceMap[selectedId];

        priceDisplay.value = selectedService ? Number(selectedService.price).toFixed(2) : '0.00';
    }

    function calculateSubtotal() {
        let sum = 0;

        itemsWrap.querySelectorAll('.itemRow').forEach(row => {
            const serviceSelect = row.querySelector('.serviceSelect');
            const selectedId = serviceSelect ? serviceSelect.value : '';
            const selectedService = serviceMap[selectedId];

            sum += selectedService ? Number(selectedService.price) : 0;
        });

        subtotalText.textContent = sum.toFixed(2);
    }

    addItemBtn.addEventListener('click', () => {
        const first = itemsWrap.querySelector('.itemRow');
        const clone = first.cloneNode(true);

        clone.querySelectorAll('input').forEach(i => {
            if (i.classList.contains('priceDisplay')) {
                i.value = '0.00';
            } else {
                i.value = '';
            }
        });

        clone.querySelectorAll('select').forEach(sel => {
            sel.value = '';
        });

        itemsWrap.appendChild(clone);
        renumberNames();
        refreshRemoveButtons();
        calculateSubtotal();
    });

    itemsWrap.addEventListener('click', (e) => {
        if (e.target.classList.contains('removeItemBtn')) {
            e.target.closest('.itemRow').remove();
            renumberNames();
            refreshRemoveButtons();
            calculateSubtotal();
        }
    });

    itemsWrap.addEventListener('change', (e) => {
        if (e.target.classList.contains('serviceSelect')) {
            const row = e.target.closest('.itemRow');
            setPriceDisplay(row);
            calculateSubtotal();
        }
    });

    itemsWrap.querySelectorAll('.itemRow').forEach(row => {
        setPriceDisplay(row);
    });

    if (logisticsType) {
        logisticsType.addEventListener('change', toggleThirdPartyFields);
        toggleThirdPartyFields();
    }

    refreshRemoveButtons();
    calculateSubtotal();
});
</script>
@endsection