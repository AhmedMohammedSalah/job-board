<template>
    <div class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
        <div class="container bg-white rounded shadow-sm p-4" style="max-width: 1200px;">
            <div v-if="loading || error" class="d-flex justify-content-center align-items-center vh-100">
                <div v-if="loading" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 fw-semibold text-secondary fs-5">Loading job details...</p>
                </div>

                <div v-else-if="error" class="alert alert-danger d-flex align-items-center gap-3 shadow rounded-4 p-4"
                    role="alert" style="max-width: 500px;">
                    <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                    <div class="flex-grow-1 fs-6">
                        {{ error }}
                    </div>
                </div>
            </div>

            <div v-else class="p-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <!-- Logo + Info -->
                    <div class="d-flex align-items-center">
                        <div class="company-logo-placeholder rounded-circle me-3 d-flex align-items-center justify-content-center"
                            style="width: 60px; height: 60px; background-color: #f0f0f0;">
                            <i class="bi bi-building text-muted fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">{{ job.title }}</h5>
                            <p class="mb-1 text-muted d-flex align-items-center">
                                <span class="badge bg-success me-2">{{ job.work_type }}</span>
                                <span class="badge bg-light text-danger border">Featured</span>
                            </p>
                        </div>
                    </div>

                    <!-- Button -->
                    <div class="d-flex align-items-center gap-2">
                        <router-link :to="{ name: 'ApplyJob', query: { jobId: id, jobTitle: job.title } }"
                            class="btn btn-primary d-flex align-items-center">
                            Apply Now
                            <i class="bi bi-arrow-right ms-2"></i>
                        </router-link>
                    </div>
                </div>

                <div class="row mb-5">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="job-description-container text-start">
                            <!-- Description Section -->
                            <div class="description-section mb-4">
                                <h4 class="section-title mb-3">Job Description</h4>
                                <p class="description-text">{{ job.description }}</p>
                            </div>

                            <!-- Requirements Section -->
                            <div class="requirements-section mb-4">
                                <h4 class="section-title mb-3">Requirements</h4>
                                <ul class="requirements-list">
                                    <li class="requirement-item">
                                        {{ job.requirements }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Benefits Section -->
                            <div class="benefits-section mb-4">
                                <h4 class="section-title mb-3">Benefits</h4>
                                <ul class="benefits-list">
                                    <li class="benefit-item">
                                        {{ job.benefits }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="job-info-card card shadow-sm p-4 mb-4">
                            <!-- Salary & Location -->
                            <div class="row mb-4 border p-3 rounded">
                                <div class="col-md-6">
                                    <h6 class="section-title text-muted mb-2">Salary (EGP)</h6>
                                    <p class="salary-amount fw-bold text-success">
                                        {{ job.min_salary }} - {{ job.max_salary }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="section-title text-muted mb-2">Job Location</h6>
                                    <p class="location text-muted">{{ job.location }}</p>
                                </div>
                            </div>

                            <!-- Remote Job -->
                            <div class="position-absolute top-1 end-0 translate-middle-y" style="right: -20px;">
                                <span class="badge bg-primary text-light">
                                    <i class="bi bi-globe me-1"></i> {{ job.work_type === 'remote' ? 'Remote' :
                                    'On-site' }}
                                </span>
                            </div>

                            <!-- Job Overview -->
                            <div class="border p-3 rounded">
                                <h6 class="section-title text-muted mb-3 text-start">Job Overview</h6>
                                <div class="row text-center">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-calendar-x text-primary mb-1 fs-4"></i>
                                            <p class="small text-muted mb-1">APPLICATION DEADLINE</p>
                                            <p class="fw-bold">{{ formatDate(job.deadline) }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-bar-chart-line text-primary mb-1 fs-4"></i>
                                            <p class="small text-muted mb-1">STATUS</p>
                                            <p class="fw-bold text-capitalize">{{ job.status }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Share Section -->
                                <div class="share-section mb-4">
                                    <h6 class="section-title text-muted mb-2 text-start">Share this job:</h6>
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <button class="btn btn-outline-primary btn-sm d-flex align-items-center">
                                            <i class="bi bi-link-45deg me-1"></i>
                                            Copy Links
                                        </button>
                                        <a href="#" class="text-decoration-none text-primary fs-5"><i
                                                class="bi bi-facebook"></i></a>
                                        <a href="#" class="text-decoration-none text-info fs-5"><i
                                                class="bi bi-twitter"></i></a>
                                        <a href="#" class="text-decoration-none text-primary fs-5"><i
                                                class="bi bi-linkedin"></i></a>
                                        <a href="#" class="text-decoration-none text-primary fs-5"><i
                                                class="bi bi-envelope-fill"></i></a>
                                    </div>
                                </div>

                                <!-- Job Tags -->
                                <div class="tags-section">
                                    <h6 class="section-title text-muted mb-2 text-start">Job tags:</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-light text-dark border">Frontend</span>
                                        <span class="badge bg-light text-dark border">Vue.js</span>
                                        <span class="badge bg-light text-dark border">Web Development</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: ['id'],
    data() {
        return {
            job: {},
            loading: true,
            error: null
        }
    },
    created() {
        this.fetchJobDetails();
    },
    methods: {
        async fetchJobDetails() {
            const token = localStorage.getItem('auth_token');
            try {
                const response = await axios.get(`http://127.0.0.1:8000/api/singleJob/${this.id}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                this.job = response.data.job;
            } catch (err) {
                console.error("Error fetching job details:", err);
                this.error = err.response?.data?.message ||
                    "Failed to load job details. Please try again later.";
            } finally {
                this.loading = false;
            }
        },
        formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }
    }
}
</script>

<style scoped>
.min-vh-100 {
    min-height: 100vh;
}

.container {
    background-color: #ffffff;
    margin: 20px auto;
}

.job-info-card {
    position: sticky;
    top: 20px;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
}

.description-text {
    white-space: pre-line;
}

.requirements-list,
.benefits-list {
    padding-left: 20px;
}

.requirement-item,
.benefit-item {
    margin-bottom: 8px;
}

.company-logo-placeholder {
    background-color: #f8f9fa;
    color: #6c757d;
}
</style>