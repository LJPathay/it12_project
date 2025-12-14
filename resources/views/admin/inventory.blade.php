@extends('admin.layout')

@section('title', 'Inventory - Barangay Health Center')
@section('page-title', 'Inventory Management')
@section('page-description', 'Manage medical supplies and equipment')

@section('page-styles')
    <style>
        body {
            color: inherit;
        }

        .inventory-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            margin-bottom: 1rem;
            border: 1px solid #edf1f7;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .inventory-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.10);
            border-color: #d0e2ff;
        }

        .stock-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .stock-good {
            background-color: #28a745;
        }

        .stock-low {
            background-color: #ffc107;
        }

        .stock-out {
            background-color: #dc3545;
        }

        .modal-content {
            border-radius: 12px;
            border: none;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #009fb1;
            box-shadow: 0 0 0 0.2rem rgba(0, 159, 177, 0.25);
        }

        /* Cards inherit theme text color */
        .inventory-card {
            color: inherit;
        }

        /* Dark mode surfaces */
        body.bg-dark .main-content {
            background-color: #151718;
        }

        /* Inventory uses a slightly darker sidebar in dark mode */
        body.bg-dark .sidebar {
            background: #131516;
            border-right-color: #2a2f35;
        }

        body.bg-dark .header {
            background: #1b1e20;
            border-bottom-color: #2a2f35;
        }

        body.bg-dark .inventory-card {
            background: #1e2124;
            color: #e6e6e6;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            border-color: #2a2f35;
        }

        body.bg-dark .table thead,
        body.bg-dark .table-light {
            background: #1a1f24 !important;
            color: #e6e6e6;
        }

        /* Headings and muted text visibility */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            color: inherit;
        }

        body.bg-dark .text-muted,
        body.bg-dark small {
            color: #b0b0b0 !important;
        }

        /* Dark mode for expiring items alert */
        body.bg-dark .alert-light.border-warning {
            background-color: #4a3a1a;
            border-color: #6a5a2a !important;
            color: #f5d88a;
        }

        body.bg-dark .alert-light.border-warning .text-warning {
            color: #fbbf24 !important;
        }

        /* Dark mode for search input group */
        body.bg-dark .input-group-text {
            background-color: #0f1316 !important;
            border-color: #2a2f35 !important;
            color: #e6e6e6 !important;
        }

        /* Dark mode modal + form fields */
        body.bg-dark .modal-content {
            background: #1e2124;
            color: #e6e6e6;
            border-color: #2a2f35;
        }

        body.bg-dark .modal-content .form-label {
            color: #e6e6e6;
        }

        body.bg-dark .modal-content .form-control,
        body.bg-dark .modal-content .form-select,
        body.bg-dark .form-control,
        body.bg-dark .form-select {
            background-color: #0f1316;
            color: #e6e6e6;
            border-color: #2a2f35;
        }

        body.bg-dark .modal-content .form-control::placeholder {
            color: #9aa4ad;
        }

        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead th {
            background-color: #f9fafb;
            border: none;
            font-weight: 600;
            color: #4b5563;
            padding: 0.85rem 1rem;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: .04em;
        }

        .table-modern tbody td {
            border: none;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #f1f3f4;
        }

        .table-modern tbody tr:hover {
            background-color: #f8f9fa;
        }

        body.bg-dark .table-modern thead th {
            background-color: #1a1f24;
            color: #e6e6e6;
        }

        body.bg-dark .table-modern tbody td {
            border-bottom-color: #2a2f35;
            color: #d6d6d6;
        }

        body.bg-dark .table-modern tbody tr:hover {
            background-color: #2a2f35;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        /* Dark Mode Modal Specifics */
        body.bg-dark .modal-content .card {
            background-color: #2a2f35 !important;
            border: 1px solid #3f4751 !important;
        }

        body.bg-dark .modal-content .card h6 {
            color: #fff !important;
        }

        body.bg-dark .modal-content .text-muted {
            color: #b0b3b8 !important;
        }

        body.bg-dark .modal-content .card span:not(.badge):not(.fw-bold) {
            color: #b0b3b8 !important;
        }

        body.bg-dark .modal-content .card .fw-bold {
            color: #fff !important;
        }

        body.bg-dark .modal-content .card p.text-muted {
            color: #b0b3b8 !important;
        }

        body.bg-dark .modal-content .table thead th {
            background-color: #23272b;
            color: #fff;
            border-bottom: 1px solid #3f4751;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        body.bg-dark .modal-content .table td {
            color: #e6e6e6;
            border-color: #3f4751;
            vertical-align: middle;
        }

        body.bg-dark .modal-content .table-hover tbody tr:hover {
            background-color: #323840;
        }

        /* Dark Mode Badges */
        body.bg-dark .badge.bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.2) !important;
            color: #75b798 !important;
        }

        body.bg-dark .badge.bg-danger-subtle {
            background-color: rgba(220, 53, 69, 0.2) !important;
            color: #ea868f !important;
        }

        /* Hide Bootstrap pagination's built-in "Showing" text on the left */
        #inventoryPaginationContainer nav p,
        #inventoryPaginationContainer nav .text-sm,
        #inventoryPaginationContainer nav .text-muted,
        nav[role="navigation"] p,
        nav[role="navigation"] .text-sm {
            display: none !important;
        }
        
        /* Bring showing text closer to pagination */
        #inventoryPaginationContainer > div:last-child {
            margin-top: -0.5rem !important;
        }

        /* Dark mode for low stock alert */
        body.bg-dark .alert-warning.border-danger-subtle {
            background-color: rgba(220, 53, 69, 0.2);
            border-color: var(--color-danger) !important;
            color: var(--color-danger-light);
        }

        body.bg-dark .alert-warning.border-danger-subtle .text-danger {
            color: var(--color-danger-light) !important;
        }
    </style>
@endsection

@section('content')
    <div class="p-0 p-md-4">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="fas fa-plus me-2"></i> Add New Item
            </button>
        </div>
        <!-- Alerts -->
        @if(($stats['low_stock'] ?? 0) > 0)
            <div class="alert alert-warning border-danger-subtle mb-3" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-triangle-exclamation me-2 text-danger"></i>
                    <strong>{{ $stats['low_stock'] }} item(s)</strong>
                    <span class="ms-2">are running low on stock and need restocking.</span>
                </div>
            </div>
        @endif
        @if(($stats['expiring_soon'] ?? 0) > 0)
            <div class="alert alert-light border-warning mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2 text-warning"></i>
                    <strong>{{ $stats['expiring_soon'] }} item(s)</strong>
                    <span class="ms-2">are expiring within 90 days.</span>
                </div>
            </div>
        @endif

        <!-- Stat Cards -->
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="inventory-card py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small text-muted">Total Items</div>
                        <i class="fas fa-box text-primary"></i>
                    </div>
                    <div class="h5 mb-0">{{ $stats['total_items'] ?? 0 }}</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="inventory-card py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small text-muted">Low Stock</div>
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                    </div>

                    <div class="h5 mb-0">{{ $stats['low_stock'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="inventory-card py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small text-muted">Out of Stock</div>
                        <i class="fas fa-triangle-exclamation text-danger"></i>
                    </div>
                    <div class="h5 mb-0">{{ $stats['out_of_stock'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="inventory-card py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small text-muted">Expiring Soon</div>
                        <i class="fas fa-hourglass-half text-warning"></i>
                    </div>
                    <div class="h5 mb-0">{{ $stats['expiring_soon'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="filter-card mb-3">
            <form method="GET" action="{{ route('admin.inventory') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label mb-1">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" id="inventorySearchInput" class="form-control"
                                placeholder="Search by name, location, or ID" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1">Category</label>
                        <select class="form-select" name="category" id="inventoryCategorySelect">
                            <option value="">All</option>
                            @foreach(($categories ?? []) as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>

        @if($inventory->count() > 0)

            <div class="table-card p-0">
                <div class="table-responsive">
                    <table class="table table-modern table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">Item Name</th>
                                <th scope="col">Category</th>
                                <th scope="col">Stock</th>
                                <th scope="col">Min Stock</th>
                                <th scope="col">Expiry Date</th>
                                <th scope="col">Location</th>
                                <th scope="col" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTableBody">
                            @foreach($inventory as $item)
                                <tr class="cursor-pointer"
                                    onclick="new bootstrap.Modal(document.getElementById('viewDetailModal{{ $item->id }}')).show()"
                                    data-name="{{ strtolower($item->item_name ?? '') }}"
                                    data-category="{{ strtolower($item->category ?? '') }}"
                                    data-status="{{ strtolower($item->status ?? '') }}"
                                    data-location="{{ strtolower($item->location ?? '') }}" data-id="{{ $item->id }}">
                                    <td>
                                        <span
                                            class="stock-indicator @if($item->current_stock == 0) stock-out @elseif($item->current_stock <= $item->minimum_stock) stock-low @else stock-good @endif"></span>
                                    </td>
                                    <td>{{ $item->item_name }}</td>
                                    <td>{{ $item->category }}</td>
                                    <td><strong>{{ $item->current_stock }}</strong></td>
                                    <td>{{ $item->minimum_stock }}</td>
                                    <td>{{ $item->expiry_date ? \Illuminate\Support\Carbon::parse($item->expiry_date)->format('Y-m-d') : 'N/A' }}
                                    </td>
                                    <td>{{ $item->location ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        @php
                                            $badge = 'secondary';
                                            if ($item->status === 'in_stock')
                                                $badge = 'success';
                                            elseif ($item->status === 'low_stock')
                                                $badge = 'warning';
                                            elseif ($item->status === 'out_of_stock')
                                                $badge = 'danger';
                                            elseif ($item->status === 'expired')
                                                $badge = 'dark';
                                        @endphp
                                        <span
                                            class="badge bg-{{ $badge }}">{{ str_replace('_', ' ', ucfirst($item->status)) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="inventoryPagination"></div>

            <!-- Per-item View Modals -->
            @foreach($inventory as $item)
                <div class="modal fade" id="viewDetailModal{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header border-0 pb-0">
                                <div>
                                    <h5 class="modal-title fw-bold">{{ $item->item_name }}</h5>
                                    <span class="text-muted small">{{ $item->category }}</span>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0 h-100">
                                            <div class="card-body">
                                                <h6 class="fw-bold mb-3">Item Details</h6>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Current Stock:</span>
                                                    <span
                                                        class="fw-bold {{ $item->current_stock <= $item->minimum_stock ? 'text-danger' : 'text-success' }}">
                                                        {{ $item->current_stock }} {{ $item->unit }}
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Minimum Stock:</span>
                                                    <span>{{ $item->minimum_stock }} {{ $item->unit }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Expiry Date:</span>
                                                    <span
                                                        class="{{ $item->expiry_date && $item->expiry_date < now() ? 'text-danger fw-bold' : '' }}">
                                                        {{ $item->expiry_date ? \Illuminate\Support\Carbon::parse($item->expiry_date)->format('M d, Y') : 'N/A' }}
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Location:</span>
                                                    <span>{{ $item->location ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0 h-100">
                                            <div class="card-body">
                                                <h6 class="fw-bold mb-3">Description</h6>
                                                <p class="text-muted small mb-0">
                                                    {{ $item->description ?? 'No description available.' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold mb-3">Transaction History</h6>
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Quantity</th>
                                                <th>Notes</th>
                                                <th>User</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($item->transactions->sortByDesc('created_at') as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                                    <td>
                                                        @if($transaction->transaction_type === 'restock')
                                                            <span class="badge bg-success-subtle text-success">Restock</span>
                                                        @else
                                                            <span class="badge bg-danger-subtle text-danger">Deduct</span>
                                                        @endif
                                                    </td>
                                                    <td
                                                        class="{{ $transaction->transaction_type === 'restock' ? 'text-success' : 'text-danger' }} fw-bold">
                                                        {{ $transaction->transaction_type === 'restock' ? '+' : '-' }}{{ $transaction->quantity }}
                                                    </td>
                                                    <td class="small text-muted">{{ $transaction->notes ?? '-' }}</td>
                                                    <td class="small">{{ $transaction->user->name ?? 'System' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-3">No transaction history found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button class="btn btn-outline-success" data-bs-toggle="modal"
                                    data-bs-target="#restockModal{{ $item->id }}">
                                    <i class="fas fa-plus me-1"></i> Restock
                                </button>
                                <button class="btn btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#deductModal{{ $item->id }}">
                                    <i class="fas fa-minus me-1"></i> Deduct
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Restock Modal -->
                <div class="modal fade" id="restockModal{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Restock: {{ $item->item_name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="{{ route('admin.inventory.restock', $item) }}">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" class="form-control" name="quantity" min="1" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">New Expiry Date</label>
                                        <input type="date" class="form-control" name="expiry_date">
                                        <small class="text-muted">Leave blank to keep the current expiry date.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control" name="notes" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">Restock</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Deduct Modal -->
                <div class="modal fade" id="deductModal{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Deduct: {{ $item->item_name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="{{ route('admin.inventory.deduct', $item) }}">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" class="form-control" name="quantity" min="1" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control" name="notes" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Deduct</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-5">
                <i class="fas fa-box me-2 fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No inventory items found</h5>
                <p class="text-muted">Start by adding your first inventory item.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="fas fa-plus me-2"></i> Add First Item
                </button>
            </div>
        @endif
    </div>
@endsection

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.inventory.add') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="item_name" class="form-label">Item Name *</label>
                        <input type="text" class="form-control" id="item_name" name="item_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category *</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Medicines">Medicines</option>
                            <option value="Medical Supplies">Medical Supplies</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Vaccines">Vaccines</option>
                            <option value="PPE">PPE</option>
                            <option value="Syringes & Needles">Syringes & Needles</option>
                            <option value="Lab Supplies">Lab Supplies</option>
                            <option value="Test Kits">Test Kits</option>
                            <option value="Disinfectants">Disinfectants</option>
                            <option value="Consumables">Consumables</option>
                            <option value="Dressings">Dressings</option>
                            <option value="Nutritional Supplements">Nutritional Supplements</option>
                            <option value="Oxygen Supplies">Oxygen Supplies</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="current_stock" class="form-label">Current Stock *</label>
                                <input type="number" class="form-control" id="current_stock" name="current_stock"
                                    min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="minimum_stock" class="form-label">Minimum Stock *</label>
                                <input type="number" class="form-control" id="minimum_stock" name="minimum_stock"
                                    min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="date" class="form-control" id="expiry_date" name="expiry_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location"
                                    placeholder="e.g., Cabinet A1">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="unit" class="form-label">Unit *</label>
                        <select class="form-select" id="unit" name="unit" required>
                            <option value="">Select Unit</option>
                            <option value="pieces">Pieces</option>
                            <option value="boxes">Boxes</option>
                            <option value="bottles">Bottles</option>
                            <option value="vials">Vials</option>
                            <option value="tablet">Tablet</option>
                            <option value="units">Units</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize reusable pagination
            // Note: window.paginatorInstance assignment is optional now but kept for legacy external access if needed
            window.paginatorInstance = new TablePaginator({
                tableId: 'inventoryTable',
                tableBodyId: 'inventoryTableBody',
                paginationContainerId: 'inventoryPagination',
                searchId: 'inventorySearchInput',
                rowsPerPage: 10,
                filterInputs: {
                    'inventoryCategorySelect': 'data-category'
                }
            });
        });
    </script>
@endpush