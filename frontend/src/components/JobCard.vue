<template>
  <div class="job-card" :class="{ 'featured': isFeatured }">
    <div class="job-header">
      <div class="company-logo">
        <img :src="employer.logo" :alt="employer.name" v-if="employer.logo">
        <div class="logo-placeholder" v-else>
          {{ employer.name.charAt(0).toUpperCase() }}
        </div>
      </div>
      <div class="job-meta">
        <h3 class="job-title">{{ job.title }}</h3>
        <div class="company-info">
          <span class="company-name">{{ employer.name }}</span>
          <span class="job-location">
            <i class="fas fa-map-marker-alt"></i> {{ job.location }}
          </span>
        </div>
      </div>
    </div>

    <div class="job-details">
      <div class="job-tags">
        <span class="job-type" :class="job.work_type">
          {{ formatWorkType(job.work_type) }}
        </span>
        <span class="job-salary" v-if="job.min_salary || job.max_salary">
          <i class="fas fa-money-bill-wave"></i> {{ formatSalary(job.min_salary, job.max_salary) }}
        </span>
        <span class="job-remote" v-if="job.remote">
          <i class="fas fa-globe"></i> Remote
        </span>
      </div>

      <p class="job-description">{{ truncateDescription(job.description) }}</p>

      <div class="job-footer">
        <span class="post-time">{{ formatDate(job.created_at) }}</span>
        <div class="job-actions">
          <button class="save-btn" @click="toggleSave" :aria-label="isSaved ? 'Unsave job' : 'Save job'">
            <i :class="['far', 'fa-bookmark', { 'fas': isSaved }]"></i>
          </button>
          <button 
            class="apply-btn" 
            @click="handleApply"
            :disabled="isApplied"
            :class="{ 'applied': isApplied }"
          >
            {{ isApplied ? 'Applied' : 'Apply Now' }}
          </button>
        </div>
      </div>
    </div>

    <div class="featured-badge" v-if="isFeatured">Featured</div>
  </div>
</template>

<script>
export default {
  name: 'JobCard',
  props: {
    job: {
      type: Object,
      required: true,
      validator: (job) => {
        return [
          'id',
          'title',
          'description',
          'work_type',
          'location',
          'created_at'
        ].every(prop => prop in job)
      }
    },
    employer: {
      type: Object,
      required: true,
      default: () => ({ name: 'Company', logo: null })
    }
  },
  data() {
    return {
      isSaved: false,
      isApplied: false
    }
  },
  computed: {
    isFeatured() {
      return this.job.status === 'featured';
    }
  },
  methods: {
    formatWorkType(type) {
      const types = {
        'full_time': 'Full Time',
        'part_time': 'Part Time',
        'contract': 'Contract',
        'internship': 'Internship',
        'remote': 'Remote'
      };
      return types[type] || type;
    },
    formatSalary(min, max) {
      if (min && max) return `$${min.toLocaleString()} - $${max.toLocaleString()}`;
      if (min) return `From $${min.toLocaleString()}`;
      if (max) return `Up to $${max.toLocaleString()}`;
      return 'Salary negotiable';
    },
    truncateDescription(desc, length = 120) {
      return desc.length > length 
        ? desc.substring(0, length) + '...' 
        : desc;
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'short', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('en-US', options);
    },
    toggleSave() {
      this.isSaved = !this.isSaved;
      this.$emit(this.isSaved ? 'save' : 'unsave', this.job.id);
    },
    handleApply() {
      if (!this.isApplied) {
        this.isApplied = true;
        this.$emit('apply', this.job.id);
      }
    }
  }
}
</script>

<style scoped>
.job-card {
  position: relative;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  padding: 20px;
  margin-bottom: 20px;
  transition: all 0.3s ease;
  border-left: 4px solid transparent;
}

.job-card.featured {
  border-left-color: #3b82f6;
  background-color: #f8fafc;
}

.job-header {
  display: flex;
  align-items: flex-start;
  margin-bottom: 15px;
}

.company-logo {
  width: 50px;
  height: 50px;
  border-radius: 8px;
  overflow: hidden;
  margin-right: 15px;
  background-color: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.company-logo img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.logo-placeholder {
  font-size: 20px;
  font-weight: bold;
  color: #64748b;
}

.job-meta {
  flex-grow: 1;
}

.job-title {
  margin: 0 0 5px 0;
  font-size: 1.1rem;
  color: #1e293b;
}

.company-info {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px;
  font-size: 0.9rem;
  color: #64748b;
}

.company-name {
  font-weight: 500;
}

.job-location i {
  margin-right: 5px;
}

.job-details {
  margin-bottom: 15px;
}

.job-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 12px;
}

.job-type {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: 500;
  background-color: #e2e8f0;
  color: #1e293b;
}

.job-type.full_time {
  background-color: #d1fae5;
  color: #065f46;
}

.job-type.part_time {
  background-color: #fef3c7;
  color: #92400e;
}

.job-type.contract {
  background-color: #e0e7ff;
  color: #3730a3;
}

.job-type.remote {
  background-color: #e0f2fe;
  color: #0369a1;
}

.job-salary,
.job-remote {
  font-size: 0.8rem;
  color: #64748b;
}

.job-salary i,
.job-remote i {
  margin-right: 5px;
}

.job-description {
  margin: 10px 0;
  color: #475569;
  font-size: 0.95rem;
  line-height: 1.5;
}

.job-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid #e2e8f0;
}

.post-time {
  font-size: 0.8rem;
  color: #64748b;
}

.job-actions {
  display: flex;
  gap: 10px;
}

.save-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: #64748b;
  font-size: 1.1rem;
}

.save-btn:hover {
  color: #3b82f6;
}

.apply-btn {
  padding: 8px 16px;
  background-color: #3b82f6;
  color: white;
  border: none;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.apply-btn:hover {
  background-color: #2563eb;
}

.apply-btn:disabled,
.apply-btn.applied {
  background-color: #94a3b8;
  cursor: not-allowed;
}

.featured-badge {
  position: absolute;
  top: 15px;
  right: 15px;
  padding: 3px 8px;
  background-color: #3b82f6;
  color: white;
  border-radius: 4px;
  font-size: 0.7rem;
  font-weight: 500;
}

@media (max-width: 768px) {
  .job-header {
    flex-direction: column;
  }
  
  .company-logo {
    margin-right: 0;
    margin-bottom: 10px;
  }
}
</style>