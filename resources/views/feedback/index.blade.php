@extends('layouts.app')

@section('title', 'Feedback System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-3">
            @include('feedback.partials.filters')
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Feedback List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Feedback Tickets</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFeedbackModal">
                        <i class="fas fa-plus"></i> New Feedback
                    </button>
                </div>
                <div class="card-body">
                    <div id="feedback-list">
                        @include('feedback.partials.list', ['feedbacks' => $feedbacks])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Feedback Modal -->
@include('feedback.modals.create')

<!-- Feedback Detail Modal -->
<div class="modal fade" id="feedbackDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Feedback Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="feedback-detail-content">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/feedback.js') }}"></script>
@endpush