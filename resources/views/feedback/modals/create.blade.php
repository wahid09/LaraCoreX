<div class="modal fade" id="createFeedbackModal" tabindex="-1" aria-labelledby="createFeedbackModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createFeedbackModalLabel">Submit New Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="create-feedback-form" action="{{ route('feedback.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required
                                    placeholder="Brief description of your feedback">
                                <div class="invalid-feedback" id="title-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="bug">Bug Report</option>
                                    <option value="feature">Feature Request</option>
                                    <option value="improvement">Improvement</option>
                                    <option value="other">Other</option>
                                </select>
                                <div class="invalid-feedback" id="type-error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="priority" name="priority" required>
                                    <option value="">Select Priority</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                                <div class="invalid-feedback" id="priority-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="attachments" class="form-label">Attachments</label>
                                <input type="file" class="form-control" id="attachments" name="attachments[]" multiple
                                    accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                                <div class="form-text">Max file size: 2MB each. Allowed: images, PDF, Word docs</div>
                                <div class="invalid-feedback" id="attachments-error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" required
                            placeholder="Please provide detailed information about your feedback..."></textarea>
                        <div class="form-text">Be as specific as possible. Include steps to reproduce for bugs.</div>
                        <div class="invalid-feedback" id="description-error"></div>
                    </div>

                    <div id="file-preview" class="mb-3" style="display: none;">
                        <label class="form-label">Selected Files:</label>
                        <div id="file-preview-list" class="border rounded p-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submit-feedback-btn">
                        <span class="spinner-border spinner-border-sm d-none" id="submit-spinner"></span>
                        <span id="submit-text">Submit Feedback</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>