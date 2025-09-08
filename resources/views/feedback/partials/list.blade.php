@if($feedbacks->count() > 0)
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Submitted By</th>
                <th>Assigned To</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($feedbacks as $feedback)
            <tr class="feedback-item" data-id="{{ $feedback->id }}">
                <td>
                    <strong>{{ $feedback->title }}</strong>
                    @if($feedback->comments_count > 0)
                    <span class="badge bg-info ms-1">
                        <i class="fas fa-comment"></i> {{ $feedback->comments_count }}
                    </span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-secondary">
                        {{ ucfirst($feedback->type) }}
                    </span>
                </td>
                <td>
                    @php
                    $priorityClasses = [
                    'low' => 'bg-success',
                    'medium' => 'bg-primary',
                    'high' => 'bg-warning',
                    'critical' => 'bg-danger'
                    ];
                    @endphp
                    <span class="badge {{ $priorityClasses[$feedback->priority] ?? 'bg-secondary' }}">
                        {{ ucfirst($feedback->priority) }}
                    </span>
                </td>
                <td>
                    @php
                    $statusClasses = [
                    'open' => 'bg-primary',
                    'in_progress' => 'bg-info',
                    'resolved' => 'bg-success',
                    'closed' => 'bg-secondary'
                    ];
                    @endphp
                    <span class="badge {{ $statusClasses[$feedback->status] ?? 'bg-secondary' }}">
                        {{ ucfirst(str_replace('_', ' ', $feedback->status)) }}
                    </span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-xs me-2">
                            <img src="{{ $feedback->user->avatar ?? asset('images/default-avatar.png') }}"
                                class="rounded-circle" alt="Avatar" width="24" height="24">
                        </div>
                        {{ $feedback->user->name }}
                    </div>
                </td>
                <td>
                    @if($feedback->assignedTo)
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-xs me-2">
                            <img src="{{ $feedback->assignedTo->avatar ?? asset('images/default-avatar.png') }}"
                                class="rounded-circle" alt="Avatar" width="24" height="24">
                        </div>
                        {{ $feedback->assignedTo->name }}
                    </div>
                    @else
                    <span class="text-muted">Not assigned</span>
                    @endif
                </td>
                <td>
                    <span class="small text-muted" title="{{ $feedback->created_at->format('M j, Y g:i A') }}">
                        {{ $feedback->created_at->diffForHumans() }}
                    </span>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary view-feedback" data-id="{{ $feedback->id }}"
                            data-bs-toggle="tooltip" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>

                        {{-- @if(auth()->user()->is_admin || auth()->id() == $feedback->user_id) --}}
                        <button class="btn btn-outline-secondary add-comment" data-id="{{ $feedback->id }}"
                            data-bs-toggle="tooltip" title="Add Comment">
                            <i class="fas fa-comment"></i>
                        </button>
                        {{-- @endif --}}

                        {{-- @if(auth()->user()->is_admin) --}}
                        <button class="btn btn-outline-info edit-feedback" data-id="{{ $feedback->id }}"
                            data-bs-toggle="tooltip" title="Edit Feedback">
                            <i class="fas fa-edit"></i>
                        </button>
                        {{-- @endif --}}
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($feedbacks->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $feedbacks->links() }}
</div>
@endif

@else
<div class="text-center py-5">
    <div class="empty-state">
        <i class="fas fa-comments fa-4x text-muted mb-3"></i>
        <h4>No feedback found</h4>
        <p class="text-muted">
            @if(request()->hasAny(['status', 'priority', 'type']))
            Try adjusting your filters or
            <a href="{{ route('feedback.index') }}" class="text-primary">clear all filters</a>
            @else
            No feedback submissions yet. Be the first to submit feedback!
            @endif
        </p>
        @if(!request()->hasAny(['status', 'priority', 'type']))
        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#createFeedbackModal">
            <i class="fas fa-plus"></i> Submit Feedback
        </button>
        @endif
    </div>
</div>
@endif