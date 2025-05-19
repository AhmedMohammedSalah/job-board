<template>
  <div class="job-card">
    <!-- Company Header -->
    <div class="job-header">
      <div class="company-logo">
        <img :src="logoUrl" :alt="job.company + ' logo'">
      </div>
      <div class="job-info">
        <h3>{{ job.title }}</h3>
        <p class="company">{{ job.company }}</p>
        <div class="job-meta">
          <span class="job-type">{{ job.jobType }}</span>
          <span class="work-model">{{ job.workModel }}</span>
        </div>
      </div>
    </div>

    <!-- Job Details -->
    <div class="job-details">
      <div class="detail-item">
        <i class="icon fas fa-map-marker-alt"></i>
        <span>{{ job.location }}</span>
      </div>
      <div class="detail-item">
        <i class="icon fas fa-money-bill-wave"></i>
        <span>{{ formattedSalary }}</span>
      </div>
      <div class="detail-item">
        <i class="icon fas fa-clock"></i>
        <span>{{ formattedDate }}</span>
      </div>
    </div>

    <!-- Footer with Status and Actions -->
    <div class="job-footer">
      <span class="status-badge" :class="job.status.toLowerCase()">
        {{ formattedStatus }}
      </span>
      <div class="job-actions">
        <button class="btn-view" @click="$emit('view', job.id)">
          <i class="fas fa-eye"></i> View
        </button>
        <button 
          class="btn-save" 
          @click="$emit('save', job.id)"
          :aria-label="isSaved ? 'Unsave job' : 'Save job'"
        >
          <i :class="['fas', isSaved ? 'fa-heart' : 'fa-heart-o']"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  job: {
    type: Object,
    required: true,
    validator: (job) => {
      return [
        'id',
        'title',
        'company',
        'jobType',
        'workModel',
        'location',
        'salaryRange',
        'status',
        'createdAt'
      ].every(key => key in job);
    }
  },
  isSaved: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['view', 'save']);

// Static logo mapping - replace with your actual images
const logoMap = {
  'TechCorp': new URL('@/assets/logos/techcorp.png', import.meta.url).href,
  'DesignHub': new URL('@/assets/logos/designhub.png', import.meta.url).href,
  'FinanceCo': new URL('@/assets/logos/financeco.png', import.meta.url).href,
};

const logoUrl = computed(() => logoMap[props.job.company] || new URL('@/assets/logos/default.png', import.meta.url).href);

const formattedSalary = computed(() => {
  if (!props.job.salaryRange) return 'Negotiable';
  return props.job.salaryRange.startsWith('EGP') 
    ? props.job.salaryRange.replace('EGP', '£') 
    : props.job.salaryRange;
});

const formattedDate = computed(() => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(props.job.createdAt).toLocaleDateString('en-US', options);
});

const formattedStatus = computed(() => {
  const statusMap = {
    active: 'Active',
    pending: 'Pending',
    closed: 'Closed',
    featured: 'Featured'
  };
  return statusMap[props.job.status.toLowerCase()] || props.job.status;
});
</script>

<style scoped>
.job-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.job-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.job-header {
  display: flex;
  gap: 1rem;
  padding: 1.5rem;
  border-bottom: 1px solid #f0f0f0;
}

.company-logo {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}

.company-logo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.job-info h3 {
  margin: 0 0 0.25rem;
  font-size: 1.1rem;
  color: #1a1a1a;
}

.company {
  margin: 0 0 0.75rem;
  color: #6b6b6b;
  font-size: 0.9rem;
}

.job-meta {
  display: flex;
  gap: 0.5rem;
}

.job-meta span {
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  background: #f5f5f5;
  color: #555;
}

.job-details {
  padding: 1.5rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
  color: #555;
}

.detail-item .icon {
  color: #1967D2;
  width: 16px;
  text-align: center;
}

.job-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid #f0f0f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.status-badge {
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
}

.status-badge.active {
  background: rgba(25, 103, 210, 0.1);
  color: #1967D2;
}

.status-badge.pending {
  background: rgba(255, 152, 0, 0.1);
  color: #FF9800;
}

.status-badge.closed {
  background: rgba(234, 67, 53, 0.1);
  color: #EA4335;
}

.status-badge.featured {
  background: rgba(52, 168, 83, 0.1);
  color: #34A853;
}

.job-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-view {
  padding: 0.5rem 1rem;
  background: #1967D2;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.85rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background 0.2s ease;
}

.btn-view:hover {
  background: #0d5bb7;
}

.btn-save {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  color: #EA4335;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-save:hover {
  background: #f8f8f8;
  border-color: #d0d0d0;
}
</style>