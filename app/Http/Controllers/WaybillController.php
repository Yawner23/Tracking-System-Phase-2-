<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Service;
use App\Models\Waybill;
use App\Models\WaybillItem;
use App\Models\WaybillLogistic;
use App\Models\WaybillPhoto;
use App\Models\WaybillRecord;
use App\Models\WaybillStatusHistory;
use App\Models\WaybillTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WaybillController extends Controller
{
    private array $statuses = [
        'Received in Branch',
        'Ready for Shipment',
        'In Transit to Main Hub',
        'Received at Main Hub',
        'Sent for Repair',
        'Under Repair',
        'Repair Completed',
        'In Transit to Branch Hub',
        'Received from Logistics',
        'Returned to Branch',
        'Claimed by Customer',
    ];

    private array $createStatuses = [
        'Ready for Shipment',
        'In Transit to Main Hub',
        'Sent for Repair',
    ];

    private array $branchEditStatuses = [
        'Received in Branch',
        'Claimed by Customer',
    ];

    private array $logisticsEditStatuses = [
        'In Transit to Main Hub',
        'Received at Main Hub',
        'Returned to Branch',
    ];

    private array $branchFilterStatuses = [
        'Received in Branch',
        'Ready for Shipment',
        'In Transit to Main Hub',
        'Received at Main Hub',
        'Sent for Repair',
        'Under Repair',
        'Repair Completed',
        'Returned to Branch',
        'Claimed by Customer',
    ];

    private array $logisticsFilterStatuses = [
        'Ready for Shipment',
        'In Transit to Main Hub',
        'Received at Main Hub',
        'Sent for Repair',
        'Under Repair',
        'Repair Completed',
        'Returned to Branch',
        'Claimed by Customer',
    ];

    private array $itemStatuses = [
        'Completed',
        'Back Job',
        'Canceled',
        'Need Further Servicing',
    ];

    private function authorizeWaybillPermission(string $permission): void
    {
        $user = auth()->user();

        if (!$user || !$user->hasPagePermission('logistics-tracking', $permission)) {
            abort(403, 'Unauthorized.');
        }
    }

    private function createStatusOptions(): array
    {
        return $this->createStatuses;
    }

    private function filterStatusOptions(): array
    {
        $user = auth()->user();

        if ($user && $user->hasRole('Branch')) {
            return $this->branchFilterStatuses;
        }

        if ($user && $user->hasRole('Logistics')) {
            return $this->logisticsFilterStatuses;
        }

        return $this->statuses;
    }

    private function editStatusOptions(): array
    {
        $user = auth()->user();

        if ($user && $user->hasRole('Branch')) {
            return $this->branchEditStatuses;
        }

        if ($user && $user->hasRole('Logistics')) {
            return $this->logisticsEditStatuses;
        }

        return $this->statuses;
    }

    private function itemStatusOptions(): array
    {
        return $this->itemStatuses;
    }

    private function logisticsTypes(): array
    {
        return [
            'impeccable_logistics_system' => 'Impeccable Logistics System',
            'third_party_logistics' => '3rd Party Logistics',
        ];
    }

    private function generateWaybillNumber(): string
    {
        $date = now()->format('Ymd');

        $latest = WaybillRecord::whereDate('created_at', now()->toDateString())
            ->where('waybill_number', 'like', "WB-{$date}-%")
            ->latest('id')
            ->first();

        $nextNumber = 1;

        if ($latest && $latest->waybill_number) {
            $lastPart = (int) str_replace("WB-{$date}-", '', $latest->waybill_number);
            $nextNumber = $lastPart + 1;
        }

        return 'WB-' . $date . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    private function generateReferenceNumber(): string
    {
        $date = now()->format('Ymd');

        $latest = WaybillRecord::whereDate('created_at', now()->toDateString())
            ->where('reference_number', 'like', "REF-{$date}-%")
            ->latest('id')
            ->first();

        $nextNumber = 1;

        if ($latest && $latest->reference_number) {
            $lastPart = (int) str_replace("REF-{$date}-", '', $latest->reference_number);
            $nextNumber = $lastPart + 1;
        }

        return 'REF-' . $date . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    private function calculateItemsTotal(array $items): float
    {
        $total = 0;

        foreach ($items as $item) {
            $service = Service::find($item['service_id']);

            if ($service) {
                $total += (float) $service->price;
            }
        }

        return $total;
    }

    public function index(string $account, Request $request)
    {
        $this->authorizeWaybillPermission('can_view');

        $user = auth()->user();

        $query = WaybillRecord::with([
            'branch',
            'creator',
            'waybill',
            'items.service',
            'logistics',
        ])
            ->whereHas('waybill')
            ->latest();

        if ($user->hasRole('Branch') && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('waybill_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('pos_tracking_number', 'like', "%{$search}%")
                    ->orWhereHas('logistics', function ($logisticsQuery) use ($search) {
                        $logisticsQuery->where('third_party_provider', 'like', "%{$search}%")
                            ->orWhere('third_party_waybill_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $waybills = $query->paginate(10)->withQueryString();
        $statuses = $this->filterStatusOptions();

        return view('backend.waybills.index', compact('waybills', 'statuses'));
    }

    public function create(string $account)
    {
        $this->authorizeWaybillPermission('can_create');

        $branches = Branch::orderBy('address')->get();
        $services = Service::orderBy('name')->get();

        $generatedWaybill = $this->generateWaybillNumber();
        $generatedReference = $this->generateReferenceNumber();

        $statuses = $this->createStatusOptions();
        $logisticsTypes = $this->logisticsTypes();

        return view('backend.waybills.create', compact(
            'branches',
            'services',
            'generatedWaybill',
            'generatedReference',
            'statuses',
            'logisticsTypes'
        ));
    }

    public function store(Request $request, string $account)
    {
        $this->authorizeWaybillPermission('can_create');

        $user = auth()->user();

        $validated = $request->validate([
            'branch_id' => [
                $user->branch_id ? 'nullable' : 'required',
                'nullable',
                'exists:branches,id',
            ],
            'client_name' => ['required', 'string', 'max:255'],
            'client_contact_number' => ['nullable', 'string', 'max:30'],
            'pos_receipt_number' => ['nullable', 'string', 'max:255'],
            'pos_tracking_number' => ['nullable', 'string', 'max:255'],
            'logistics_type' => ['required', Rule::in(array_keys($this->logisticsTypes()))],
            'third_party_provider' => ['nullable', 'string', 'max:255', 'required_if:logistics_type,third_party_logistics'],
            'third_party_waybill_number' => ['nullable', 'string', 'max:255', 'required_if:logistics_type,third_party_logistics'],
            'mode_of_payment' => ['required', Rule::in(['G-Cash', 'Bank Transfer', 'Cash'])],
            'payment_status' => ['required', Rule::in(['Unpaid', 'Paid', 'Partial', 'Refund', 'Cancelled'])],
            'current_status' => ['required', Rule::in($this->createStatusOptions())],
            'additional_information' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.shoe_brand' => ['required', 'string', 'max:255'],
            'items.*.colorway' => ['nullable', 'string', 'max:255'],
            'items.*.service_id' => ['required', 'exists:services,id'],

            'job_photo' => ['nullable', 'array'],
            'job_photo.*' => ['file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        DB::transaction(function () use ($validated, $request, $user) {
            $branchId = $user->branch_id ?: $validated['branch_id'];

            $waybillNumber = $this->generateWaybillNumber();
            $referenceNumber = $this->generateReferenceNumber();

            $totalAmount = $this->calculateItemsTotal($validated['items']);

            $paymentStatus = $validated['payment_status'];
            $totalAmountPaid = $paymentStatus === 'Paid' ? $totalAmount : 0;
            $balance = max($totalAmount - $totalAmountPaid, 0);

            $record = WaybillRecord::create([
                'branch_id' => $branchId,
                'created_by' => $user->id,
                'waybill_number' => $waybillNumber,
                'reference_number' => $referenceNumber,
                'client_name' => $validated['client_name'],
                'client_contact_number' => $validated['client_contact_number'] ?? null,
                'pos_receipt_number' => $validated['pos_receipt_number'] ?? null,
                'pos_tracking_number' => $validated['pos_tracking_number'] ?? null,
                'additional_information' => $validated['additional_information'] ?? null,
                'current_status' => $validated['current_status'],
                'mode_of_payment' => $validated['mode_of_payment'],
                'payment_status' => $paymentStatus,
                'total_amount' => $totalAmount,
                'total_amount_paid' => $totalAmountPaid,
                'balance' => $balance,
            ]);

            Waybill::create([
                'waybill_record_id' => $record->id,
            ]);

            foreach ($validated['items'] as $item) {
                $service = Service::findOrFail($item['service_id']);

                WaybillItem::create([
                    'waybill_record_id' => $record->id,
                    'service_id' => $service->id,
                    'shoe_brand' => $item['shoe_brand'],
                    'colorway' => $item['colorway'] ?? null,
                    'item_status' => null,
                    'price' => $service->price,
                ]);
            }

            WaybillLogistic::create([
                'waybill_record_id' => $record->id,
                'logistics_type' => $validated['logistics_type'],
                'third_party_provider' => $validated['third_party_provider'] ?? null,
                'third_party_waybill_number' => $validated['third_party_waybill_number'] ?? null,
            ]);

            WaybillTransaction::create([
                'waybill_record_id' => $record->id,
                'amount' => $totalAmount,
                'adjustment' => 0,
                'refund_amount' => 0,
                'payment_status' => $paymentStatus === 'Cancelled' ? 'Cancel' : $paymentStatus,
                'created_by' => $user->id,
                'remarks' => 'Initial waybill transaction',
            ]);

            WaybillStatusHistory::create([
                'waybill_record_id' => $record->id,
                'user_id' => $user->id,
                'status' => $validated['current_status'],
                'remarks' => 'Waybill created',
            ]);

            if ($request->hasFile('job_photo')) {
                foreach ($request->file('job_photo') as $photo) {
                    $path = $photo->store('uploads/waybills/photos', 'public');

                    WaybillPhoto::create([
                        'waybill_record_id' => $record->id,
                        'type' => 'job_photo',
                        'file_path' => $path,
                        'uploaded_by' => $user->id,
                    ]);
                }
            }
        });

        return redirect()
            ->route('waybills.index', ['account' => $account])
            ->with('success', 'Waybill created successfully.');
    }

    public function show(string $account, string $waybill)
    {
        $this->authorizeWaybillPermission('can_view');

        $waybill = WaybillRecord::with([
            'branch',
            'creator',
            'waybill',
            'items.service',
            'photos.uploader',
            'logistics.logisticsAcceptedBy',
            'logistics.mainHubAcceptedBy',
            'transactions.payments.receiver',
            'statusHistories.user',
        ])
            ->whereHas('waybill')
            ->findOrFail($waybill);

        $user = auth()->user();

        if ($user->hasRole('Branch') && $user->branch_id && $waybill->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized.');
        }

        if ($user->hasRole('Logistics') && !$waybill->logistics?->logistics_accepted_at) {
            abort(403, 'You must accept this waybill before viewing it.');
        }

        $statuses = $this->editStatusOptions();
        $itemStatuses = $this->itemStatusOptions();

        return view('backend.waybills.show', compact(
            'waybill',
            'statuses',
            'itemStatuses'
        ));
    }

    public function edit(string $account, string $waybill)
    {
        $this->authorizeWaybillPermission('can_edit');

        $waybill = WaybillRecord::with([
            'branch',
            'waybill',
            'items.service',
            'photos',
            'logistics',
            'transactions',
        ])
            ->whereHas('waybill')
            ->findOrFail($waybill);

        $user = auth()->user();

        if ($user->hasRole('Branch') && $user->branch_id && $waybill->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized.');
        }

        if ($user->hasRole('Logistics') && !$waybill->logistics?->logistics_accepted_at) {
            abort(403, 'You must accept this waybill before editing it.');
        }

        $branches = Branch::orderBy('address')->get();
        $services = Service::orderBy('name')->get();

        $statuses = $this->editStatusOptions();
        $logisticsTypes = $this->logisticsTypes();
        $itemStatuses = $this->itemStatusOptions();

        return view('backend.waybills.edit', compact(
            'waybill',
            'branches',
            'services',
            'statuses',
            'logisticsTypes',
            'itemStatuses'
        ));
    }

    public function update(Request $request, string $account, string $waybill)
    {
        $this->authorizeWaybillPermission('can_edit');

        $waybill = WaybillRecord::with(['items', 'logistics', 'transactions'])
            ->whereHas('waybill')
            ->findOrFail($waybill);

        $user = auth()->user();

        if ($user->hasRole('Branch') && $user->branch_id && $waybill->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized.');
        }

        if ($user->hasRole('Logistics') && !$waybill->logistics?->logistics_accepted_at) {
            abort(403, 'You must accept this waybill before editing it.');
        }

        $validated = $request->validate([
            'branch_id' => [
                $user->branch_id ? 'nullable' : 'required',
                'nullable',
                'exists:branches,id',
            ],
            'client_name' => ['required', 'string', 'max:255'],
            'client_contact_number' => ['nullable', 'string', 'max:30'],
            'pos_receipt_number' => ['nullable', 'string', 'max:255'],
            'pos_tracking_number' => ['nullable', 'string', 'max:255'],
            'logistics_type' => ['required', Rule::in(array_keys($this->logisticsTypes()))],
            'third_party_provider' => ['nullable', 'string', 'max:255', 'required_if:logistics_type,third_party_logistics'],
            'third_party_waybill_number' => ['nullable', 'string', 'max:255', 'required_if:logistics_type,third_party_logistics'],
            'mode_of_payment' => ['required', Rule::in(['G-Cash', 'Bank Transfer', 'Cash'])],
            'payment_status' => ['required', Rule::in(['Unpaid', 'Paid', 'Partial', 'Refund', 'Cancelled'])],
            'current_status' => ['required', Rule::in($this->editStatusOptions())],
            'additional_information' => ['nullable', 'string'],
            'adjustment' => ['nullable', 'numeric', 'min:0'],
            'refund_amount' => ['nullable', 'numeric', 'min:0'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.shoe_brand' => ['required', 'string', 'max:255'],
            'items.*.colorway' => ['nullable', 'string', 'max:255'],
            'items.*.service_id' => ['required', 'exists:services,id'],
            'items.*.item_status' => ['nullable', Rule::in($this->itemStatusOptions())],

            'job_photo' => ['nullable', 'array'],
            'job_photo.*' => ['file', 'mimes:jpg,jpeg,png', 'max:2048'],

            'proof_of_payment' => ['nullable', 'array'],
            'proof_of_payment.*' => ['file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        DB::transaction(function () use ($validated, $request, $waybill, $user) {
            $branchId = $user->branch_id ?: $validated['branch_id'];

            $oldStatus = $waybill->current_status;

            $totalAmount = $this->calculateItemsTotal($validated['items']);
            $adjustment = (float) ($validated['adjustment'] ?? 0);
            $refundAmount = (float) ($validated['refund_amount'] ?? 0);
            $netAmount = max($totalAmount + $adjustment - $refundAmount, 0);

            $paymentStatus = $validated['payment_status'];
            $totalAmountPaid = $paymentStatus === 'Paid' ? $netAmount : 0;
            $balance = max($netAmount - $totalAmountPaid, 0);

            $waybill->update([
                'branch_id' => $branchId,
                'client_name' => $validated['client_name'],
                'client_contact_number' => $validated['client_contact_number'] ?? null,
                'pos_receipt_number' => $validated['pos_receipt_number'] ?? null,
                'pos_tracking_number' => $validated['pos_tracking_number'] ?? null,
                'additional_information' => $validated['additional_information'] ?? null,
                'current_status' => $validated['current_status'],
                'mode_of_payment' => $validated['mode_of_payment'],
                'payment_status' => $paymentStatus,
                'total_amount' => $netAmount,
                'total_amount_paid' => $totalAmountPaid,
                'balance' => $balance,
            ]);

            $waybill->items()->delete();

            foreach ($validated['items'] as $item) {
                $service = Service::findOrFail($item['service_id']);

                WaybillItem::create([
                    'waybill_record_id' => $waybill->id,
                    'service_id' => $service->id,
                    'shoe_brand' => $item['shoe_brand'],
                    'colorway' => $item['colorway'] ?? null,
                    'item_status' => $item['item_status'] ?? null,
                    'price' => $service->price,
                ]);
            }

            WaybillLogistic::updateOrCreate(
                ['waybill_record_id' => $waybill->id],
                [
                    'logistics_type' => $validated['logistics_type'],
                    'third_party_provider' => $validated['third_party_provider'] ?? null,
                    'third_party_waybill_number' => $validated['third_party_waybill_number'] ?? null,
                ]
            );

            $transaction = $waybill->transactions()->latest()->first();

            $transactionData = [
                'amount' => $netAmount,
                'adjustment' => $adjustment,
                'refund_amount' => $refundAmount,
                'payment_status' => $paymentStatus === 'Cancelled' ? 'Cancel' : $paymentStatus,
                'remarks' => 'Waybill transaction updated',
            ];

            if ($transaction) {
                $transaction->update($transactionData);
            } else {
                WaybillTransaction::create(array_merge($transactionData, [
                    'waybill_record_id' => $waybill->id,
                    'created_by' => $user->id,
                ]));
            }

            if ($oldStatus !== $validated['current_status']) {
                WaybillStatusHistory::create([
                    'waybill_record_id' => $waybill->id,
                    'user_id' => $user->id,
                    'status' => $validated['current_status'],
                    'remarks' => 'Waybill status updated',
                ]);
            }

            if ($request->hasFile('job_photo')) {
                foreach ($request->file('job_photo') as $photo) {
                    $path = $photo->store('uploads/waybills/photos', 'public');

                    WaybillPhoto::create([
                        'waybill_record_id' => $waybill->id,
                        'type' => 'job_photo',
                        'file_path' => $path,
                        'uploaded_by' => $user->id,
                    ]);
                }
            }

            if ($request->hasFile('proof_of_payment')) {
                foreach ($request->file('proof_of_payment') as $photo) {
                    $path = $photo->store('uploads/waybills/payment_proofs', 'public');

                    WaybillPhoto::create([
                        'waybill_record_id' => $waybill->id,
                        'type' => 'proof_of_payment',
                        'file_path' => $path,
                        'uploaded_by' => $user->id,
                    ]);
                }
            }
        });

        return redirect()
            ->route('waybills.index', ['account' => $account])
            ->with('success', 'Waybill updated successfully.');
    }

    public function updateStatus(Request $request, string $account, string $waybill)
    {
        $this->authorizeWaybillPermission('can_edit');

        $statusOptions = $this->editStatusOptions();

        $validated = $request->validate([
            'status' => ['required', Rule::in($statusOptions)],
            'remarks' => ['nullable', 'string'],
        ]);

        $user = auth()->user();

        $waybill = WaybillRecord::with(['waybill', 'logistics'])
            ->whereHas('waybill')
            ->findOrFail($waybill);

        if ($user->hasRole('Branch') && $user->branch_id && $waybill->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized.');
        }

        if ($user->hasRole('Logistics') && !$waybill->logistics?->logistics_accepted_at) {
            abort(403, 'You must accept this waybill before updating it.');
        }

        DB::transaction(function () use ($waybill, $validated, $user) {
            $waybill->update([
                'current_status' => $validated['status'],
            ]);

            WaybillStatusHistory::create([
                'waybill_record_id' => $waybill->id,
                'user_id' => $user->id,
                'status' => $validated['status'],
                'remarks' => $validated['remarks'] ?? 'Status updated.',
            ]);
        });

        return redirect()
            ->route('waybills.show', [
                'account' => $account,
                'waybill' => $waybill->id,
            ])
            ->with('success', 'Waybill status updated successfully.');
    }

    public function acceptLogistics(string $account, string $waybill)
    {
        $this->authorizeWaybillPermission('can_edit');

        $user = auth()->user();

        if (!$user->hasRole('Logistics')) {
            abort(403, 'Only logistics users can accept waybills.');
        }

        $waybill = WaybillRecord::with(['waybill', 'logistics'])
            ->whereHas('waybill')
            ->findOrFail($waybill);

        if (!$waybill->logistics) {
            return redirect()
                ->route('waybills.index', ['account' => $account])
                ->with('success', 'This waybill has no logistics record.');
        }

        if ($waybill->logistics->logistics_type === 'third_party_logistics') {
            return redirect()
                ->route('waybills.index', ['account' => $account])
                ->with('success', 'Third-party logistics waybills do not need logistics acceptance.');
        }

        if ($waybill->logistics->logistics_accepted_at) {
            return redirect()
                ->route('waybills.index', ['account' => $account])
                ->with('success', 'This waybill was already accepted by logistics.');
        }

        DB::transaction(function () use ($waybill, $user) {
            $waybill->logistics->update([
                'logistics_accepted_by' => $user->id,
                'logistics_accepted_at' => now(),
            ]);

            $waybill->update([
                'current_status' => 'In Transit to Main Hub',
            ]);

            WaybillStatusHistory::create([
                'waybill_record_id' => $waybill->id,
                'user_id' => $user->id,
                'status' => 'In Transit to Main Hub',
                'remarks' => 'Accepted by logistics.',
            ]);
        });

        return redirect()
            ->route('waybills.index', ['account' => $account])
            ->with('success', 'Waybill accepted by logistics successfully.');
    }

    public function acceptMainHub(string $account, string $waybill)
    {
        $this->authorizeWaybillPermission('can_edit');

        $user = auth()->user();

        $waybill = WaybillRecord::with(['waybill', 'logistics'])
            ->whereHas('waybill')
            ->findOrFail($waybill);

        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin')) {
            abort(403, 'Unauthorized.');
        }

        if (!$waybill->logistics) {
            return redirect()
                ->route('waybills.index', ['account' => $account])
                ->with('success', 'This waybill has no logistics record.');
        }

        if ($waybill->logistics->main_hub_accepted_at) {
            return redirect()
                ->route('waybills.index', ['account' => $account])
                ->with('success', 'This waybill was already accepted at the main hub.');
        }

        DB::transaction(function () use ($waybill, $user) {
            $waybill->logistics->update([
                'main_hub_accepted_by' => $user->id,
                'main_hub_accepted_at' => now(),
            ]);

            $waybill->update([
                'current_status' => 'Received at Main Hub',
            ]);

            WaybillStatusHistory::create([
                'waybill_record_id' => $waybill->id,
                'user_id' => $user->id,
                'status' => 'Received at Main Hub',
                'remarks' => 'Accepted at main hub.',
            ]);
        });

        return redirect()
            ->route('waybills.index', ['account' => $account])
            ->with('success', 'Waybill accepted at main hub successfully.');
    }

    public function destroy(string $account, string $waybill)
    {
        $this->authorizeWaybillPermission('can_delete');

        $waybill = WaybillRecord::with([
            'waybill',
            'items',
            'photos',
            'statusHistories',
            'logistics',
            'transactions.payments',
        ])
            ->whereHas('waybill')
            ->findOrFail($waybill);

        $user = auth()->user();

        if ($user->hasRole('Branch') && $user->branch_id && $waybill->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized.');
        }

        if ($user->hasRole('Logistics') && !$waybill->logistics?->logistics_accepted_at) {
            abort(403, 'You must accept this waybill before deleting it.');
        }

        DB::transaction(function () use ($waybill) {
            foreach ($waybill->transactions as $transaction) {
                $transaction->payments()->delete();
                $transaction->delete();
            }

            $waybill->items()->delete();
            $waybill->photos()->delete();
            $waybill->statusHistories()->delete();
            $waybill->logistics()->delete();
            $waybill->waybill()->delete();

            $waybill->delete();
        });

        return redirect()
            ->route('waybills.index', ['account' => $account])
            ->with('success', 'Waybill deleted successfully.');
    }
}