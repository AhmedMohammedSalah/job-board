
<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <!-- Header Section -->
      <div class="text-center mb-12">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 bg-clip-text text-transparent bg-gradient-to-r from-teal-500 to-cyan-600">
          Your Favorite Opportunities
        </h1>
        <p class="mt-3 text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
          Curate your dream jobs and explore them at your leisure
        </p>
        <div class="mt-6 inline-flex items-center px-6 py-2 bg-teal-100 text-teal-800 font-semibold rounded-full shadow-md">
          <span>{{ favoriteJobs.length }} {{ favoriteJobs.length === 1 ? 'Job Saved' : 'Jobs Saved' }}</span>
        </div>
      </div>

      <!-- Loading State -->
      <div
        v-if="loading"
        class="flex flex-col items-center justify-center py-20 animate-fade-in"
      >
        <font-awesome-icon
          :icon="['fas', 'spinner']"
          spin
          size="4x"
          class="text-teal-500 mb-4"
        />
        <p class="text-gray-700 text-lg font-medium">Fetching your favorites...</p>
      </div>

      <!-- Empty State -->
      <div
        v-else-if="favoriteJobs.length === 0"
        class="bg-white rounded-3xl shadow-lg p-10 text-center max-w-2xl mx-auto border border-teal-100 animate-slide-up"
      >
        <font-awesome-icon
          :icon="['fas', 'heart']"
          size="5x"
          class="text-teal-400 mb-6 transform hover:scale-110 transition-transform duration-300"
        />
        <h3 class="text-3xl font-bold text-gray-800 mb-4">No Favorites Yet</h3>
        <p class="text-gray-500 text-base leading-relaxed">
          Start building your collection by saving jobs that inspire you!
        </p>
        <router-link
          to="/jobs"
          class="mt-8 inline-flex items-center px-6 py-3 bg-gradient-to-r from-teal-500 to-cyan-500 text-white font-semibold rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:ring-opacity-50"
        >
          <font-awesome-icon :icon="['fas', 'briefcase']" class="w-5 h-5 mr-2" />
          Discover Jobs
        </router-link>
      </div>

      <!-- Jobs Grid -->
      <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="favJob in favoriteJobs"
          :key="favJob.id"
          class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100 overflow-hidden group transform hover:-translate-y-1 animate-slide-up"
        >
          <!-- Job Card Header -->
          <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-teal-50 to-cyan-50">
            <div class="flex justify-between items-start">
              <div>
                <h2 class="text-xl font-semibold text-gray-900 group-hover:text-teal-600 transition-colors duration-300">
                  <router-link
                    :to="{ name: 'JobDetails', params: { id: favJob.job.id } }"
                    class="hover:underline"
                  >
                    {{ favJob.job.title }}
                  </router-link>
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                  <font-awesome-icon :icon="['fas', 'building']" class="w-4 h-4 mr-1 text-teal-500" />
                  {{ favJob.job.employer_id ? `Employer ID: ${favJob.job.employer_id}` : 'Unknown Employer' }}
                </p>
              </div>
              <button
                @click="removeFavorite(favJob.job.id)"
                class="p-2 text-gray-400 hover:text-red-500 rounded-full hover:bg-red-50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-400"
                aria-label="Remove from favorites"
              >
                <font-awesome-icon :icon="['fas', 'heart-crack']" class="w-5 h-5" />
              </button>
            </div>
          </div>

          <!-- Job Card Body -->
          <div class="p-6">
            <div class="flex flex-wrap gap-3 mb-4">
              <span class="px-3 py-1 bg-teal-100 text-teal-700 text-xs font-medium rounded-full shadow-sm">
                <font-awesome-icon :icon="['fas', 'location-dot']" class="w-3 h-3 mr-1" />
                {{ favJob.job.location || 'Remote' }}
              </span>
              <span class="px-3 py-1 bg-cyan-100 text-cyan-700 text-xs font-medium rounded-full shadow-sm">
                <font-awesome-icon :icon="['fas', 'briefcase']" class="w-3 h-3 mr-1" />
                {{ formatWorkType(favJob.job.work_type) }}
              </span>
            </div>
            <div v-if="favJob.job.min_salary || favJob.job.max_salary" class="flex items-center mb-4">
              <font-awesome-icon :icon="['fas', 'money-bill-wave']" class="w-4 h-4 mr-2 text-green-500" />
              <span class="text-sm font-medium text-green-600">
                {{ formatSalary(favJob.job.min_salary, favJob.job.max_salary) }}
              </span>
            </div>
            <p class="text-gray-600 text-sm line-clamp-2 mb-4">
              {{ favJob.job.description }}
            </p>
            <div class="text-gray-500 text-xs">
              <font-awesome-icon :icon="['fas', 'clock']" class="w-3 h-3 mr-1" />
              Saved {{ formatRelativeDate(favJob.created_at) }}
            </div>
          </div>

          <!-- Job Card Footer -->
          <div class="p-4 bg-gray-50 flex justify-between items-center">
            <router-link
              :to="{ name: 'JobDetails', params: { id: favJob.job.id } }"
              class="text-teal-600 hover:text-teal-800 font-medium flex items-center transition-colors duration-200"
            >
              View Details
              <font-awesome-icon
                :icon="['fas', 'arrow-right']"
                class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-200"
              />
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { library } from '@fortawesome/fontawesome-svg-core';
import api from "@/services/api";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {
  faSpinner,
  faHeart,
  faBriefcase,
  faBuilding,
  faLocationDot,
  faMoneyBillWave,
  faClock,
  faArrowRight,
  faHeartCrack,
} from '@fortawesome/free-solid-svg-icons';
import { formatDistanceToNow } from 'date-fns';

library.add(
  faSpinner,
  faHeart,
  faBriefcase,
  faBuilding,
  faLocationDot,
  faMoneyBillWave,
  faClock,
  faArrowRight,
  faHeartCrack
);

export default {
  name: 'FavoriteJobs',
  components: {
    FontAwesomeIcon,
  },
  data() {
    return {
      favoriteJobs: [],
      loading: true,
      error: null,
    };
  },
  async mounted() {
    try {
      const response = await api.candidate.getFavoriteJobs();
      this.favoriteJobs = response.data.favorite_jobs || [];
    } catch (error) {
      console.error("Failed to fetch favorite jobs:", error);
      this.error = error.message;
      // Optionally, display a user-friendly error message using a toast or similar
      this.$toast.error("Failed to load your favorite jobs. Please try again.");
    } finally {
      this.loading = false;
    }
  },
  methods: {
    async removeFavorite(jobId) {
      try {
        // Simulate API call to remove favorite
        this.favoriteJobs = this.favoriteJobs.filter((favJob) => favJob.job.id !== jobId);
        this.$toast?.success('Job removed from favorites!');
      } catch (error) {
        console.error('Failed to remove favorite:', error);
        this.$toast?.error('Failed to remove job from favorites.');
      }
    },
    formatWorkType(type) {
      const types = {
        remote: 'Remote',
        full_time: 'Full Time',
        part_time: 'Part Time',
        contract: 'Contract',
        internship: 'Internship',
        hybrid: 'Hybrid',
      };
      return types[type] || type;
    },
    formatSalary(min, max) {
      if (min && max) {
        return `$${parseInt(min).toLocaleString()} - $${parseInt(max).toLocaleString()}/mo`;
      }
      if (min) return `From $${parseInt(min).toLocaleString()}/mo`;
      if (max) return `Up to $${parseInt(max).toLocaleString()}/mo`;
      return 'Salary negotiable';
    },
    formatRelativeDate(dateString) {
      return formatDistanceToNow(new Date(dateString), { addSuffix: true });
    },
  },
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slide-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out forwards;
}

.animate-slide-up {
  animation: slide-up 0.6s ease-out forwards;
}
</style>