@extends('layouts.backend')

@section('content')
@php
    $user = auth()->user();

    $canAssignBranch = $user->hasRole('Super Admin') || $user->hasRole('Admin');

    $branchDisplay = $waybill->branch
        ? 'Branch #' . $waybill->branch->id . ' — ' . ($waybill->branch->address ?? 'No Address')
        : 'Branch #' . ($waybill->branch_id ?? '-');

    $servicesForJs = ($services ?? collect())->map(function ($service) {
        return [
            'id' => $service->id,
            'name' => $service->name,
            'price' => (float) $service->price,
        ];
    })->values();

    $latestTransaction = $waybill->transactions->sortByDesc('id')->first();
    $proofPhotos = $waybill->photos->where('type', 'proof_of_payment')->sortByDesc('id');
@endphp

<div class="max-w-screen-2xl mx-auto">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Waybill</h1>
            <p class="text-sm text-gray-500">
                Update waybill details, logistics, multiple shoes, payment, and uploads.
            </p>
        </div>

        <a href="{{ route('waybills.index') }}"
           class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 w-full sm:w-auto">
            Back
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="break-words">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <form method="POST"
              action="{{ route('waybills.update', $waybill->id) }}"
              enctype="multipart/form-data"
              class="space-y-5">
            @csrf
            @method('PUT')

            <div class="text-sm text-gray-500 break-words">
                Waybill:
                <span class="font-semibold text-gray-800 break-all">
                    {{ $waybill->waybill_number ?? '-' }}
                </span>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Branch *</label>

                @if($canAssignBranch)
                    <select name="branch_id"
                            class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                            required>
                        <option value="">-- Select Branch --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(old('branch_id', $waybill->branch_id) == $branch->id)>
                                Branch #{{ $branch->id }} — {{ $branch->address ?? 'No Address' }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text"
                           value="{{ $branchDisplay }}"
                           class="w-full rounded-lg border-gray-200 bg-gray-100 text-gray-700"
                           readonly>
                    <input type="hidden" name="branch_id" value="{{ $waybill->branch_id }}">
                @endif
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Client Name *</label>
                <input type="text"
                       name="client_name"
                       value="{{ old('client_name', $waybill->client_name) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                       required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Client Contact Number</label>
                    <input type="text"
                           name="client_contact_number"
                           value="{{ old('client_contact_number', $waybill->client_contact_number) }}"
                           class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">POS Tracking Number</label>
                    <input type="text"
                           name="pos_tracking_number"
                           value="{{ old('pos_tracking_number', $waybill->pos_tracking_number) }}"
                           class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-700 mb-3">Logistics</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Logistics Type</label>
                        <select name="logistics_type"
                                id="logistics_type"
                                class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                            @foreach($logisticsTypes as $value => $label)
                                <option value="{{ $value }}"
                                    @selected(old('logistics_type', optional($waybill->logistics)->logistics_type) == $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="thirdPartyFields" class="{{ old('logistics_type', optional($waybill->logistics)->logistics_type) === 'third_party_logistics' ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">3rd Party Provider</label>
                            <input type="text"
                                   name="third_party_provider"
                                   value="{{ old('third_party_provider', optional($waybill->logistics)->third_party_provider) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                                   placeholder="JNT, LALAMOVE, LBC">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">3rd Party Waybill Number</label>
                            <input type="text"
                                   name="third_party_waybill_number"
                                   value="{{ old('third_party_waybill_number', optional($waybill->logistics)->third_party_waybill_number) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                                   placeholder="Enter third party waybill number">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status *</label>
                <select name="current_status"
                        id="currentStatusSelect"
                        class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                        required>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(old('current_status', $waybill->current_status) == $status)>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="border rounded-lg p-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-3">
                    <p class="text-sm font-semibold text-gray-700">Shoes Information</p>

                    <button type="button" id="addItemBtn"
                            class="px-3 py-2 rounded-md bg-gray-900 text-white hover:bg-gray-800 text-sm w-full sm:w-auto">
                        + Add Shoe
                    </button>
                </div>

                <div id="itemsWrap" class="space-y-4">
                    @php
                        $oldItems = old('items');

                        if (is_array($oldItems)) {
                            $itemsForForm = $oldItems;
                        } else {
                            $itemsForForm = $waybill->items->map(function ($item) {
                                return [
                                    'shoe_brand' => $item->shoe_brand,
                                    'colorway' => $item->colorway,
                                    'service_id' => $item->service_id,
                                ];
                            })->toArray();
                        }

                        if (empty($itemsForForm)) {
                            $itemsForForm = [[]];
                        }
                    @endphp

                    @foreach($itemsForForm as $i => $item)
                        <div class="itemRow border rounded-lg p-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Brand *</label>
                                    <input type="text"
                                           name="items[{{ $i }}][shoe_brand]"
                                           value="{{ $item['shoe_brand'] ?? '' }}"
                                           class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Colorway</label>
                                    <input type="text"
                                           name="items[{{ $i }}][colorway]"
                                           value="{{ $item['colorway'] ?? '' }}"
                                           class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Service Needed *</label>
                                    <select name="items[{{ $i }}][service_id]"
                                            class="serviceSelect w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                                            required>
                                        <option value="">-- Select Service --</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}"
                                                @selected((string)($item['service_id'] ?? '') === (string)$service->id)>
                                                {{ $service->name }} - {{ number_format($service->price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Price</label>
                                    <input type="text"
                                           class="priceDisplay w-full rounded-lg border-gray-200 bg-gray-100 text-gray-700"
                                           readonly>
                                </div>
                            </div>

                            <div class="mt-3 flex justify-end">
                                <button type="button"
                                        class="removeItemBtn px-3 py-1 rounded-md bg-red-600 text-white hover:bg-red-700 text-sm">
                                    Remove
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 text-sm text-gray-700 flex items-center justify-between gap-3">
                    <span class="font-semibold">Shoes Subtotal:</span>
                    <span class="font-bold" id="itemsSubtotalText">0.00</span>
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-700 mb-3">Payment</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Mode of Payment *</label>
                        <select name="mode_of_payment"
                                class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                                required>
                            <option value="">-- Select --</option>
                            <option value="G-Cash" @selected(old('mode_of_payment', $waybill->mode_of_payment) == 'G-Cash')>G-Cash</option>
                            <option value="Bank Transfer" @selected(old('mode_of_payment', $waybill->mode_of_payment) == 'Bank Transfer')>Bank Transfer</option>
                            <option value="Cash" @selected(old('mode_of_payment', $waybill->mode_of_payment) == 'Cash')>Cash</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Payment Status *</label>
                        <select name="payment_status"
                                class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900"
                                required>
                            <option value="Unpaid" @selected(old('payment_status', $waybill->payment_status) == 'Unpaid')>Unpaid</option>
                            <option value="Paid" @selected(old('payment_status', $waybill->payment_status) == 'Paid')>Paid</option>
                            <option value="Partial" @selected(old('payment_status', $waybill->payment_status) == 'Partial')>Partial</option>
                            <option value="Refund" @selected(old('payment_status', $waybill->payment_status) == 'Refund')>Refund</option>
                            <option value="Cancelled" @selected(old('payment_status', $waybill->payment_status) == 'Cancelled')>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Adjustment</label>
                        <input id="adjustmentInput"
                               type="number"
                               step="0.01"
                               name="adjustment"
                               value="{{ old('adjustment', $latestTransaction?->adjustment ?? 0) }}"
                               class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Refund Amount</label>
                        <input id="refundInput"
                               type="number"
                               step="0.01"
                               name="refund_amount"
                               value="{{ old('refund_amount', $latestTransaction?->refund_amount ?? 0) }}"
                               class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                    </div>
                </div>

                <div class="mt-4 text-sm text-gray-700 flex items-center justify-between gap-3">
                    <span class="font-semibold">Net Amount:</span>
                    <span class="font-bold" id="netAmountText">0.00</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Additional Information</label>
                <textarea name="additional_information"
                          rows="4"
                          class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">{{ old('additional_information', $waybill->additional_information) }}</textarea>
            </div>

            <div id="proofOfPaymentSection"
                 class="border rounded-lg p-4 {{ old('current_status', $waybill->current_status) === 'Repair Completed' ? '' : 'hidden' }}">
                <p class="text-sm font-semibold text-gray-700 mb-3">Proof of Payment</p>

                @if($proofPhotos->count())
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 mb-2">Uploaded Proof of Payment:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($proofPhotos as $photo)
                                <img src="{{ asset($photo->file_path) }}"
                                     alt="Proof of Payment"
                                     class="w-full h-40 object-cover rounded-lg border">
                            @endforeach
                        </div>
                    </div>
                @endif

                <label class="block text-sm font-semibold text-gray-700 mb-1">Upload Proof of Payment</label>
                <input type="file" name="proof_of_payment[]" class="w-full" multiple accept=".jpg,.jpeg,.png">
            </div>

            <div class="border rounded-lg p-4">
                <p class="text-sm font-semibold text-gray-700 mb-3">Photo</p>

                @if($waybill->photos->where('type', 'job_photo')->count())
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 mb-2">Current Photos:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($waybill->photos->where('type', 'job_photo')->sortByDesc('id') as $photo)
                                <img src="{{ asset('storage/' . $photo->file_path) }}"
                                     alt="Waybill photo"
                                     class="w-full h-40 object-cover rounded-lg border">
                            @endforeach
                        </div>
                    </div>
                @endif

                <label class="block text-sm font-semibold text-gray-700 mb-1">Add Photos</label>
                <input type="file" name="job_photo[]" class="w-full" multiple accept=".jpg,.jpeg,.png">
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800 w-full sm:w-auto">
                    Update
                </button>

                <a href="{{ route('waybills.index') }}"
                   class="px-5 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 text-center w-full sm:w-auto">
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
    const currentStatusSelect = document.getElementById('currentStatusSelect');
    const proofOfPaymentSection = document.getElementById('proofOfPaymentSection');
    const logisticsType = document.getElementById('logistics_type');
    const thirdPartyFields = document.getElementById('thirdPartyFields');

    const subtotalText = document.getElementById('itemsSubtotalText');
    const adjustmentInput = document.getElementById('adjustmentInput');
    const refundInput = document.getElementById('refundInput');
    const netAmountText = document.getElementById('netAmountText');

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

        const adjustment = parseFloat(adjustmentInput?.value || 0);
        const refund = parseFloat(refundInput?.value || 0);
        const net = sum + adjustment - refund;

        subtotalText.textContent = sum.toFixed(2);
        netAmountText.textContent = net.toFixed(2);
    }

    function toggleProofOfPayment() {
        if (!currentStatusSelect || !proofOfPaymentSection) return;

        if (currentStatusSelect.value === 'Repair Completed') {
            proofOfPaymentSection.classList.remove('hidden');
        } else {
            proofOfPaymentSection.classList.add('hidden');
        }
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
            if (sel.classList.contains('serviceSelect')) {
                sel.value = '';
            }
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

    if (currentStatusSelect) {
        currentStatusSelect.addEventListener('change', toggleProofOfPayment);
    }

    if (adjustmentInput) {
        adjustmentInput.addEventListener('input', calculateSubtotal);
    }

    if (refundInput) {
        refundInput.addEventListener('input', calculateSubtotal);
    }

    if (logisticsType) {
        logisticsType.addEventListener('change', toggleThirdPartyFields);
        toggleThirdPartyFields();
    }

    itemsWrap.querySelectorAll('.itemRow').forEach(row => {
        setPriceDisplay(row);
    });

    refreshRemoveButtons();
    calculateSubtotal();
    toggleProofOfPayment();
});
</script>
@endsection