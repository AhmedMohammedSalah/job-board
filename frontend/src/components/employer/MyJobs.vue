<template>
  <div class="d-flex">
    <SidebarComponent />
    <div class="job-list-container">
      <div v-if="isLoading" class="loading-overlay">
        <div class="loading-spinner"></div>
        <p>Loading jobs...</p>
      </div>
      <div v-if="error" class="error-alert">
        <i class="bi bi-exclamation-circle"></i>
        <p>{{ error }}</p>
        <button @click="fetchJobs">Retry</button>
      </div>
      <div v-if="!isLoading && !error" class="job-header">
        <h4>
          My Jobs <span class="job-count">({{ jobs.length }})</span>
        </h4>
        <div class="filter-controls">
          <div class="job-status-filter">
            <span>Job status</span>
            <div class="btn-group">
              <button
                :class="[
                  'btn',
                  'btn-outline-secondary',
                  { active: currentFilter === 'all' },
                ]"
                @click="filterJobs('all')"
              >
                All Jobs
              </button>
              <button
                :class="[
                  'btn',
                  'btn-outline-secondary',
                  { active: currentFilter === 'published' },
                ]"
                @click="filterJobs('published')"
              >
                Published
              </button>
              <button
                :class="[
                  'btn',
                  'btn-outline-secondary',
                  { active: currentFilter === 'expired' },
                ]"
                @click="filterJobs('expired')"
              >
                Expired
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="!isLoading && !error" class="job-list-table">
        <table class="table">
          <thead>
            <tr>
              <th class="jobs-header">JOBS</th>
              <th class="status-header">STATUS</th>
              <th class="applications-header">APPLICATIONS</th>
              <th class="actions-header">ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="job in paginatedJobs"
              :key="job.id"
              :class="{ 'highlighted-row': job.id === selectedJobId }"
            >
              <td class="job-info">
                <div class="job-title">{{ job.title }}</div>
                <div class="job-details">
                  <span>{{ job.employment_type || "N/A" }}</span>
                  <span v-if="job.closing_date" class="bullet-separator"
                    >•</span
                  >
                  <span v-if="job.closing_date">{{
                    formatDaysRemaining(job.closing_date)
                  }}</span>
                  <span v-if="job.created_at" class="bullet-separator">•</span>
                  <span v-if="job.created_at">{{
                    formatDate(job.created_at)
                  }}</span>
                </div>
              </td>
              <td class="status-cell">
                <span :class="['status-badge', getStatusClass(job.status)]">
                  <i
                    :class="[
                      'bi',
                      job.is_active
                        ? 'bi-check-circle-fill'
                        : 'bi-x-circle-fill',
                    ]"
                  ></i>
                  {{ job.status.charAt(0).toUpperCase() + job.status.slice(1) }}
                </span>
              </td>
              <td class="applications-cell">
                <span class="applications-count">
                  <i class="bi bi-people-fill"></i>
                  {{ job.applications_count || 0 }} Applications
                </span>
              </td>
              <td class="actions-cell">
                <button
                  class="btn btn-primary btn-sm view-applications"
                  @click="viewApplications(job.id)"
                >
                  View Applications
                </button>
                <div class="dropdown">
                  <button
                    class="btn btn-light btn-sm options-btn"
                    @click="toggleDropdown(job.id)"
                  >
                    <i class="bi bi-three-dots"></i>
                  </button>
                  <div
                    v-if="job.id === activeDropdown"
                    class="dropdown-menu"
                    @click.stop
                  >
                    <a
                      href="#"
                      class="dropdown-item"
                      @click.prevent="viewDetails(job.id)"
                    >
                      <i class="bi bi-eye"></i> View Detail
                    </a>
                    <a
                      href="#"
                      class="dropdown-item"
                      @click.prevent="toggleJobStatus(job.id)"
                    >
                      <i class="bi bi-toggle-on"></i>
                      {{ job.is_active ? "Mark as Expired" : "Reactivate" }}
                    </a>
                  </div>
                </div>
              </td>
            </tr>
            <tr v-if="paginatedJobs.length === 0">
              <td colspan="4" class="no-jobs">
                No jobs found.
                <router-link to="/employer/post-job"
                  >Create a new job</router-link
                >
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="!isLoading && !error && jobs.length > 0" class="pagination">
        <ul class="pagination-list">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <a
              class="page-link"
              href="#"
              aria-label="Previous"
              @click.prevent="prevPage"
            >
              <span aria-hidden="true">&laquo;</span>
            </a>
          </li>
          <li
            v-for="page in totalPages"
            :key="page"
            :class="['page-item', { active: currentPage === page }]"
          >
            <a class="page-link" href="#" @click.prevent="goToPage(page)">
              {{ page < 10 ? "0" + page : page }}
            </a>
          </li>
          <li
            class="page-item"
            :class="{ disabled: currentPage === totalPages }"
          >
            <a
              class="page-link"
              href="#"
              aria-label="Next"
              @click.prevent="nextPage"
            >
              <span aria-hidden="true">&raquo;</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../services/authStore";
import SidebarComponent from "./SidebarComponent.vue";
import axios from "axios";

const router = useRouter();
const authStore = useAuthStore();

const jobs = ref([]);
const selectedJobId = ref(null);
const activeDropdown = ref(null);
const currentPage = ref(1);
const itemsPerPage = 10;
const totalPages = ref(1);
const error = ref(null);
const isLoading = ref(false);
const currentFilter = ref("all");

const paginatedJobs = computed(() => {
  let filteredJobs = jobs.value;
  if (currentFilter.value !== "all") {
    filteredJobs = jobs.value.filter(
      (job) => job.status === currentFilter.value
    );
  }
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredJobs.slice(start, end);
});

const fetchJobs = async () => {
  if (!authStore.token) {
    router.push("/login");
    return;
  }

  isLoading.value = true;
  error.value = null;

  try {
    const response = await axios.get(`/api/jobs?page=${currentPage.value}`, {
      headers: { Authorization: `Bearer ${authStore.token}` },
    });

    jobs.value = response.data.data || [];
    totalPages.value = response.data.last_page || 1;
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to load jobs";
  } finally {
    isLoading.value = false;
  }
};

const filterJobs = (filter) => {
  currentFilter.value = filter;
  currentPage.value = 1;
  fetchJobs();
};

const toggleDropdown = (jobId) => {
  activeDropdown.value = activeDropdown.value === jobId ? null : jobId;
};

const viewDetails = (jobId) => {
  router.push(`/employer/jobs/${jobId}`);
  activeDropdown.value = null;
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

    const job = jobs.value.find((j) => j.id === jobId);
    if (job) {
      job.is_active = !job.is_active;
      job.status = job.is_active ? "published" : "expired";
    }
    activeDropdown.value = null;
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to update job status";
  }
};

const viewApplications = (jobId) => {
  router.push(`/employer/jobs/${jobId}/applications`);
  activeDropdown.value = null;
};

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    fetchJobs();
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    fetchJobs();
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
    fetchJobs();
  }
};

const closeDropdownOnClickOutside = (event) => {
  if (activeDropdown.value !== null) {
    activeDropdown.value = null;
  }
};

const formatDaysRemaining = (closingDate) => {
  const now = new Date();
  const close = new Date(closingDate);
  const diffDays = Math.ceil((close - now) / (1000 * 60 * 60 * 24));
  return diffDays > 0 ? `${diffDays} days remaining` : "Expired";
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
};

const getStatusClass = (status) => {
  return status === "published" || status === "active" ? "active" : "expire";
};

onMounted(async () => {
  if (!authStore.user) {
    try {
      await authStore.fetchUser();
    } catch (err) {
      router.push("/login");
    }
  }
  fetchJobs();
  document.addEventListener("click", closeDropdownOnClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", closeDropdownOnClickOutside);
});
</script>

<style scoped>
.job-list-container {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 20px;
  max-width: 1200px;
  margin: 20px auto;
}

.job-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.job-count {
  color: #6c757d;
  font-weight: normal;
}

.filter-controls {
  display: flex;
  align-items: center;
}

.job-status-filter {
  display: flex;
  align-items: center;
  gap: 10px;
}

.job-status-filter span {
  color: #6c757d;
  font-size: 0.9rem;
}

.job-list-table {
  margin-bottom: 20px;
  overflow-x: auto;
}

table {
  border-collapse: separate;
  border-spacing: 0;
  width: 100%;
}

thead th {
  background-color: #f8f9fa;
  color: #6c757d;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  padding: 12px 15px;
}

.jobs-header {
  width: 35%;
}

.status-header {
  width: 15%;
}

.applications-header {
  width: 25%;
}

.actions-header {
  width: 25%;
}

tbody tr {
  border-bottom: 1px solid #e9ecef;
}

tbody tr.highlighted-row {
  background-color: #f8f9fb;
  border: 1px solid #3b82f6;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.job-info {
  padding: 15px;
}

.job-title {
  font-weight: 600;
  color: #212529;
  margin-bottom: 5px;
}

.job-details {
  color: #6c757d;
  font-size: 0.85rem;
}

.bullet-separator {
  margin: 0 5px;
}

.status-cell {
  vertical-align: middle;
  padding: 0 15px;
}

.status-badge {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.85rem;
  font-weight: 500;
}

.status-badge.active {
  color: #28a745;
}

.status-badge.expire {
  color: #dc3545;
}

.applications-cell {
  vertical-align: middle;
  padding: 0 15px;
}

.applications-count {
  display: flex;
  align-items: center;
  gap: 5px;
  color: #6c757d;
  font-size: 0.9rem;
}

.actions-cell {
  vertical-align: middle;
  padding: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.view-applications {
  background-color: #3b82f6;
  border-color: #3b82f6;
}

.options-btn {
  padding: 0.25rem 0.5rem;
}

.dropdown {
  position: relative;
}

.dropdown-menu {
  position: absolute;
  right: 0;
  top: 100%;
  background-color: white;
  border-radius: 4px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  min-width: 160px;
  padding: 8px 0;
  margin-top: 5px;
}

.dropdown-item {
  display: flex;
  align-items: center;
  padding: 8px 15px;
  color: #212529;
  text-decoration: none;
  font-size: 0.9rem;
}

.dropdown-item:hover {
  background-color: #f8f9fa;
  color: #3b82f6;
}

.dropdown-item i {
  margin-right: 8px;
  color: #6c757d;
  font-size: 1rem;
}

.pagination {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.pagination-list {
  display: flex;
  list-style: none;
  padding: 0;
  margin: 0;
  gap: 5px;
}

.page-item.disabled .page-link {
  color: #6c757d;
  pointer-events: none;
  background-color: #e9ecef;
}

.page-item.active .page-link {
  background-color: #3b82f6;
  border-color: #3b82f6;
  color: white;
}

.page-link {
  color: #6c757d;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  padding: 0;
}

.page-link:hover {
  background-color: #e9ecef;
  color: #3b82f6;
}

.loading-overlay {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.loading-spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3b82f6;
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
  color: #3b82f6;
  text-decoration: none;
}

.no-jobs a:hover {
  text-decoration: underline;
}

@media (max-width: 992px) {
  .job-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }

  .filter-controls {
    width: 100%;
  }

  .actions-cell {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .view-applications {
    width: 100%;
  }
}
</style>
