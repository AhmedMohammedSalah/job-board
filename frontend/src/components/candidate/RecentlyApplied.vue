<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
// import JobCard from "@/components/candidate/JobCard.vue";
import api from "@/services/api";

const router = useRouter();

const jobs = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchRecentApplications = async () => {
  try {
    const response = await api.candidate.getJobsRecentlyApplied();

    if (!response.data || !Array.isArray(response.data)) {
      throw new Error("Invalid API response format");
    }

    jobs.value = response.data.map((job) => ({
      ...job,
      category: job.category || { name: "General" },
      location: job.location || "Remote",
      work_type: job.work_type || "remote",
    }));
  } catch (err) {
    error.value = err.response?.data?.message || err.message;
    console.error("API Error:", err);
  } finally {
    loading.value = false;
  }
};

const handleViewJob = (jobId) => {
  router.push({ name: "JobDetails", params: { id: jobId } });
};

onMounted(fetchRecentApplications);
</script>

<template>
  <section class="recently-applied">
    <div class="section-header">
      <h2>Recently Applied Jobs</h2>
      <router-link to="/candidate/recentlyApplied" class="view-all">
        View All Applications
        <font-awesome-icon :icon="['fas', 'arrow-right']" />
      </router-link>
    </div>

    <div class="jobs-container">
      <template v-if="loading">
        <JobCard v-for="n in 3" :key="n" :loading="true" />
      </template>

      <template v-else>
        <JobCard
          v-for="job in jobs"
          :key="job.id"
          :job="job"
          @view-job="handleViewJob"
        />

        <div v-if="!jobs.length" class="empty-state">
          <font-awesome-icon :icon="['fas', 'briefcase']" />
          <p>No recent applications found</p>
        </div>
      </template>
    </div>
  </section>
</template>
