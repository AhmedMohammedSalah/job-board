<template>
  <div class="d-flex">
    <SidebarComponent />
    <div class="job-list-container">
      <div class="job-header">
        <h4>
          My Jobs <span class="job-count">({{ jobs.length }})</span>
        </h4>

        <div class="filter-controls">
          <div class="job-status-filter">
            <span>Job status</span>
            <div class="btn-group">
              <button class="btn btn-outline-secondary active">All Jobs</button>
              <button
                class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
              >
                <span class="visually-hidden">Toggle Dropdown</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="job-list-table">
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
              v-for="job in jobs"
              :key="job.id"
              :class="{ 'highlighted-row': job.id === selectedJobId }"
            >
              <td class="job-info">
                <div class="job-title">{{ job.title }}</div>
                <div class="job-details">
                  <span>{{ job.type }}</span>
                  <span v-if="job.days" class="bullet-separator">•</span>
                  <span v-if="job.days">{{ job.days }} days remaining</span>
                  <span v-if="job.date" class="bullet-separator">•</span>
                  <span v-if="job.date">{{ job.date }}</span>
                </div>
              </td>
              <td class="status-cell">
                <span :class="['status-badge', job.status.toLowerCase()]">
                  <i
                    :class="[
                      'bi',
                      job.status === 'Active'
                        ? 'bi-check-circle-fill'
                        : 'bi-x-circle-fill',
                    ]"
                  ></i>
                  {{ job.status }}
                </span>
              </td>
              <td class="applications-cell">
                <span class="applications-count">
                  <i class="bi bi-people-fill"></i>
                  {{ job.applicationsCount }} Applications
                </span>
              </td>
              <td class="actions-cell">
                <button class="btn btn-primary btn-sm view-applications">
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
                      @click.prevent="promoteJob(job.id)"
                    >
                      <i class="bi bi-arrow-up-circle"></i> Promote Job
                    </a>
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
                      <i class="bi bi-toggle-on"></i> Make it Expire
                    </a>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="pagination">
        <ul class="pagination-list">
          <li class="page-item">
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
            :class="['page-item', currentPage === page ? 'active' : '']"
          >
            <a class="page-link" href="#" @click.prevent="goToPage(page)">
              {{ page < 10 ? "0" + page : page }}
            </a>
          </li>
          <li class="page-item">
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
import SidebarComponent from "./SidebarComponent.vue";
const jobs = ref([
  {
    id: 1,
    title: "UI/UX Designer",
    type: "Full Time",
    days: 27,
    date: null,
    status: "Active",
    applicationsCount: 798,
  },
  {
    id: 2,
    title: "Senior UX Designer",
    type: "Internship",
    days: 8,
    date: null,
    status: "Active",
    applicationsCount: 185,
  },
  {
    id: 3,
    title: "Junior Graphic Designer",
    type: "Full Time",
    days: 24,
    date: null,
    status: "Active",
    applicationsCount: 563,
  },
  {
    id: 4,
    title: "Front End Developer",
    type: "Full Time",
    days: null,
    date: "Dec 7, 2019",
    status: "Expire",
    applicationsCount: 740,
  },
  {
    id: 5,
    title: "Techical Support Specialist",
    type: "Part Time",
    days: 4,
    date: null,
    status: "Active",
    applicationsCount: 356,
  },
  {
    id: 6,
    title: "Interaction Designer",
    type: "Contract Base",
    days: null,
    date: "Feb 2, 2019",
    status: "Expire",
    applicationsCount: 426,
  },
  {
    id: 7,
    title: "Software Engineer",
    type: "Temporary",
    days: 9,
    date: null,
    status: "Active",
    applicationsCount: 922,
  },
  {
    id: 8,
    title: "Product Designer",
    type: "Full Time",
    days: 7,
    date: null,
    status: "Active",
    applicationsCount: 594,
  },
  {
    id: 9,
    title: "Project Manager",
    type: "Full Time",
    days: null,
    date: "Dec 4, 2019",
    status: "Expire",
    applicationsCount: 196,
  },
  {
    id: 10,
    title: "Marketing Manager",
    type: "Full Time",
    days: 4,
    date: null,
    status: "Active",
    applicationsCount: 462,
  },
  {
    id: 10,
    title: "Marketing Manager",
    type: "Full Time",
    days: 4,
    date: null,
    status: "Active",
    applicationsCount: 462,
  },
  {
    id: 10,
    title: "Marketing Manager",
    type: "Full Time",
    days: 4,
    date: null,
    status: "Active",
    applicationsCount: 462,
  },
  {
    id: 10,
    title: "Marketing Manager",
    type: "Full Time",
    days: 4,
    date: null,
    status: "Active",
    applicationsCount: 462,
  },
]);

const selectedJobId = ref(5);
const activeDropdown = ref(null);
const currentPage = ref(1);
const itemsPerPage = 10;

const totalPages = computed(() => Math.ceil(jobs.value.length / itemsPerPage));

const toggleDropdown = (jobId) => {
  if (activeDropdown.value === jobId) {
    activeDropdown.value = null;
  } else {
    activeDropdown.value = jobId;
  }
};

const promoteJob = (jobId) => {
  console.log(`Promoting job ID: ${jobId}`);
  activeDropdown.value = null;
};

const viewDetails = (jobId) => {
  console.log(`Viewing details for job ID: ${jobId}`);
  selectedJobId.value = jobId;
  activeDropdown.value = null;
};

const toggleJobStatus = (jobId) => {
  const job = jobs.value.find((j) => j.id === jobId);
  if (job) {
    job.status = job.status === "Active" ? "Expire" : "Active";
  }
  activeDropdown.value = null;
};

const goToPage = (page) => {
  currentPage.value = page;
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const closeDropdownOnClickOutside = (event) => {
  if (activeDropdown.value !== null) {
    activeDropdown.value = null;
  }
};

onMounted(() => {
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
