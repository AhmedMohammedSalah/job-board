<!-- components/candidate/JobCard.vue -->
<template>
  <div class="job-card" v-if="!loading && !error">
    <div class="application-status" :class="applicationStatusClass">
      <font-awesome-icon :icon="statusIcon" />
      <span>My application: {{ applicationStatusText }}</span>
    </div>
    
    <div class="job-header">
      <div class="company-info">
        <div class="logo-placeholder">
          <font-awesome-icon :icon="['fas', 'building']" class="company-icon" />
        </div>
        <div class="job-meta">
          <h3 class="job-title">{{ job.title }}</h3>
          <div class="company-location">
            <span class="company-name">{{ job.category.name }}</span>
            <span class="separator">•</span>
            <font-awesome-icon :icon="['fas', 'map-marker-alt']" />
            <span class="location">{{ job.location }}</span>
          </div>
        </div>
      </div>
      <div class="work-type" :class="workTypeClass">
        {{ workTypeLabel }}
      </div>
    </div>

    <div class="job-details">
      <div class="detail-item">
        <font-awesome-icon :icon="['fas', 'money-bill-wave']" />
        <span class="salary">{{ formattedSalary }}</span>
      </div>

      <div class="detail-item">
        <font-awesome-icon :icon="['fas', 'clock']" />
        <span class="deadline">{{ timeLeft }}</span>
      </div>
    </div>

    <div class="job-footer">
      <div class="tags">
        <span
          class="tag"
          v-for="(skill, index) in job.category.name.split(' ')"
          :key="index"
        >
          {{ skill }}
        </span>
      </div>
      <button class="view-button" @click="$emit('view-job', job.id)">
        View Details
      </button>
    </div>
  </div>

  <!-- Loading State -->
  <div class="job-card loading" v-if="loading">
    <div class="skeleton-header"></div>
    <div class="skeleton-body"></div>
    <div class="skeleton-footer"></div>
  </div>

  <!-- Error State -->
  <div class="job-card error" v-if="error">
    <font-awesome-icon :icon="['fas', 'exclamation-triangle']" />
    <p>Failed to load job details</p>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { formatDistanceToNow } from 'date-fns';
import api from "../../services/api.js";

const props = defineProps({
  job: {
    type: Object,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
});
const emit = defineEmits(["view-job"]);

const loading = ref(true);
const error = ref(false);

const workTypeMap = {
  remote: { label: "Remote", icon: ["fas", "house"], class: "remote" },
  onsite: { label: "On-site", icon: ["fas", "briefcase"], class: "onsite" },
  hybrid: { label: "Hybrid", icon: ["fas", "balance-scale"], class: "hybrid" },
};

const statusMap = {
  pending: { 
    text: "Pending Review", 
    class: "status-pending", 
    icon: ["fas", "hourglass-half"] 
  },
  reviewed: { 
    text: "Under Review", 
    class: "status-reviewed", 
    icon: ["fas", "eye"] 
  },
  accepted: { 
    text: "Accepted", 
    class: "status-accepted", 
    icon: ["fas", "check-circle"] 
  },
  rejected: { 
    text: "Not Selected", 
    class: "status-rejected", 
    icon: ["fas", "times-circle"] 
  },
  hired: { 
    text: "Hired!", 
    class: "status-hired", 
    icon: ["fas", "trophy"] 
  }
};

onMounted(() => {
  setTimeout(() => {
    loading.value = false;
  }, 1000);
  console.log("JobCard mounted with job:", props.job);
});

// Computed properties
const applicationStatus = computed(() => {
  return props.job.applications?.[0]?.status || "pending";
});

const applicationStatusText = computed(() => {
  return statusMap[applicationStatus.value]?.text || "Pending Review";
});

const applicationStatusClass = computed(() => {
  return statusMap[applicationStatus.value]?.class || "status-pending";
});

const statusIcon = computed(() => {
  return statusMap[applicationStatus.value]?.icon || ["fas", "hourglass-half"];
});

const workTypeLabel = computed(
  () => workTypeMap[props.job.work_type]?.label || ""
);
const workTypeClass = computed(
  () => workTypeMap[props.job.work_type]?.class || ""
);
const formattedSalary = computed(() => {
  if (!props.job.min_salary || !props.job.max_salary)
    return "Salary not specified";

  const min = parseFloat(props.job.min_salary).toLocaleString();
  const max = parseFloat(props.job.max_salary).toLocaleString();
  return `EGP ${min} - ${max}/mo`;
});

const timeLeft = computed(() => {
  if (!props.job.deadline) return "No deadline specified";
  return formatDistanceToNow(new Date(props.job.deadline), { addSuffix: true });
});
</script>

<style scoped>
.job-card {
  background: #ffffff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease;
  margin-bottom: 16px;
  position: relative;
}

.job-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.application-status {
  position: absolute;
  top: -10px;
  right: 20px;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.status-pending {
  background: #fff3e0;
  color: #ef6c00;
}

.status-reviewed {
  background: #e3f2fd;
  color: #1976d2;
}

.status-accepted {
  background: #e8f5e9;
  color: #2e7d32;
}

.status-rejected {
  background: #ffebee;
  color: #c62828;
}

.status-hired {
  background: #f3e5f5;
  color: #7b1fa2;
  font-weight: bold;
}

.job-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 16px;
  margin-top: 10px;
}

.company-info {
  display: flex;
  gap: 12px;
}

.logo-placeholder {
  width: 48px;
  height: 48px;
  background: #f5f5f5;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.company-icon {
  font-size: 24px;
  color: #666;
}

.job-meta {
  display: flex;
  flex-direction: column;
}

.job-title {
  margin: 0;
  font-size: 18px;
  color: #1a1a1a;
}

.company-location {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #666;
  font-size: 14px;
  margin-top: 4px;
}

.work-type {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
}

.work-type.remote {
  background: #e3f2fd;
  color: #1976d2;
}

.work-type.onsite {
  background: #f0f4c3;
  color: #827717;
}

.work-type.hybrid {
  background: #f8bbd0;
  color: #880e4f;
}

.job-details {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 16px;
  margin: 16px 0;
  padding: 12px 0;
  border-top: 1px solid #eee;
  border-bottom: 1px solid #eee;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #444;
}

.salary {
  font-weight: 500;
  color: #2e7d32;
}

.deadline {
  color: #d32f2f;
}

.job-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 16px;
}

.tags {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.tag {
  background: #f5f5f5;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  color: #666;
}

.view-button {
  background: #1976d2;
  color: white;
  border: none;
  padding: 8px 24px;
  border-radius: 20px;
  cursor: pointer;
  transition: background 0.2s ease;
}

.view-button:hover {
  background: #1565c0;
}

/* Loading States */
.loading .skeleton-header,
.loading .skeleton-body,
.loading .skeleton-footer {
  background: #f5f5f5;
  border-radius: 4px;
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
  100% {
    opacity: 1;
  }
}

.error {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 20px;
  color: #d32f2f;
  text-align: center;
}
</style>