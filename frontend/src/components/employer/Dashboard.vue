<template>
  <div class="dashboard-container">
    <SidebarComponent
      :initialActive="0"
      @navigate="handleNavigation"
      @logout="handleLogout"
    />

    <div class="main-content">
      <div class="content-wrapper">
        <div v-if="isLoading" class="loading-overlay">
          <div class="loading-spinner"></div>
          <p>Loading dashboard...</p>
        </div>

        <div v-if="error" class="error-alert">
          <i class="bi bi-exclamation-circle"></i>
          <p>{{ error }}</p>
          <button @click="fetchDashboardData">Retry</button>
        </div>

        <div v-if="!isLoading && !error">
          <div class="page-header">
            <h1>Hello, {{ user?.name || "Employer" }}</h1>
            <p>Here is your daily activities and applications</p>
          </div>

          <div class="stats-container">
            <div class="stats-card stats-card-blue">
              <div class="stats-info">
                <h2>{{ stats.openJobs }}</h2>
                <p>Open Jobs</p>
              </div>
              <div class="stats-icon stats-icon-blue">
                <i class="bi bi-briefcase"></i>
              </div>
            </div>

            <div class="stats-card stats-card-yellow">
              <div class="stats-info">
                <h2>{{ stats.totalApplications }}</h2>
                <p>Total Applications</p>
              </div>
              <div class="stats-icon stats-icon-yellow">
                <i class="bi bi-person"></i>
              </div>
            </div>
          </div>

          <div class="jobs-container">
            <div class="jobs-header">
              <h2>Recently Posted Jobs</h2>
              <router-link to="/employer/jobs" class="view-all">
                View all
                <i class="bi bi-chevron-right"></i>
              </router-link>
            </div>

            <div class="jobs-table">
              <div class="table-header">
                <div class="col-job">JOBS</div>
                <div class="col-status">STATUS</div>
                <div class="col-applications">APPLICATIONS</div>
                <div class="col-actions">ACTIONS</div>
              </div>

              <div v-if="jobs.length === 0" class="no-jobs">
                <p>
                  No jobs posted yet.
                  <router-link to="/employer/post-job"
                    >Create your first job</router-link
                  >
                </p>
              </div>

              <div class="table-body">
                <div
                  v-for="job in jobs"
                  :key="job.id"
                  :class="[
                    'job-row',
                    { 'selected-job': job.id === selectedJobId },
                  ]"
                >
                  <div class="col-job">
                    <h3>{{ job.title }}</h3>
                    <p>
                      {{ job.employment_type || "N/A" }} •
                      {{ formatTimeRemaining(job.created_at) }}
                    </p>
                  </div>
                  <div class="col-status">
                    <span :class="['status-badge', getStatusClass(job.status)]">
                      <span
                        :class="['status-dot', getStatusClass(job.status)]"
                      ></span>
                      {{
                        job.status.charAt(0).toUpperCase() + job.status.slice(1)
                      }}
                    </span>
                  </div>
                  <div class="col-applications">
                    <i class="bi bi-people"></i>
                    <span>{{ job.applications_count || 0 }} Applications</span>
                  </div>
                  <div class="col-actions">
                    <button class="btn-view" @click="showApplications(job.id)">
                      View Applications
                    </button>
                    <button class="btn-menu" @click="toggleJobMenu(job.id)">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>

                    <div
                      v-if="job.id === selectedJobId && showActionMenu"
                      class="action-menu"
                    >
                      <ul>
                        <li
                          class="menu-item"
                          @click="$router.push(`/employer/jobs/${job.id}`)"
                        >
                          <i class="bi bi-eye"></i>
                          View Detail
                        </li>
                        <li class="menu-item" @click="toggleJobStatus(job.id)">
                          <i class="bi bi-check-circle"></i>
                          {{ job.is_active ? "Mark as Expired" : "Reactivate" }}
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-if="showApplicationsPanel" class="applications-panel">
            <div class="applications-header">
              <h2>Applications for {{ selectedJobDetails?.title }}</h2>
              <button class="close-btn" @click="closeApplications">
                <i class="bi bi-x"></i>
              </button>
            </div>
            <div class="applications-content">
              <div v-if="loadingApplications" class="loading-applications">
                <div class="loading-spinner"></div>
                <p>Loading applications...</p>
              </div>

              <div v-else>
                <div
                  v-for="applicant in selectedJobDetails?.applications || []"
                  :key="applicant.id"
                  class="applicant-card"
                >
                  <div class="applicant-info">
                    <div class="applicant-avatar">
                      <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="applicant-details">
                      <h3>{{ applicant.user?.name || "N/A" }}</h3>
                      <p>{{ applicant.user?.title || "Job Seeker" }}</p>
                      <div class="application-meta">
                        <small
                          >Applied:
                          {{ formatDate(applicant.created_at) }}</small
                        >
                        <span
                          :class="[
                            'status-badge',
                            `status-${applicant.status}`,
                          ]"
                        >
                          {{
                            applicant.status.charAt(0).toUpperCase() +
                            applicant.status.slice(1)
                          }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="applicant-actions">
                    <button
                      class="btn-review"
                      @click="reviewApplication(applicant)"
                    >
                      Review
                    </button>
                    <button
                      class="btn-contact"
                      @click="contactApplicant(applicant)"
                    >
                      Contact
                    </button>
                  </div>
                </div>

                <div
                  v-if="selectedJobDetails?.applications?.length === 0"
                  class="no-applicants"
                >
                  No applications available to display
                </div>
              </div>
            </div>
          </div>

          <div
            v-if="showApplicationsPanel"
            class="overlay"
            @click="closeApplications"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useAuthStore } from "../../services/authStore";
import { useRouter } from "vue-router";
import SidebarComponent from "./SidebarComponent.vue";
import axios from "axios";

const authStore = useAuthStore();
const router = useRouter();

const isLoading = ref(false);
const error = ref(null);
const jobs = ref([]);
const stats = ref({
  openJobs: 0,
  totalApplications: 0,
});
const showActionMenu = ref(false);
const selectedJobId = ref(null);
const showApplicationsPanel = ref(false);
const selectedJobDetails = ref(null);
const loadingApplications = ref(false);

const user = computed(() => authStore.user);

const fetchDashboardData = async () => {
  // if (!authStore.token) {
  //   router.push("/login");
  //   return;
  // }

  isLoading.value = true;
  error.value = null;

  try {
    const response = await axios.get("/api/jobs", {
      headers: { Authorization: `Bearer ${authStore.token}` },
    });

    jobs.value = response.data.data || [];

    stats.value = {
      openJobs: jobs.value.filter(
        (job) => job.status === "published" || job.is_active
      ).length,
      totalApplications: jobs.value.reduce(
        (sum, job) => sum + (job.applications_count || 0),
        0
      ),
    };
  } catch (err) {
    error.value =
      err.response?.data?.message || "Failed to load dashboard data";
  } finally {
    isLoading.value = false;
  }
};

const showApplications = async (jobId) => {
  loadingApplications.value = true;
  showApplicationsPanel.value = true;

  try {
    const response = await axios.get(`/api/jobs/${jobId}/applications`, {
      headers: { Authorization: `Bearer ${authStore.token}` },
    });

    const job = jobs.value.find((j) => j.id == jobId);
    selectedJobDetails.value = {
      title: job?.title || "Job",
      applications: response.data.data || [],
    };
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to load applications";
  } finally {
    loadingApplications.value = false;
  }
};

const toggleJobStatus = async (jobId) => {
  try {
    await axios.patch(
      `/api/jobs/${jobId}/toggle-active`,
      {},
      {
        headers: { Authorization: `Bearer ${authStore.token}` },
      }
    );

    const job = jobs.value.find((j) => j.id == jobId);
    if (job) {
      job.is_active = !job.is_active;
      job.status = job.is_active ? "published" : "expired";
    }

    stats.value.openJobs = jobs.value.filter(
      (job) => job.status === "published" || job.is_active
    ).length;
    showActionMenu.value = false;
    selectedJobId.value = null;
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to update job status";
  }
};

const reviewApplication = (applicant) => {
  router.push(`/employer/applications/${applicant.id}`);
};

const contactApplicant = (applicant) => {
  window.location.href = `mailto:${applicant.user.email}`;
};

const closeApplications = () => {
  showApplicationsPanel.value = false;
  selectedJobDetails.value = null;
};

const handleNavigation = (index) => {
  const routes = [
    "/employer/dashboard",
    "/employer/jobs",
    "/employer/post-job",
  ];
  router.push(routes[index]);
};

const handleLogout = async () => {
  await authStore.logout();
  router.push("/login");
};

const toggleJobMenu = (jobId) => {
  if (selectedJobId.value === jobId) {
    showActionMenu.value = !showActionMenu.value;
  } else {
    selectedJobId.value = jobId;
    showActionMenu.value = true;
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
};

const formatTimeRemaining = (createdAt) => {
  const created = new Date(createdAt);
  const now = new Date();
  const diffDays = Math.ceil((now - created) / (1000 * 60 * 60 * 24));
  return `${diffDays} days ago`;
};

const getStatusClass = (status) => {
  switch (status) {
    case "published":
    case "active":
      return "status-active";
    case "draft":
      return "status-draft";
    case "expired":
    case "rejected":
      return "status-expired";
    case "pending":
      return "status-pending";
    default:
      return "status-pending";
  }
};

onMounted(async () => {
  if (!authStore.user) {
    try {
      await authStore.fetchUser();
    } catch (err) {
      router.push("/login");
    }
  }
  fetchDashboardData();
});
</script>

<style scoped>
.dashboard-container {
  display: flex;
  height: 100vh;
  background-color: #f8f9fa;
}

.main-content {
  flex: 1;
  overflow: auto;
  position: relative;
}

.content-wrapper {
  padding: 1.5rem;
  position: relative;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 1.25rem;
  font-weight: 500;
  margin-bottom: 0.25rem;
}

.page-header p {
  color: #6c757d;
  font-size: 0.875rem;
  margin: 0;
}

.stats-container {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stats-card {
  border-radius: 0.5rem;
  padding: 1rem;
  display: flex;
  align-items: center;
  width: 250px;
}

.stats-card-blue {
  background-color: #e9f0ff;
}

.stats-card-yellow {
  background-color: #fff8e1;
}

.stats-info {
  flex: 1;
}

.stats-info h2 {
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0;
}

.stats-info p {
  color: #495057;
  font-size: 0.875rem;
  margin: 0;
}

.stats-icon {
  width: 40px;
  height: 40px;
  border-radius: 0.5rem;
  background-color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stats-icon-blue i {
  color: #2c7be5;
}

.stats-icon-yellow i {
  color: #f7c848;
}

.jobs-container {
  background-color: #fff;
  border-radius: 0.5rem;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.jobs-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
}

.jobs-header h2 {
  font-size: 1rem;
  font-weight: 500;
  margin: 0;
}

.view-all {
  font-size: 0.875rem;
  color: #6c757d;
  display: flex;
  align-items: center;
  text-decoration: none;
}

.view-all i {
  margin-left: 0.25rem;
  font-size: 0.75rem;
}

.jobs-table {
  width: 100%;
}

.table-header {
  display: grid;
  grid-template-columns: 4fr 2fr 3fr 3fr;
  padding: 0.75rem 1rem;
  background-color: #f8f9fa;
  font-size: 0.75rem;
  font-weight: 500;
  color: #6c757d;
}

.job-row {
  display: grid;
  grid-template-columns: 4fr 2fr 3fr 3fr;
  padding: 1rem;
  border-top: 1px solid #f1f1f1;
  align-items: center;
  position: relative;
  transition: all 0.2s ease;
}

.job-row.selected-job {
  background-color: #e9f0ff;
  border: 1px solid #c9d8f3;
  border-radius: 0.25rem;
  margin: 0.25rem 0.5rem;
}

.col-job h3 {
  font-size: 0.9375rem;
  font-weight: 500;
  margin: 0;
  margin-bottom: 0.25rem;
}

.col-job p {
  font-size: 0.875rem;
  color: #6c757d;
  margin: 0;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.5rem;
  border-radius: 1rem;
  font-size: 0.75rem;
}

.status-active {
  color: #28a745;
}

.status-expired {
  color: #dc3545;
}

.status-pending {
  color: #ffc107;
}

.status-draft {
  color: #6c757d;
}

.status-dot {
  width: 0.5rem;
  height: 0.5rem;
  border-radius: 50%;
  margin-right: 0.25rem;
}

.status-dot.status-active {
  background-color: #28a745;
}

.status-dot.status-expired {
  background-color: #dc3545;
}

.status-dot.status-pending {
  background-color: #ffc107;
}

.status-dot.status-draft {
  background-color: #6c757d;
}

.col-applications {
  display: flex;
  align-items: center;
  color: #6c757d;
  font-size: 0.875rem;
}

.col-applications i {
  margin-right: 0.5rem;
}

.col-actions {
  display: flex;
  align-items: center;
}

.btn-view {
  background-color: #2c7be5;
  color: white;
  border: none;
  border-radius: 0.25rem;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-view:hover {
  background-color: #1a68d1;
}

.btn-menu {
  background: none;
  border: none;
  color: #adb5bd;
  margin-left: 0.5rem;
  cursor: pointer;
  padding: 0.25rem;
}

.btn-menu:hover {
  color: #6c757d;
}

.action-menu {
  position: absolute;
  right: 3rem;
  top: 4rem;
  background-color: white;
  border: 1px solid #e9ecef;
  border-radius: 0.375rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
  width: 12rem;
  z-index: 10;
}

.action-menu ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  color: #495057;
  cursor: pointer;
}

.menu-item:hover {
  background-color: #f8f9fa;
}

.menu-item i {
  margin-right: 0.5rem;
  font-size: 0.75rem;
}

.applications-panel {
  position: fixed;
  top: 0;
  right: 0;
  width: 450px;
  height: 100vh;
  background-color: white;
  box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  overflow-y: auto;
  transform: translateX(0);
  transition: transform 0.3s ease;
}

.applications-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #e9ecef;
  position: sticky;
  top: 0;
  background-color: white;
  z-index: 1;
}

.applications-header h2 {
  font-size: 1.125rem;
  font-weight: 500;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.25rem;
  color: #6c757d;
  cursor: pointer;
}

.applications-content {
  padding: 1rem;
}

.loading-applications {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 2rem;
}

.applicant-card {
  background-color: #f8f9fa;
  border-radius: 0.5rem;
  padding: 1rem;
  margin-bottom: 1rem;
}

.applicant-info {
  display: flex;
  margin-bottom: 1rem;
}

.applicant-avatar {
  width: 50px;
  height: 50px;
  background-color: #e9ecef;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
}

.applicant-avatar i {
  font-size: 1.5rem;
  color: #6c757d;
}

.applicant-details h3 {
  font-size: 1rem;
  font-weight: 500;
  margin: 0;
  margin-bottom: 0.25rem;
}

.applicant-details p {
  font-size: 0.875rem;
  color: #6c757d;
  margin: 0;
  margin-bottom: 0.5rem;
}

.application-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.application-meta small {
  color: #6c757d;
}

.status-pending {
  background-color: #fff3cd;
  color: #856404;
}

.status-accepted {
  background-color: #d4edda;
  color: #155724;
}

.status-rejected {
  background-color: #f8d7da;
  color: #721c24;
}

.status-shortlisted {
  background-color: #e7f3fe;
  color: #004085;
}

.status-hired {
  background-color: #d1ecf1;
  color: #0c5460;
}

.applicant-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-review {
  background-color: #2c7be5;
  color: white;
  border: none;
  border-radius: 0.25rem;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  cursor: pointer;
  flex: 1;
}

.btn-contact {
  background-color: white;
  color: #2c7be5;
  border: 1px solid #2c7be5;
  border-radius: 0.25rem;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  cursor: pointer;
  flex: 1;
}

.no-applicants {
  text-align: center;
  color: #6c757d;
  padding: 2rem 0;
}

.overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 999;
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(255, 255, 255, 0.8);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.loading-spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #2c7be5;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.error-alert {
  background-color: #f8d7da;
  color: #721c24;
  padding: 1rem;
  border-radius: 0.25rem;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.error-alert i {
  font-size: 1.5rem;
}

.error-alert button {
  margin-left: auto;
  background-color: #dc3545;
  color: white;
  border: none;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  cursor: pointer;
}

.no-jobs {
  padding: 2rem;
  text-align: center;
  color: #6c757d;
}

.no-jobs a {
  color: #2c7be5;
  text-decoration: none;
}

.no-jobs a:hover {
  text-decoration: underline;
}
</style>
