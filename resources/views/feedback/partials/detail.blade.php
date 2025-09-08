<div class="feedback-detail">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h4 class="mb-2">{{ $feedback->title }}</h4>
            <div class="d-flex gap-2 mb-2">
                <span class="badge bg-secondary">{{ ucfirst($feedback->type) }}</span>
                <span class="badge {{ $priorityClasses[$feedback->priority] }}">{{ ucfirst($feedback->priority)
                    }}</span>
                <span class="badge {{ $statusClasses[$feedback->status] }}">{{ ucfirst(str_replace('_', ' ',
                    $feedback->status)) }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <!-- Description -->
    <div class="mb-4">
        <h6 class="text-muted">Description</h6>
        <p class="mb-0">{{ $feedback->description }}</p>
    </div>

    <!-- Meta Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-3">Submission Details</h6>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-user me-2 text-primary"></i>
                        <div>
                            <small class="text-muted">Submitted by</small>
                            <div class="fw-medium">{{ $feedback->user->name }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-calendar me-2 text-primary"></i>
                        <div>
                            <small class="text-muted">Created</small>
                            <div class="fw-medium">{{ $feedback->created_at->format('M j, Y g:i A') }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        <div>
                            <small class="text-muted">Last updated</small>
                            <div class="fw-medium">{{ $feedback->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-3">Assignment Details</h6>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-user-check me-2 text-primary"></i>
                        <div>
                            <small class="text-muted">Assigned to</small>
                            <div class="fw-medium">
                                @if($feedback->assignedTo)
                                {{ $feedback->assignedTo->name }}
                                @else
                                <span class="text-muted">Not assigned</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-flag me-2 text-primary"></i>
                        <div>
                            <small class="text-muted">Status</small>
                            <div>
                                <span class="badge {{ $statusClasses[$feedback->status] }}">
                                    {{ ucfirst(str_replace('_', ' ', $feedback->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-bolt me-2 text-primary"></i>
                        <div>
                            <small class="text-muted">Priority</small>
                            <div>
                                <span class="badge {{ $priorityClasses[$feedback->priority] }}">
                                    {{ ucfirst($feedback->priority) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attachments -->
    @if($feedback->attachments->count() > 0)
    <div class="mb-4">
        <h6 class="text-muted mb-3">Attachments</h6>
        <div class="row">
            @foreach($feedback->attachments as $attachment)
            <div class="col-md-4 mb-2">
                <div class="card">
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-paperclip me-2 text-muted"></i>
                            <div class="flex-grow-1">
                                <small class="d-block text-truncate" style="max-width: 150px;">
                                    {{ $attachment->original_name }}
                                </small>
                            </div>
                            <a href="{{ Storage::url($attachment->filename) }}" target="_blank"
                                class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Comments Section -->
    <div class="comments-section">
        <h6 class="text-muted mb-3">Comments</h6>

        <div class="comments-list mb-3">
            @forelse($feedback->comments as $comment)
            <div class="comment-item mb-3 p-3 border rounded">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-2">
                            <img src="{{ $comment->user->avatar ?? asset('images/default-avatar.png') }}"
                                class="rounded-circle" width="32" height="32" alt="Avatar">
                        </div>
                        <div>
                            <strong class="d-block">{{ $comment->user->name }}</strong>
                            @if($comment->is_admin_note)
                            <span class="badge bg-info">Admin Note</span>
                            @endif
                        </div>
                    </div>
                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-0 text-dark">{{ $comment->content }}</p>
            </div>
            @empty
            <div class="text-center py-4">
                <i class="fas fa-comments fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">No comments yet</p>
            </div>
            @endforelse
        </div>

        <!-- Add Comment Form -->
        @if(auth()->check())
        <form class="add-comment-form" data-feedback-id="{{ $feedback->id }}">
            @csrf
            <div class="mb-2">
                <textarea name="content" class="form-control" rows="3" placeholder="Add a comment..."
                    required></textarea>
            </div>
            @if(auth()->user()->is_admin)
            <div class="form-check mb-2">
                <input type="checkbox" name="is_admin_note" id="is_admin_note_{{ $feedback->id }}"
                    class="form-check-input">
                <label for="is_admin_note_{{ $feedback->id }}" class="form-check-label">Mark as admin note</label>
            </div>
            @endif
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-1"></i> Add Comment
            </button>
        </form>
        @endif
    </div>
</div>