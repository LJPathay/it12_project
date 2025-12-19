<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Actor</th>
                <th>Event</th>
                <th>Target</th>
                <th>Details</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody id="systemLogsTableBody">
            @foreach($logs as $log)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            @php
                                $actorName = $log->loggable->name ?? 'System';
                                $actorInitial = substr($actorName, 0, 1);
                            @endphp
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ $actorInitial }}
                            </div>
                            <div>
                                <div class="fw-medium">{{ $log->actor_display }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $eventBadgeClass = match(true) {
                                str_contains($log->event_name, 'Created') || str_contains($log->event_name, 'Registration') => 'bg-success',
                                str_contains($log->event_name, 'Completed') || str_contains($log->event_name, 'Approved') => 'bg-info',
                                str_contains($log->event_name, 'Started') || str_contains($log->event_name, 'Updated') => 'bg-warning text-dark',
                                str_contains($log->event_name, 'Cancelled') || str_contains($log->event_name, 'Deleted') || str_contains($log->event_name, 'No-Show') => 'bg-danger',
                                str_contains($log->event_name, 'Login') => 'bg-primary',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $eventBadgeClass }}">{{ $log->event_name }}</span>
                    </td>
                    <td class="fw-medium">{{ $log->target }}</td>
                    <td class="text-muted">{{ $log->details }}</td>
                    <td>
                        <small class="text-muted">{{ $log->created_at->format('M d, Y') }}</small>
                        <br>
                        <small class="text-muted">{{ $log->created_at->format('g:i A') }}</small>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
