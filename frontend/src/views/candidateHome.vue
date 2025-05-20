<template>
  <div class="candidate-home">
    <!-- <Navbar /> -->

    <main class="main-content">
      <button @click="toggleSidebar" class="filter-toggle-btn">
        <i class="fas fa-filter"></i> Filters
      </button>

      <section class="jobs-section">
        <h2 class="section-title">Available Jobs</h2>

        <div v-if="loading" class="loading-state">
          <i class="fas fa-spinner fa-spin"></i> Loading jobs...
        </div>

        <div v-else-if="error" class="error-state">
          <i class="fas fa-exclamation-circle"></i> {{ error }}
          <button @click="fetchJobs">Retry</button>
        </div>

        <div v-else>
          <div class="jobs-grid">
            <JobCard
              v-for="job in filteredJobs"
              :key="job.id"
              :job="job"
              :employer="{ name: 'ay7aga' }"
              @apply="handleJobApply"
            />
          </div>

          <div v-if="filteredJobs.length === 0" class="no-jobs">
            <i class="fas fa-briefcase"></i>
            <p>No jobs match your filters</p>
            <button @click="clearFilters">Clear filters</button>
          </div>
        </div>
      </section>
    </main>

    <FilterSidebar
      v-if="showSidebar"
      :filters="activeFilters"
      @update:filters="updateFilters"
      @close="showSidebar = false"
    />

    <div
      v-if="showSidebar"
      class="sidebar-overlay"
      @click="showSidebar = false"
    ></div>

    <!-- <AppFooter /> -->
  </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import axios from "../axios";
import Navbar from "../components/homePage/Navbar.vue";
import AppFooter from "../components/homePage/AppFooter.vue";
import FilterSidebar from "../components/FilterSidebar.vue";
import JobCard from "../components/JobCard.vue";

export default {
  name: "CandidateHomePage",
  components: { Navbar, AppFooter, FilterSidebar, JobCard },
  setup() {
    const showSidebar = ref(false);
    const loading = ref(false);
    const error = ref(null);
    const allJobs = ref([]);
    const activeFilters = ref({
      search: "",
      work_type: "",
      min_salary: null,
      max_salary: null,
      remote: false,
    });

    // Debounce function to prevent rapid API calls during filter changes
    const debounce = (fn, delay) => {
      let timeoutId;
      return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn.apply(this, args), delay);
      };
    };

    const fetchJobs = async () => {
      try {
        loading.value = true;
        error.value = null;
        const response = await axios.get("/api/jobs/filter");
        allJobs.value = response.data.map(job => ({
          ...job,
          applied: job.applied || false // Ensure applied status exists
        }));
      } catch (err) {
        console.error("Error fetching jobs:", err);
        error.value = err.response?.data?.message || "Failed to load jobs";
        // Consider returning or throwing the error to let the caller handle it
      } finally {
        loading.value = false;
      }
    };

    // Debounced version of fetchFilteredJobs
    const debouncedFetchFilteredJobs = debounce(async () => {
      try {
        loading.value = true;
        // Clean the params object to remove null/empty values
        const params = Object.entries(activeFilters.value).reduce((acc, [key, value]) => {
          if (value !== null && value !== "" && value !== false) {
            acc[key] = value;
          }
          return acc;
        }, {});

        const response = await axios.get("/api/jobs/filter", { params });
        allJobs.value = response.data.map(job => ({
          ...job,
          applied: job.applied || false
        }));
      } catch (err) {
        console.error("Error filtering jobs:", err);
        error.value = err.response?.data?.message || "Failed to filter jobs";
      } finally {
        loading.value = false;
      }
    }, 300); // 300ms debounce delay

    const fetchFilteredJobs = debouncedFetchFilteredJobs;

    // Actual filtered jobs based on client-side filtering if needed
    const filteredJobs = computed(() => {
      return allJobs.value.filter(job => {
        // If you want to add client-side filtering logic here
        // For now, it just returns all jobs as the server does the filtering
        return true;
      });
    });

    const toggleSidebar = () => {
      showSidebar.value = !showSidebar.value;
    };

    const updateFilters = (newFilters) => {
      activeFilters.value = newFilters;
      fetchFilteredJobs();
    };

    const handleJobApply = async (jobId) => {
      try {
        await axios.post("/api/applications", { job_id: jobId });
        // Update the applied status immutably
        allJobs.value = allJobs.value.map(job => 
          job.id === jobId ? { ...job, applied: true } : job
        );
      } catch (err) {
        console.error("Error applying to job:", err);
        const errorMessage = err.response?.data?.message || "Failed to apply for job";
        error.value = errorMessage;
        // Consider using a more user-friendly notification system
        alert(errorMessage);
      }
    };

    const clearFilters = () => {
      activeFilters.value = {
        search: "",
        work_type: "",
        min_salary: null,
        max_salary: null,
        remote: false,
      };
      showSidebar.value = false;
      fetchJobs();
    };

    onMounted(() => {
      fetchJobs();
    });

    return {
      showSidebar,
      loading,
      error,
      activeFilters,
      filteredJobs,
      toggleSidebar,
      updateFilters,
      handleJobApply,
      clearFilters,
    };
  },
};
</script>
<style scoped>
.candidate-home {
  position: relative;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
  padding: 2rem;
  position: relative;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

.filter-toggle-btn {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  padding: 0.75rem 1.5rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 9999px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  z-index: 90;
  transition: all 0.2s ease;
}

.filter-toggle-btn:hover {
  background: #2563eb;
  transform: translateY(-2px);
}

.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 95;
}

.jobs-section {
  margin-top: 2rem;
}

.section-title {
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
  color: #1e293b;
  font-weight: 600;
}

.jobs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.no-jobs {
  text-align: center;
  padding: 3rem;
  background: #f8fafc;
  border-radius: 8px;
  margin-top: 2rem;
}

.no-jobs i {
  font-size: 2rem;
  color: #94a3b8;
  margin-bottom: 1rem;
}

.no-jobs p {
  color: #64748b;
  margin-bottom: 1rem;
  font-size: 1.1rem;
}

.no-jobs button {
  padding: 0.5rem 1.5rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  transition: background 0.2s;
}

.no-jobs button:hover {
  background: #2563eb;
}

.loading-state,
.error-state {
  text-align: center;
  padding: 2rem;
  font-size: 1.1rem;
}

.loading-state i {
  margin-right: 0.5rem;
}

.error-state {
  color: #ef4444;
}

.error-state button {
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

@media (max-width: 768px) {
  .filter-sidebar {
    width: 100%;
    max-width: 320px;
  }

  .filter-toggle-btn {
    bottom: 1rem;
    right: 1rem;
  }

  .jobs-grid {
    grid-template-columns: 1fr;
  }
}
</style>
