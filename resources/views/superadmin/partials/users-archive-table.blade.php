<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th class="text-center">Role</th>
                <th>Archived At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td class="text-center">{{ ucfirst($user->role === 'user' ? 'patient' : $user->role) }}</td>
                    <td>{{ optional($user->deleted_at)->format('M d, Y g:i A') }}</td>
                    <td>
<div class="d-flex gap-1">
    <form method="POST" action="{{ route('superadmin.user.restore', ['type' => $user->role, 'id' => $user->id]) }}" class="d-inline">
        @csrf
        <button type="button" class="btn btn-sm btn-outline-secondary action-btn"
            data-confirm data-confirm-title="Restore User"
            data-confirm-message="Are you sure you want to restore this user?"
            title="Restore">
            <i class="fas fa-undo text-success"></i>
        </button>
    </form>
</div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
