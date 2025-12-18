@extends('admin.layout')

@section('title', 'Malasakit | Inventory Reports')
@section('page-title', 'Inventory Reports')
@section('page-description', 'Comprehensive inventory analytics and statistics')

@section('content')
    <div class="p-0 p-md-4">
        <!-- Export Buttons -->
        <!-- Export Form -->
        <div class="card-surface p-3 mb-4">
            <form action="{{ route('admin.reports.inventory') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Start Date</label>
                    <input type="date" name="start_date" class="form-control" required
                        value="{{ request('start_date', $filterStartDate) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">End Date</label>
                    <input type="date" name="end_date" class="form-control" required
                        value="{{ request('end_date', $filterEndDate) }}">
                </div>
                <div class="col-md-6 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-filter me-2"></i>Apply Filter
                    </button>
                    <button type="submit" formaction="{{ route('admin.reports.export.inventory') }}" class="btn btn-success flex-grow-1">
                        <i class="fas fa-file-excel me-2"></i>Export Excel (Report)
                    </button>
                    <button type="submit" formaction="{{ route('admin.reports.export.inventory.pdf') }}"
                        class="btn btn-danger flex-grow-1">
                        <i class="fas fa-file-pdf me-2"></i>Export PDF
                    </button>
                </div>
            </form>
        </div>

        <p class="text-muted small mb-3">
            Showing results from <strong>{{ \Carbon\Carbon::parse($filterStartDate)->format('M d, Y') }}</strong>
            to <strong>{{ \Carbon\Carbon::parse($filterEndDate)->format('M d, Y') }}</strong>
        </p>

        <!-- Overview Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card-surface p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Total Items</small>
                        <i class="fas fa-box text-primary"></i>
                    </div>
                    <h3 class="mb-0">{{ number_format($totalItems) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-surface p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Low Stock</small>
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                    </div>
                    <h3 class="mb-0">{{ number_format($lowStockCount) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-surface p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Out of Stock</small>
                        <i class="fas fa-times-circle text-danger"></i>
                    </div>
                    <h3 class="mb-0">{{ number_format($outOfStockCount) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-surface p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Expiring In Range</small>
                        <i class="fas fa-clock text-info"></i>
                    </div>
                    <h3 class="mb-0">{{ number_format($expiringSoonCount) }}</h3>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Category Breakdown -->
            <div class="col-md-6">
                <div class="card-surface p-3 h-100">
                    <h5 class="mb-3 fw-bold">Category Breakdown</h5>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-end">Items</th>
                                    <th class="text-end">Total Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categoryBreakdown as $category)
                                    <tr>
                                        <td>{{ $category->category }}</td>
                                        <td class="text-end"><strong>{{ $category->count }}</strong></td>
                                        <td class="text-end">{{ number_format($category->total_stock) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="col-md-6">
                <div class="card-surface p-3 h-100">
                    <h5 class="mb-3 fw-bold">Recent Transactions</h5>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Date</th>
                                    <th>Item</th>
                                    <th>User</th>
                                    <th>Qty</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->format('M d, Y') }}<br><small class="text-muted">{{ $transaction->created_at->format('h:i A') }}</small></td>
                                        <td>{{ Str::limit($transaction->inventory->item_name ?? 'N/A', 20) }}</td>
                                        <td>
                                            @if($transaction->performable)
                                                {{ $transaction->performable->name }}
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                        <td class="{{ $transaction->transaction_type === 'restock' ? 'text-success' : 'text-danger' }} fw-bold">
                                            {{ $transaction->transaction_type === 'restock' ? '+' : '-' }}{{ $transaction->quantity }}
                                        </td>
                                        <td class="small text-muted">{{ Str::limit($transaction->notes ?? '-', 20) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No recent transactions</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Critical Items -->
        <div class="row g-4">
            <!-- Low Stock Items -->
            <div class="col-md-6">
                <div class="card-surface p-3 h-100">
                    <h5 class="mb-3 fw-bold">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Low Stock Items
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th class="text-end">Current</th>
                                    <th class="text-end">Min</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockItems as $item)
                                    <tr>
                                        <td>{{ $item->item_name }}</td>
                                        <td class="text-end"><span class="badge bg-warning">{{ $item->current_stock }}</span>
                                        </td>
                                        <td class="text-end">{{ $item->minimum_stock }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No low stock items</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Expiring Soon Items -->
            <div class="col-md-6">
                <div class="card-surface p-3 h-100">
                    <h5 class="mb-3 fw-bold">
                        <i class="fas fa-clock text-info me-2"></i>Expiring Soon
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th class="text-end">Expiry Date</th>
                                    <th class="text-end">Days Left</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expiringSoonItems as $item)
                                    <tr>
                                        <td>{{ $item->item_name }}</td>
                                        <td class="text-end">{{ \Carbon\Carbon::parse($item->expiry_date)->format('M d, Y') }}
                                        </td>
                                        <td class="text-end">
                                            <span
                                                class="badge bg-{{ \Carbon\Carbon::parse($item->expiry_date)->diffInDays(now()) < 30 ? 'danger' : 'info' }}">
                                                {{ \Carbon\Carbon::parse($item->expiry_date)->diffInDays(now()) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No items expiring soon</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection