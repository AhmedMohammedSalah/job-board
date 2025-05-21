<template>
    <div class="job-details-page">

        <div class="breadcrumb-wrapper w-100 py-3 bg-light">
            <div class="container">
                <nav aria-label="breadcrumb" class="breadcrumb-nav">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <RouterLink :to="{ name: 'Home' }" class="text-decoration-none">Home</RouterLink>
                        </li>
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Find Job</a></li>
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Graphics & Design</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Job Details</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="container">
            <div v-if="loading" class="d-flex justify-content-center align-items-center vh-100">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 fw-semibold text-secondary fs-5">Loading job details...</p>
                </div>
            </div>

            <div v-else-if="error" class="d-flex justify-content-center align-items-center vh-100">
                <div class="alert alert-danger d-flex align-items-center gap-3 shadow rounded-4 p-4" role="alert"
                    style="max-width: 500px;">
                    <font-awesome-icon :icon="['fas', 'exclamation-triangle']" class="fs-3" />
                    <div class="flex-grow-1 fs-6">
                        {{ error }}
                    </div>
                </div>
            </div>

            <div v-else class="content p-4">
                <div class="job-header d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <div class="company-logo rounded-circle me-3 d-flex align-items-center justify-content-center">
                            <font-awesome-icon :icon="['fas', 'building']" class="text-muted fs-4" />
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">{{ job.title }}</h5>
                            <div class="d-flex align-items-center flex-wrap gap-2 mt-2">
                                <span class="badge bg-success">{{ job.work_type }}</span>
                                <span class="badge bg-warning text-dark">Featured</span>
                                <span class="badge bg-info" v-if="job.status === 'open'">Hiring</span>
                            </div>
                        </div>
                    </div>

                    <router-link :to="{ name: 'ApplyJob', query: { jobId: id, jobTitle: job.title } }"
                        class="btn btn-primary d-flex align-items-center apply-btn">
                        Apply Now
                        <font-awesome-icon :icon="['fas', 'arrow-right']" class="ms-2" />
                    </router-link>
                </div>

                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="job-content">
                            <div class="mb-5">
                                <h4 class="section-title mb-3 fw-bold text-primary">Job Description</h4>
                                <div class="description-text" v-html="formatText(job.description)"></div>
                            </div>
                            <div class="mb-5">
                                <h4 class="section-title mb-3 fw-bold text-primary">Requirements</h4>
                                <div class="requirements-list" v-html="formatText(job.requirements)"></div>
                            </div>
                            <div class="mb-4">
                                <h4 class="section-title mb-3 fw-bold text-primary">Benefits</h4>
                                <div class="benefits-list" v-html="formatText(job.benefits)"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="job-summary-card card shadow-sm position-relative">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-primary text-light">
                                    <i class="bi bi-globe me-1"></i>
                                    {{ job.work_type === 'remote' ? 'Remote' : 'On-site' }}
                                </span>
                            </div>

                            <div class="card-body p-4">
                                <div class="salary-location mb-4 p-3 bg-light rounded">
                                    <div class="row">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <h6 class="text-muted mb-2">
                                                <font-awesome-icon :icon="['fas', 'money-bill-wave']" class="me-2" />
                                                Salary
                                            </h6>
                                            <p class="fw-bold text-success mb-0">
                                                EGP {{ job.min_salary }} - {{ job.max_salary }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-muted mb-2">
                                                <font-awesome-icon :icon="['fas', 'map-marker-alt']" class="me-2" />
                                                Location
                                            </h6>
                                            <p class="fw-bold mb-0">{{ job.location }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="job-overview mb-4 p-3 border rounded">
                                    <h5 class="text-muted mb-3 fw-bold">
                                        <font-awesome-icon :icon="['fas', 'info-circle']" class="me-2" />
                                        Job Overview
                                    </h5>
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <font-awesome-icon :icon="['fas', 'calendar-alt']"
                                                class="text-primary mb-2 fs-4" />
                                            <p class="small text-muted mb-1">Posted On</p>
                                            <p class="fw-bold">{{ formatDate(job.created_at) }}</p>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <font-awesome-icon :icon="['fas', 'clock']"
                                                class="text-primary mb-2 fs-4" />
                                            <p class="small text-muted mb-1">Deadline</p>
                                            <p class="fw-bold">{{ formatDate(job.deadline) }}</p>
                                        </div>
                                        <div class="col-6">
                                            <font-awesome-icon :icon="['fas', 'briefcase']"
                                                class="text-primary mb-2 fs-4" />
                                            <p class="small text-muted mb-1">Job Type</p>
                                            <p class="fw-bold text-capitalize">{{ job.work_type }}</p>
                                        </div>
                                        <div class="col-6">
                                            <font-awesome-icon :icon="['fas', 'chart-line']"
                                                class="text-primary mb-2 fs-4" />
                                            <p class="small text-muted mb-1">Status</p>
                                            <p class="fw-bold text-capitalize">{{ job.status }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="share-job mb-4">
                                    <h5 class="text-muted mb-3 fw-bold">
                                        Share this job:
                                    </h5>
                                    <div class="d-flex align-items-center gap-2">
                                        <button @click="copyLink"
                                            class="btn btn-outline-primary d-flex align-items-center gap-1">
                                            <font-awesome-icon :icon="['fas', 'link']" />
                                            Copy Link
                                        </button>

                                        <a :href="linkedInUrl" target="_blank" rel="noopener"
                                            class="btn btn-outline-primary d-flex align-items-center justify-content-center p-2"
                                            style="width: 38px; height: 38px;">
                                            <font-awesome-icon :icon="['fab', 'linkedin-in']" />
                                        </a>
                                        <a :href="facebookUrl" target="_blank" rel="noopener"
                                            class="btn btn-outline-primary d-flex align-items-center justify-content-center p-2"
                                            style="width: 38px; height: 38px;">
                                            <font-awesome-icon :icon="['fab', 'facebook-f']" />
                                        </a>
                                        <a :href="twitterUrl" target="_blank" rel="noopener"
                                            class="btn btn-outline-primary d-flex align-items-center justify-content-center p-2"
                                            style="width: 38px; height: 38px;">
                                            <font-awesome-icon :icon="['fab', 'twitter']" />
                                        </a>
                                        <a :href="mailtoUrl" target="_blank" rel="noopener"
                                            class="btn btn-outline-primary d-flex align-items-center justify-content-center p-2"
                                            style="width: 38px; height: 38px;">
                                            <font-awesome-icon :icon="['fas', 'envelope']" />
                                        </a>
                                    </div>
                                    <div v-if="copySuccess" class="text-success mt-2">
                                        Link copied to clipboard!
                                    </div>
                                </div>

                                <div class="job-tags">
                                    <h5 class="text-muted mb-3 fw-bold">
                                        <font-awesome-icon :icon="['fas', 'tags']" class="me-2" />
                                        Job Tags
                                    </h5>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-light text-dark border">Frontend</span>
                                        <span class="badge bg-light text-dark border">Vue.js</span>
                                        <span class="badge bg-light text-dark border">Web Development</span>
                                        <span class="badge bg-light text-dark border">JavaScript</span>
                                        <span class="badge bg-light text-dark border">HTML/CSS</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <CommentsSection :jobId="jobId" :currentUser="currentUser" :isAuthenticated="isAuthenticated"
                    @auth-failed="handleAuthFailed" />
            </div>
        </div>
        <AppFooter />
    </div>
</template>

<script>
import AppHeader from '../../components/homePage/AppHeader.vue';
import Navbar from '../../components/homePage/Navbar.vue';
import AppFooter from '../../components/homePage/AppFooter.vue';
import CommentsSection from '../../components/Comments&Replies/CommentsSection.vue';
import axios from 'axios';
import { RouterLink } from 'vue-router';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

export default {
    name: 'JobDetails',
    components: {
        AppHeader,
        Navbar,
        AppFooter,
        CommentsSection,
        RouterLink,
        FontAwesomeIcon
    },
    props: ['id'],
    data() {
        return {
            job: {},
            loading: true,
            error: null,
            copySuccess: false,
            currentUser: null,
            isAuthenticated: false
        }
    },
    computed: {
        jobId() {
            return this.$route.params.id;
        },
        linkedInUrl() {
            return `https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(window.location.href)}&title=${encodeURIComponent(this.job.title)}`;
        },
        facebookUrl() {
            return `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`;
        },
        twitterUrl() {
            return `https://twitter.com/intent/tweet?url=${encodeURIComponent(window.location.href)}&text=${encodeURIComponent(`Check out this job: ${this.job.title}`)}`;
        },
        mailtoUrl() {
            return `mailto:?subject=${encodeURIComponent(`Job Opportunity: ${this.job.title}`)}&body=${encodeURIComponent(`Check out this job: ${window.location.href}`)}`;
        }
    },
    created() {
        this.fetchJobDetails();
        this.checkAuth();
    },
    methods: {
        async fetchJobDetails() {
            this.loading = true;
            this.error = null;
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
                this.error = err.response?.data?.message || "Failed to load job details. Please try again later.";
            } finally {
                this.loading = false;
            }
        },
        mounted() {
            try {
                const user = localStorage.getItem("user");
                this.currentUser = user ? JSON.parse(user) : { id: 1 };
            } catch (e) {
                this.currentUser = { id: 1 };
            }
            console.log('Current User:', this.localUser.id);
        },
        async checkAuth() {
            const token = localStorage.getItem('auth_token');
            if (token) {
                try {
                    const response = await axios.get('/api/user', {
                        headers: {
                            'Authorization': `Bearer ${token}`
                        }
                    });
                    this.currentUser = response.data;
                    this.isAuthenticated = true;
                } catch (error) {
                    localStorage.removeItem('auth_token');
                    this.isAuthenticated = false;
                }
            }
        },

        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        },

        formatText(text) {
            if (!text) return '';
            return text.replace(/\n/g, '<br>');
        },

        copyLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                this.copySuccess = true;
                setTimeout(() => {
                    this.copySuccess = false;
                }, 3000);
            }).catch(err => {
                console.error("Failed to copy: ", err);
            });
        },

        handleAuthFailed() {
            this.isAuthenticated = false;
            localStorage.removeItem('auth_token');
        }
    }
}
</script>

<style scoped>
.job-details-page {
    background-color: white;
    min-height: 100vh;
}

.header,
.breadcrumb-wrapper {
    background-color: #eeeeee;
    border-radius: 0 0 8px 8px;
}

.breadcrumb-item a {
    color: #64748b;
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb-item a:hover {
    color: #334155;
}

.breadcrumb-item.active {
    color: #334155;
    font-weight: 500;
}

.breadcrumb-item+.breadcrumb-item::before {
    content: "›";
    padding: 0 8px;
    color: #94a3b8;
}

.job-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.company-logo {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    border-radius: 12px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
}

.badge {
    font-weight: 500;
    letter-spacing: 0.3px;
}

.btn-primary {
    background-color: #3b82f6;
    border-color: #3b82f6;
    transition: all 0.3s;
}

.btn-primary:hover {
    background-color: #2563eb;
    border-color: #2563eb;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.text-success {
    color: #10b981 !important;
}

.text-primary {
    color: #3b82f6 !important;
}

.bg-primary {
    background-color: #3b82f6 !important;
}

@media (max-width: 768px) {
    .job-title {
        font-size: 1.5rem;
    }

    .company-logo {
        width: 60px;
        height: 60px;
    }

    .job-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1.5rem;
    }
}

@media (max-width: 576px) {
    .breadcrumb-nav {
        font-size: 0.85rem;
    }

    .job-title {
        font-size: 1.3rem;
    }
}
</style>