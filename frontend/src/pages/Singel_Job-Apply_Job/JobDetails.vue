import axios from 'axios';
<template>
    <div class="container bg-white">
        <div v-if="loading || error" class="d-flex justify-content-center align-items-center vh-100">
  <div v-if="loading" class="text-center">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-3 fw-semibold text-secondary fs-5">Loading job details...</p>
  </div>

  <div v-else-if="error" class="alert alert-danger d-flex align-items-center gap-3 shadow rounded-4 p-4" role="alert" style="max-width: 500px;">
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
                    <img :src="job.companyImage" alt="Company Logo" class="rounded-circle me-3" width="60"
                        height="60" />
                    <div>
                        <h5 class="mb-1 fw-bold">{{ job.title }}</h5>
                        <p class="mb-1 text-muted d-flex align-items-center">
                            at {{ company }}
                            <span class="badge bg-success ms-2">{{ job.type }}</span>
                            <span class="badge bg-light text-danger border ms-2">Featured</span>
                        </p>
                    </div>
                </div>

                <!-- Bookmark + Button -->
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-light border rounded-circle p-2">
                        <i class="bi bi-bookmark"></i>
                    </button>
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
                                <li v-for="(req, index) in job.requirements" :key="'req' + index"
                                    class="requirement-item">
                                    {{ req }}
                                </li>
                            </ul>
                        </div>

                        <!-- Desirable Section -->
                        <div class="desirable-section mb-4" v-if="job.desirable && job.desirable.length">
                            <h4 class="section-title mb-3">Desirable:</h4>
                            <ul class="desirable-list">
                                <li v-for="(item, index) in job.desirable" :key="'des' + index" class="desirable-item">
                                    {{ item }}
                                </li>
                            </ul>
                        </div>

                        <!-- Benefits Section -->
                        <div class="benefits-section mb-4" v-if="job.benefits && job.benefits.length">
                            <h4 class="section-title mb-3">Benefits</h4>
                            <ul class="benefits-list">
                                <li v-for="(benefit, index) in job.benefits" :key="'ben' + index" class="benefit-item">
                                    {{ benefit }}
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
                                <h6 class="section-title text-muted mb-2">Salary (USD)</h6>
                                <p class="salary-amount fw-bold text-success">{{ job.salary }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="section-title text-muted mb-2">Job Location</h6>
                                <p class="location text-muted">{{ job.location }}</p>
                            </div>
                        </div>

                        <!-- Remote Job -->
                        <div class="position-absolute top-0 end-0 translate-middle-y" style="right: -20px;">
                            <span class="badge bg-primary text-light">
                                <i class="bi bi-globe me-1"></i> {{ job.remote }}
                            </span>
                        </div>

                        <!-- Job Benefits -->
                        <div class="benefits-section mb-4 border p-3 rounded">
                            <h6 class="section-title text-muted mb-2 text-start">Job Benefits</h6>
                            <div v-if="job.jobBenefits" class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-success border"
                                    v-for="(benefit, index) in job.jobBenefits" :key="index">
                                    {{ benefit }}
                                </span>
                            </div>
                        </div>

                        <!-- Job Overview -->
                        <div class="border p-3 rounded">
                            <h6 class="section-title text-muted mb-3 text-start">Job Overview</h6>
                            <div class="row text-center">
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-calendar-check text-primary mb-1 fs-4"></i>
                                        <p class="small text-muted mb-1">JOB POSTED:</p>
                                        <p class="fw-bold">{{ job.posted }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-calendar-x text-primary mb-1 fs-4"></i>
                                        <p class="small text-muted mb-1">JOB EXPIRE IN</p>
                                        <p class="fw-bold">{{ job.expires }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-bar-chart-line text-primary mb-1 fs-4"></i>
                                        <p class="small text-muted mb-1">JOB LEVEL:</p>
                                        <p class="fw-bold">{{ job.level }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row text-center">
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-briefcase text-primary mb-1 fs-4"></i>
                                        <p class="small text-muted mb-1">EXPERIENCE:</p>
                                        <p class="fw-bold">{{ job.experience }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-mortarboard text-primary mb-1 fs-4"></i>
                                        <p class="small text-muted mb-1">EDUCATION</p>
                                        <p class="fw-bold">{{ job.education }}</p>
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
                                    <span class="badge bg-light text-dark border" v-for="(tag, index) in job.tags"
                                        :key="index">
                                        {{ tag }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Jobs Section -->
            <div class="related-jobs-section my-5">
                <h4 class="section-title mb-4 text-start">Related Jobs</h4>
                <div class="row">
                    <div class="col-md-4 mb-4" v-for="(relatedJob, index) in job.relatedJobs"
                        :key="'related-job-' + index">
                        <div class="card shadow-sm border-light">
                            <div class="card-body">
                                <h5 class="card-title text-start">{{ relatedJob.title }}</h5>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="badge bg-primary text-white">{{ relatedJob.type }}</span>
                                    <span class="text-muted">Salary: {{ relatedJob.salary }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <p class="card-text text-muted mb-0">
                                        <i class="bi bi-geo-alt me-2"></i>{{ relatedJob.location }}
                                    </p>
                                    <i class="bi bi-bookmark"></i>
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
    computed: {
        job() {
            return {
                title: this.job.title,
                description: this.job.description,
                requirements: this.job.requirements ? this.job.requirements.split(',') : [],
                benefits: this.job.benefits ? this.job.benefits.split(',') : [],
                work_type: this.job.work_type,
                location: this.job.location,
                salary: `${this.job.min_salary} - ${this.job.max_salary}`,
                posted: new Date(this.job.created_at).toLocaleDateString(),
                expires: new Date(this.job.deadline).toLocaleDateString(),
                remote: this.job.work_type === 'remote' ? 'Remote' : 'On-site',
            };
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
        }
    }
}
</script>

<style scoped>
.container {
    background-color: #ffffff;
}

.related-jobs-section {
    border-top: 1px solid #ddd;
    padding-top: 20px;
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
</style>