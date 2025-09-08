// resources/js/feedback.js
class FeedbackSystem {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupFilters();
        this.setupCreateModal();
    }

    bindEvents() {
        // Create feedback form
        document.getElementById('create-feedback-form')?.addEventListener('submit', (e) => this.handleCreateFeedback(e));

        // Filters form
        document.getElementById('feedback-filters')?.addEventListener('submit', (e) => this.handleFilters(e));
        document.getElementById('feedback-filters')?.addEventListener('reset', () => this.resetFilters());

        // Event delegation for dynamic elements
        document.addEventListener('click', (e) => {
            if (e.target.closest('.view-feedback')) {
                alert("ok")
                const button = e.target.closest('.view-feedback');
                this.showFeedbackDetail(button.dataset.id);
            }
            if (e.target.closest('.add-comment')) {
                const button = e.target.closest('.add-comment');
                this.addComment(button.dataset.id);
            }
        });
    }

    async handleCreateFeedback(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        try {
            const response = await axios.post(form.action, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            if (response.data.success) {
                this.closeModal('#createFeedbackModal');
                form.reset();
                this.loadFeedbacks();
                this.showToast('Feedback submitted successfully!', 'success');
            }
        } catch (error) {
            this.showToast('Error submitting feedback', 'error');
        }
    }

    async showFeedbackDetail(feedbackId) {
        try {
            // Show loading state
            const modalContent = document.getElementById('feedback-detail-content');
            modalContent.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading feedback details...</p>
            </div>
        `;

            // Show modal immediately
            const modal = new bootstrap.Modal(document.getElementById('feedbackDetailModal'));
            modal.show();

            // Fetch feedback details
            const response = await axios.get(`/feedback/${feedbackId}`);

            // Update modal content
            modalContent.innerHTML = response.data.html;

            // Bind comment form event
            this.bindCommentForm(feedbackId);

        } catch (error) {
            console.error('Error loading feedback details:', error);
            this.showToast('Error loading feedback details', 'error');

            // Show error message in modal
            document.getElementById('feedback-detail-content').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                <h5>Error Loading Details</h5>
                <p class="text-muted">Unable to load feedback details. Please try again.</p>
                <button class="btn btn-primary" onclick="feedbackSystem.showFeedbackDetail(${feedbackId})">
                    <i class="fas fa-refresh me-1"></i> Try Again
                </button>
            </div>
        `;
        }
    }
    bindCommentForm(feedbackId) {
        const form = document.querySelector('.add-comment-form');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.handleAddComment(feedbackId, form);
            });
        }
    }

    async handleAddComment(feedbackId, form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        try {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Adding...';

            const response = await axios.post(`/feedback/${feedbackId}/comments`, {
                content: formData.get('content'),
                is_admin_note: formData.get('is_admin_note') === 'on',
                _token: document.querySelector('meta[name="csrf-token"]').content
            });

            if (response.data.success) {
                // Clear form
                form.reset();

                // Reload the detail view to show new comment
                await this.showFeedbackDetail(feedbackId);

                this.showToast('Comment added successfully!', 'success');
            }
        } catch (error) {
            this.showToast('Error adding comment', 'error');
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    async updateFeedback(feedbackId, data) {
        try {
            const response = await axios.put(`/feedback/${feedbackId}`, data);
            if (response.data.success) {
                this.loadFeedbacks();
                this.showToast('Feedback updated successfully!', 'success');
            }
        } catch (error) {
            this.showToast('Error updating feedback', 'error');
        }
    }

    async addComment(feedbackId) {
        const content = prompt('Enter your comment:');
        if (content) {
            try {
                const response = await axios.post(`/feedback/${feedbackId}/comments`, {
                    content: content,
                    _token: document.querySelector('meta[name="csrf-token"]').content
                });

                if (response.data.success) {
                    this.loadFeedbacks();
                    this.showToast('Comment added successfully!', 'success');
                }
            } catch (error) {
                this.showToast('Error adding comment', 'error');
            }
        }
    }

    async handleFilters(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const params = new URLSearchParams(formData).toString();

        try {
            const response = await axios.get(`/feedback?${params}`);
            document.getElementById('feedback-list').innerHTML = response.data.html;
        } catch (error) {
            this.showToast('Error applying filters', 'error');
        }
    }

    async loadFeedbacks() {
        try {
            const response = await axios.get('/feedback');
            document.getElementById('feedback-list').innerHTML = response.data.html;
        } catch (error) {
            this.showToast('Error loading feedbacks', 'error');
        }
    }

    resetFilters() {
        window.location.href = '/feedback';
    }

    closeModal(selector) {
        const modal = bootstrap.Modal.getInstance(document.querySelector(selector));
        modal?.hide();
    }

    showToast(message, type = 'success') {
        // Implement toast notification
        console.log(`${type}: ${message}`);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    new FeedbackSystem();
});

// Add to feedback.js
setupRealTimeUpdates() {
    if (typeof Echo !== 'undefined') {
        Echo.channel('feedback')
            .listen('FeedbackUpdated', (e) => {
                this.loadFeedbacks();
            })
            .listen('CommentAdded', (e) => {
                this.loadFeedbacks();
            });
    }
}


// Add these methods to your FeedbackSystem class

setupCreateModal() {
    const modal = document.getElementById('createFeedbackModal');
    if (modal) {
        // File preview functionality
        const fileInput = document.getElementById('attachments');
        const filePreview = document.getElementById('file-preview');
        const filePreviewList = document.getElementById('file-preview-list');

        fileInput?.addEventListener('change', (e) => this.handleFileSelection(e));

        // Modal show/hide events
        modal.addEventListener('show.bs.modal', () => this.resetCreateForm());
        modal.addEventListener('hidden.bs.modal', () => this.resetCreateForm());
    }
}

handleFileSelection(e) {
    const files = e.target.files;
    const filePreview = document.getElementById('file-preview');
    const filePreviewList = document.getElementById('file-preview-list');

    if (files.length > 0) {
        filePreview.style.display = 'block';
        filePreviewList.innerHTML = '';

        Array.from(files).forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item d-flex justify-content-between align-items-center mb-2';

            const fileInfo = document.createElement('div');
            fileInfo.className = 'file-info';
            fileInfo.innerHTML = `
                <i class="fas fa-file me-2"></i>
                <span class="file-name">${file.name}</span>
                <small class="text-muted ms-2">(${this.formatFileSize(file.size)})</small>
            `;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-outline-danger';
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.onclick = () => this.removeFile(file.name);

            fileItem.appendChild(fileInfo);
            fileItem.appendChild(removeBtn);
            filePreviewList.appendChild(fileItem);
        });
    } else {
        filePreview.style.display = 'none';
    }
}

formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

removeFile(fileName) {
    const fileInput = document.getElementById('attachments');
    const files = Array.from(fileInput.files);
    const newFiles = files.filter(file => file.name !== fileName);

    // Create new FileList (simulated)
    const dataTransfer = new DataTransfer();
    newFiles.forEach(file => dataTransfer.items.add(file));
    fileInput.files = dataTransfer.files;

    // Trigger change event to update preview
    fileInput.dispatchEvent(new Event('change'));
}

resetCreateForm() {
    const form = document.getElementById('create-feedback-form');
    if (form) {
        form.reset();
        document.getElementById('file-preview').style.display = 'none';
        document.getElementById('file-preview-list').innerHTML = '';

        // Clear validation errors
        const errorElements = form.querySelectorAll('.is-invalid, .invalid-feedback');
        errorElements.forEach(el => {
            el.classList.remove('is-invalid');
            el.textContent = '';
        });
    }
}

async handleCreateFeedback(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submit-feedback-btn');
    const spinner = document.getElementById('submit-spinner');
    const submitText = document.getElementById('submit-text');

    // Show loading state
    submitBtn.disabled = true;
    spinner.classList.remove('d-none');
    submitText.textContent = 'Submitting...';

    try {
        const response = await axios.post(form.action, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response.data.success) {
            // Close modal and reset form
            this.closeModal('#createFeedbackModal');
            this.showToast('Feedback submitted successfully!', 'success');

            // Reload feedback list
            this.loadFeedbacks();
        }
    } catch (error) {
        if (error.response?.status === 422) {
            // Validation errors
            this.handleValidationErrors(error.response.data.errors);
        } else {
            this.showToast('Error submitting feedback. Please try again.', 'error');
        }
    } finally {
        // Reset loading state
        submitBtn.disabled = false;
        spinner.classList.add('d-none');
        submitText.textContent = 'Submit Feedback';
    }
}

handleValidationErrors(errors) {
    // Clear previous errors
    const errorElements = document.querySelectorAll('.is-invalid, .invalid-feedback');
    errorElements.forEach(el => {
        el.classList.remove('is-invalid');
        el.textContent = '';
    });

    // Show new errors
    Object.entries(errors).forEach(([field, messages]) => {
        const input = document.querySelector(`[name="${field}"]`);
        const errorElement = document.getElementById(`${field}-error`);

        if (input) {
            input.classList.add('is-invalid');
        }

        if (errorElement) {
            errorElement.textContent = messages[0];
        }
    });
}