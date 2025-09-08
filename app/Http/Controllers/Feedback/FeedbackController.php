<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\FeedbackComment;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = Feedback::with(['user', 'assignedTo', 'comments.user'])
            ->latest();

        // Filter by status
        if ($request->has('status') && in_array($request->status, ['open', 'resolved'])) {
            if ($request->status === 'open') {
                $query->open();
            } else {
                $query->resolved();
            }
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->ajax()) {
            return response()->json([
                'feedbacks' => $query->paginate(10)
            ]);
        }

        return view('feedback.index', [
            'feedbacks' => $query->paginate(10),
            'filters' => $request->all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:bug,feature,improvement,other',
            'priority' => 'required|in:low,medium,high,critical',
            'attachments.*' => 'nullable|file|max:2048'
        ]);

        $feedback = Feedback::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'priority' => $request->priority,
            'user_id' => Auth::id()
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = $file->store('feedback-attachments');

                $feedback->attachments()->create([
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType()
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'feedback' => $feedback->load('user', 'attachments')
        ]);
    }

    public function update(Request $request, Feedback $feedback)
    {
        $request->validate([
            'status' => 'sometimes|in:open,in_progress,resolved,closed',
            'priority' => 'sometimes|in:low,medium,high,critical',
            'assigned_to' => 'sometimes|nullable|exists:users,id'
        ]);

        $feedback->update($request->only(['status', 'priority', 'assigned_to']));

        if ($request->status === 'resolved') {
            $feedback->update(['resolved_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'feedback' => $feedback->fresh(['user', 'assignedTo', 'comments.user'])
        ]);
    }

    public function addComment(Request $request, Feedback $feedback)
    {
        $request->validate([
            'content' => 'required|string',
            'is_admin_note' => 'sometimes|boolean'
        ]);

        $comment = $feedback->comments()->create([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'is_admin_note' => $request->is_admin_note ?? false
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user')
        ]);
    }
    public function show(Feedback $feedback)
    {
        // Load all relationships needed for the detail view
        $feedback->load([
            'user',
            'assignedTo',
            'comments.user',
            'attachments'
        ]);

        // Prepare priority and status classes for the view
        $priorityClasses = [
            'low' => 'bg-success',
            'medium' => 'bg-primary',
            'high' => 'bg-warning',
            'critical' => 'bg-danger'
        ];

        $statusClasses = [
            'open' => 'bg-primary',
            'in_progress' => 'bg-info',
            'resolved' => 'bg-success',
            'closed' => 'bg-secondary'
        ];

        if (request()->ajax()) {
            return response()->json([
                'html' => view('feedback.partials.detail', compact('feedback', 'priorityClasses', 'statusClasses'))->render()
            ]);
        }

        return view('feedback.show', compact('feedback', 'priorityClasses', 'statusClasses'));
    }
}
