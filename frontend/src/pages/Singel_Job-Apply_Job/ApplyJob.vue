<template>
    <div class="container my-5">
        <div class="card shadow-sm p-4">
            <h3 class="mb-4">Apply for: {{ jobTitle }}</h3>
            <form @submit.prevent="submitForm" novalidate>
                <div class="mb-3 text-start">
                    <label for="resume" class="form-label">Choose Resume (PDF only, max 5MB)</label>
                    <input type="file" class="form-control" id="resume" accept=".pdf" @change="handleFileUpload"
                        :class="{ 'is-invalid': errors.resume }">
                    <div class="invalid-feedback">{{ errors.resume }}</div>
                </div>

                <div class="mb-3 text-start">
                    <label for="coverLetter" class="form-label">Cover Letter (min 50 characters)</label>
                    <textarea class="form-control" id="coverLetter" v-model.trim="form.coverLetter"
                        :class="{ 'is-invalid': errors.coverLetter }" @blur="validateField('coverLetter')"
                        rows="5"></textarea>
                    <div class="invalid-feedback">{{ errors.coverLetter }}</div>
                    <div class="text-muted small mt-1">{{ form.coverLetter.length }}/1000 characters</div>
                </div>

                <div v-if="submitError" class="alert alert-danger text-start">
                    {{ submitError }}
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <router-link :to="{ name: 'JobDetails', params: { id: jobId } }"
                        class="btn btn-outline-primary px-4">Cancel</router-link>
                    <button type="submit" class="btn btn-primary px-4" :disabled="submitting">
                        <span v-if="!submitting">Submit Application</span>
                        <span v-else class="d-flex align-items-center">
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            jobId: this.$route.query.jobId,
            jobTitle: this.$route.query.jobTitle || 'This position',
            submitting: false,
            submitError: '',
            form: {
                coverLetter: '',
                resume: null
            },
            errors: {
                coverLetter: '',
                resume: ''
            }
        }
    },
    methods: {
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.type !== 'application/pdf') {
                    this.errors.resume = 'Only PDF files are allowed';
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    this.errors.resume = 'File size must be less than 5MB';
                    return;
                }
                this.form.resume = file;
                this.errors.resume = '';
            }
        },
        validateField(field) {
            if (field === 'coverLetter') {
                this.errors.coverLetter = !this.form.coverLetter
                    ? 'Cover letter is required'
                    : this.form.coverLetter.length < 50
                        ? 'Cover letter must be at least 50 characters'
                        : this.form.coverLetter.length > 1000
                            ? 'Cover letter must be less than 1000 characters'
                            : '';
            }
        },
        validateForm() {
            this.validateField('coverLetter');
            if (!this.form.resume) {
                this.errors.resume = 'Resume is required';
            }
            return !Object.values(this.errors).some(error => error);
        },
        async submitForm() {
            this.submitError = '';
            if (!this.validateForm()) {
                return;
            }

            this.submitting = true;

            const formData = new FormData();
            formData.append('job_id', this.jobId);
            formData.append('cover_letter', this.form.coverLetter);
            formData.append('resume', this.form.resume);

            try {
                const token = localStorage.getItem('auth_token');

                const response = await axios.post('http://127.0.0.1:8000/api/applications', formData, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'                    }
                });

                this.$router.push({
                    name: 'ThankYouPage',
                    query: {
                        jobId: this.jobId,
                        jobTitle: this.jobTitle
                    }
                });
            } catch (error) {
                this.submitError = error.response?.data?.message ||
                    'Something went wrong while submitting your application. Please try again later.';
                console.error('Application error:', error);
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>

<style scoped>
.container {
    max-width: 700px;
}

.card {
    border-radius: 10px;
    border: 1px solid #e0e0e0;
}

.form-control {
    border-radius: 6px;
    padding: 10px 15px;
}

.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    color: #dc3545;
    font-size: 0.875em;
}

textarea.form-control {
    min-height: 150px;
}

.btn {
    border-radius: 6px;
    padding: 8px 20px;
    transition: all 0.3s;
}

.btn-primary {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.btn-primary:hover {
    background-color: #2563eb;
    border-color: #2563eb;
}

.btn-outline-secondary {
    border-color: #d1d5db;
}

.btn-outline-secondary:hover {
    background-color: #f3f4f6;
}

.spinner-border {
    width: 1rem;
    height: 1rem;
}
</style>
