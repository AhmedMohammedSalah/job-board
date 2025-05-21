<template>
  <div class="dashboard-container">
    <SidebarComponent
      :initialActive="0"
      @navigate="handleNavigation"
      @logout="handleLogout"
    />

    <div class="main-content">
      <div class="content-wrapper">
        <div v-if="employerStore.isLoading" class="loading-overlay">
          <div class="loading-spinner"></div>
          <p>Loading dashboard...</p>
        </div>

        <div v-if="employerStore.error" class="error-alert">
          <i class="bi bi-exclamation-circle"></i>
          <p>{{ employerStore.error }}</p>
          <button @click="employerStore.fetchDashboardData">Retry</button>
        </div>

        <div v-if="!employerStore.isLoading && !employerStore.error">
          <div class="page-header">
            <h1>Hello, {{ user.name || "Employer" }}</h1>
            <p>Here is your daily activities and applications</p>
          </div>

          <div class="stats-container">
            <div class="stats-card stats-card-blue">
              <div class="stats-info">
                <h2>{{ employerStore.stats.openJobs }}</h2>
                <p>Open Jobs</p>
              </div>
              <div class="stats-icon stats-icon-blue">
                <i class="bi bi-briefcase"></i>
              </div>
            </div>

            <div class="stats-card stats-card-yellow">
              <div class="stats-info">
                <h2>{{ employerStore.stats.savedCandidates }}</h2>
                <p>Saved Candidates</p>
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

              <div v-if="employerStore.jobs.length === 0" class="no-jobs">
                <p>
                  No jobs posted yet.
                  <router-link to="/employer/jobs/create"
                    >Create your first job</router-link
                  >
                </p>
              </div>

              <div class="table-body">
                <div
                  v-for="(job, index) in employerStore.jobs"
                  :key="job.id"
                  :class="[
                    'job-row',
                    { 'selected-job': job.id === employerStore.selectedJob },
                  ]"
                >
                  <div class="col-job">
                    <h3>{{ job.title }}</h3>
                    <p>{{ job.job_type }} • {{ job.timeRemaining }}</p>
                  </div>
                  <div class="col-status">
                    <span
                      :class="[
                        'status-badge',
                        job.status === 'Active'
                          ? 'status-active'
                          : 'status-expired',
                      ]"
                    >
                      <span
                        :class="[
                          'status-dot',
                          job.status === 'Active'
                            ? 'status-dot-active'
                            : 'status-dot-expired',
                        ]"
                      ></span>
                      {{ job.status }}
                    </span>
                  </div>
                  <div class="col-applications">
                    <i class="bi bi-people"></i>
                    <span>{{ job.applications_count }} Applications</span>
                  </div>
                  <div class="col-actions">
                    <button
                      class="btn-view"
                      @click="employerStore.showApplications(job.id)"
                    >
                      View Applications
                    </button>
                    <button class="btn-menu" @click="toggleJobMenu(job.id)">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>

                    <div
                      v-if="
                        job.id === employerStore.selectedJob && showActionMenu
                      "
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
                        <li
                          class="menu-item"
                          @click="employerStore.toggleJobStatus(job.id)"
                        >
                          <i class="bi bi-check-circle"></i>
                          {{
                            job.status === "Active"
                              ? "Mark as expired"
                              : "Reactivate"
                          }}
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div
            v-if="employerStore.showApplicationsPanel"
            class="applications-panel"
          >
            <div class="applications-header">
              <h2>
                Applications for {{ employerStore.selectedJobDetails?.title }}
              </h2>
              <button
                class="close-btn"
                @click="employerStore.closeApplications"
              >
                <i class="bi bi-x"></i>
              </button>
            </div>
            <div class="applications-content">
              <div
                v-for="(applicant, index) in employerStore.selectedJobDetails
                  ?.applicants || []"
                :key="index"
                class="applicant-card"
              >
                <div class="applicant-info">
                  <div class="applicant-avatar">
                    <i class="bi bi-person-circle"></i>
                  </div>
                  <div class="applicant-details">
                    <h3>{{ applicant.name }}</h3>
                    <p>{{ applicant.position }}</p>
                    <div class="applicant-skills">
                      <span
                        v-for="(skill, idx) in applicant.skills"
                        :key="idx"
                        class="skill-tag"
                      >
                        {{ skill }}
                      </span>
                    </div>
                  </div>
                </div>
                <div class="applicant-actions">
                  <button class="btn-review">Review</button>
                  <button class="btn-contact">Contact</button>
                </div>
              </div>
              <div
                v-if="
                  employerStore.selectedJobDetails?.applicants?.length === 0
                "
                class="no-applicants"
              >
                No applications available to display
              </div>
            </div>
          </div>
          <div
            v-if="employerStore.showApplicationsPanel"
            class="overlay"
            @click="employerStore.closeApplications"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useEmployerStore } from "../../services/employerStore";
import { useAuthStore } from "../../services/authStore";
import SidebarComponent from "./SidebarComponent.vue";

const employerStore = useEmployerStore();
const authStore = useAuthStore();
const showActionMenu = ref(false);

// Properly imported computed
const user = computed(() => authStore.user);

// Fetch data when component mounts
onMounted(() => {
  employerStore.fetchDashboardData();
});

const toggleJobMenu = (jobId) => {
  if (employerStore.selectedJob === jobId && showActionMenu.value) {
    showActionMenu.value = false;
  } else {
    employerStore.setSelectedJob(jobId);
    showActionMenu.value = true;
  }
};

const handleNavigation = (route) => {
  console.log(`Navigating to: ${route}`);
};

const handleLogout = async () => {
  try {
    await authStore.logout();
    window.location.href = "/login";
  } catch (err) {
    console.error("Error logging out:", err);
  }
};
</script>

<style scoped>
.dashboard-container {
  display: flex;
  height: 100vh;
  background-color: #f8f9fa;
}

/* Main Content Styles */
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

.status-dot {
  width: 0.5rem;
  height: 0.5rem;
  border-radius: 50%;
  margin-right: 0.25rem;
}

.status-dot-active {
  background-color: #28a745;
}

.status-dot-expired {
  background-color: #dc3545;
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

.menu-item-blue {
  color: #2c7be5;
}

.menu-item i {
  margin-right: 0.5rem;
  font-size: 0.75rem;
}

/* Applications Panel Styles */
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

.applicant-skills {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.skill-tag {
  background-color: #e9f0ff;
  color: #2c7be5;
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
  border-radius: 1rem;
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
