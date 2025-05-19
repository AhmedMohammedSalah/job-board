<template>
  <div class="candidate-home">
    <Navbar />
    <section1 />
    <BecomeaCandidate />

    <!-- Main Content Area -->
    <main class="main-content">
      <!-- Filter Button -->
      <button @click="toggleSidebar" class="filter-toggle-btn">
        <i class="fas fa-filter"></i> Filters
      </button>

      <Popular :jobs="filteredJobs" />
    </main>

    <!-- Filter Sidebar -->
    <transition name="slide">
      <FilterSidebar 
        v-if="showSidebar"
        :filters="activeFilters"
        @update:filters="updateFilters"
        @close="showSidebar = false"
        class="filter-sidebar"
      />
    </transition>

    <!-- Overlay -->
    <div 
      v-if="showSidebar"
      class="sidebar-overlay" 
      @click="showSidebar = false"
    ></div>

    <AppFooter />
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import Popular from '../components/homePage/Popular.vue';
import Navbar from '../components/homePage/Navbar.vue';
import AppHeader from '../components/homePage/AppHeader.vue';
import AppFooter from '../components/homePage/AppFooter.vue';
import section1 from '../components/homePage/section1.vue';
import BecomeaCandidate from '../components/homePage/BecomeaCandidate.vue';
import FilterSidebar from '../components/FilterSidebar.vue';

export default {
  name: 'CandidateHomePage',
  components: { 
    Navbar,
    AppHeader,
    section1,
    Popular,
    BecomeaCandidate,
    AppFooter,
    FilterSidebar
  },
  setup() {
    const showSidebar = ref(false);
    const activeFilters = ref({
      search: '',
      category: '',
      jobType: '',
      minSalary: null,
      maxSalary: null,
      remoteOnly: false
    });

    // Sample jobs data - replace with your actual data
    const allJobs = ref([
      {
        id: 1,
        title: 'UI/UX Designer',
        category: 'Design',
        location: 'Prague, Czech Republic',
        salary: 90000,
        type: 'Full Time',
        remote: true
      },
      // Add more job objects
    ]);

    const filteredJobs = computed(() => {
      return allJobs.value.filter(job => {
        // Search filter
        const matchesSearch = !activeFilters.value.search || 
          job.title.toLowerCase().includes(activeFilters.value.search.toLowerCase()) ||
          job.location.toLowerCase().includes(activeFilters.value.search.toLowerCase());

        // Category filter
        const matchesCategory = !activeFilters.value.category || 
          job.category === activeFilters.value.category;

        // Job type filter
        const matchesJobType = !activeFilters.value.jobType || 
          job.type === activeFilters.value.jobType;

        // Salary filter
        const matchesSalary = (!activeFilters.value.minSalary || 
          job.salary >= activeFilters.value.minSalary) && 
          (!activeFilters.value.maxSalary || job.salary <= activeFilters.value.maxSalary);

        // Remote filter
        const matchesRemote = !activeFilters.value.remoteOnly || job.remote;

        return matchesSearch && matchesCategory && matchesJobType && 
               matchesSalary && matchesRemote;
      });
    });

    const toggleSidebar = () => {
      showSidebar.value = !showSidebar.value;
    };

    const updateFilters = (newFilters) => {
      activeFilters.value = newFilters;
    };

    return {
      showSidebar,
      activeFilters,
      filteredJobs,
      toggleSidebar,
      updateFilters
    };
  }
}
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

.filter-toggle-btn i {
  font-size: 1rem;
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

/* Animation for sidebar */
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
  transform: translateX(100%);
}

.filter-sidebar {
  position: fixed;
  top: 0;
  right: 0;
  width: 320px;
  height: 100vh;
  background: white;
  z-index: 100;
  overflow-y: auto;
  box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .filter-sidebar {
    width: 100%;
    max-width: 320px;
  }
  
  .filter-toggle-btn {
    bottom: 1rem;
    right: 1rem;
  }
}
</style>