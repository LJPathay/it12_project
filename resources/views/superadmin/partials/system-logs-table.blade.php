<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>User</th>
                <th class="text-center">Action</th>
                <th>Table</th>
                <th>Record ID</th>
                <th class="text-center">Status</th>
                <th>Date</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody id="systemLogsTableBody">
            @foreach($logs as $log)
                @php
                    // Get the actual user name from polymorphic relationship
                    $userName = 'System';
                    if ($log->loggable) {
                        $userName = $log->loggable->name;
                    }
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ substr($userName, 0, 1) }}
                            </div>
                            {{ $userName }}
                        </div>
                    </td>
                    <td class="text-center">
                        @if($log->action == 'created')
                            <span class="badge bg-success">{{ ucfirst($log->action) }}</span>
                        @elseif($log->action == 'updated')
                            <span class="badge bg-warning text-dark">{{ ucfirst($log->action) }}</span>
                        @elseif($log->action == 'deleted')
                            <span class="badge bg-danger">{{ ucfirst($log->action) }}</span>
                        @else
                            <span class="badge bg-info">{{ ucfirst($log->action) }}</span>
                        @endif
                    </td>
                    <td>{{ $log->table_name ?? 'N/A' }}</td>
                    <td>{{ $log->record_id ?? 'N/A' }}</td>
                    <td class="text-center">
                        @if($log->status == 'active')
                            <span class="badge bg-success">{{ ucfirst($log->status) }}</span>
                        @elseif($log->status == 'inactive')
                            <span class="badge bg-secondary">{{ ucfirst($log->status) }}</span>
                        @elseif($log->status == 'archived')
                            <span class="badge bg-warning text-dark">{{ ucfirst($log->status) }}</span>
                        @else
                            <span class="badge bg-info">{{ ucfirst($log->status ?? 'active') }}</span>
                        @endif
                    </td>
                    <td>{{ $log->created_at->format('M d, Y g:i A') }}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                            data-bs-target="#logDetailsModal" data-user="{{ $userName }}"
                            data-action="{{ ucfirst($log->action) }}" data-table="{{ $log->table_name ?? 'N/A' }}"
                            data-record="{{ $log->record_id ?? 'N/A' }}"
                            data-status="{{ ucfirst($log->status ?? 'active') }}" data-old='@json($log->old_values)'
                            data-new='@json($log->new_values)' data-ip="{{ $log->ip_address ?? 'N/A' }}"
                            data-timestamp="{{ $log->created_at->format('F d, Y \a\t g:i A') }}" title="View Details">
                            <i class="fas fa-eye text-info"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
