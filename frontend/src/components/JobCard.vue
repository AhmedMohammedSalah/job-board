<template>
  <div class="job-card" :class="{ featured: isFeatured }">
    <div class="job-header">
      <div class="company-logo">
        <img
          :src="job.employer?.logo"
          :alt="job.employer?.name"
          v-if="job.employer?.logo"
        />
        <div class="logo-placeholder" v-else>
          {{ job.employer?.name?.charAt(0).toUpperCase() || "C" }}
        </div>
      </div>

      <div class="job-meta">
        <h3 class="job-title">{{ job.title }}</h3>
        <div class="company-info">
          <span class="company-name">{{
            job.employer?.name || "Company"
          }}</span>
          <span class="job-location">
            <i class="fas fa-map-marker-alt"></i> {{ job.location }}
          </span>
        </div>
      </div>

      <button
        class="favorite-btn"
        @click.stop="toggleFavorite"
        :disabled="isFavoriteLoading"
        :aria-label="
          localIsFavorited ? 'Remove from favorites' : 'Add to favorites'
        "
      >
        <i
          :class="['fa-heart', localIsFavorited ? 'fas text-red-500' : 'far']"
        ></i>
        <span class="fav-text">
          {{ localIsFavorited ? "Remove from Favorites" : "Add to Favorites" }}
        </span>
      </button>
    </div>

    <div class="job-details">
      <div class="job-tags">
        <span class="job-type" :class="job.work_type">
          {{ formatWorkType(job.work_type) }}
        </span>
        <span class="job-salary" v-if="job.min_salary || job.max_salary">
          <i class="fas fa-money-bill-wave"></i>
          {{ formatSalary(job.min_salary, job.max_salary) }}
        </span>
        <span class="job-remote" v-if="job.is_remote">
          <i class="fas fa-globe"></i> Remote
        </span>
      </div>

      <p class="job-description">{{ truncateDescription(job.description) }}</p>

      <div class="job-footer">
        <span class="post-time">{{ formatDate(job.created_at) }}</span>
        <div class="job-actions">
          <button
            class="apply-btn"
            @click="handleApply"
            :disabled="isApplied"
            :class="{ applied: isApplied }"
          >
            {{ isApplied ? "Applied" : "View Details" }}
          </button>
        </div>
      </div>
    </div>

    <div class="featured-badge" v-if="isFeatured">Featured</div>
  </div>
</template>

<script>
import api from '@/services/api';

export default {
  name: 'JobCard',
  props: {
    job: {
      type: Object,
      required: true
    },
    isApplied: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      isFavoriteLoading: false,
      localIsFavorited: false
    };
  },
  computed: {
    isFeatured() {
      return this.job.status === 'featured';
    }
  },
  mounted() {
    this.checkIfFavorited();
    
  },
  methods: {
    async checkIfFavorited() {
      try {
        const res = await api.candidate.isFavoriteJob( this.job.id );
        console.log('Favorite status:', res);
        this.localIsFavorited = res.data?.is_favorite || false;
      } catch (err) {
        console.error('Failed to check favorite status:', err);
        this.localIsFavorited = false;
      }
    },
    async toggleFavorite() {
      if (this.isFavoriteLoading) return;
      this.isFavoriteLoading = true;
      try {
        if (this.localIsFavorited) {
          await api.candidate.removeFavoriteJob(this.job.id);
        } else {
          await api.candidate.addFavoriteJob(this.job.id);
        }
        this.localIsFavorited = !this.localIsFavorited;
        this.$emit('favorite-toggle', {
          jobId: this.job.id,
          isFavorited: this.localIsFavorited
        });
      } catch (err) {
        console.error('Favorite toggle failed:', err);
      } finally {
        this.isFavoriteLoading = false;
      }
    },
    formatWorkType(type) {
      const types = {
        full_time: 'Full Time',
        part_time: 'Part Time',
        contract: 'Contract',
        internship: 'Internship',
        remote: 'Remote'
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
      if (!desc) return '';
      return desc.length > length ? desc.substring(0, length) + '...' : desc;
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'short', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('en-US', options);
    },
    handleApply() {
      this.$router.push({ name: 'JobDetails', params: { id: this.job.id } });
    }
  }
};
</script>

<style scoped>
.job-card {
  position: relative;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
  padding: 20px;
  margin-bottom: 24px;
  border-left: 4px solid transparent;
  transition: transform 0.2s;
}
.job-card:hover {
  transform: translateY(-2px);
}
.job-card.featured {
  border-left-color: #3b82f6;
  background-color: #f0f9ff;
}
.job-header {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
  gap: 15px;
  position: relative;
}
.company-logo {
  width: 50px;
  height: 50px;
  border-radius: 8px;
  overflow: hidden;
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
  font-size: 1.2rem;
  font-weight: bold;
  color: #64748b;
}
.job-meta {
  flex-grow: 1;
}
.job-title {
  font-size: 1.2rem;
  margin-bottom: 4px;
  color: #1e293b;
}
.company-info {
  display: flex;
  gap: 10px;
  font-size: 0.9rem;
  color: #475569;
}
.favorite-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  border: none;
  background: none;
  font-size: 0.9rem;
  cursor: pointer;
  color: #64748b;
  padding: 4px 8px;
  border-radius: 6px;
  transition: background 0.2s, color 0.2s;
}
.favorite-btn:hover {
  background-color: #fef2f2;
  color: #ef4444;
}
.favorite-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.fav-text {
  font-weight: 500;
}
.job-details {
  margin-top: 12px;
}
.job-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 10px;
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
.job-description {
  font-size: 0.95rem;
  color: #475569;
  line-height: 1.5;
  margin-bottom: 10px;
}
.job-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 12px;
  border-top: 1px solid #e2e8f0;
}
.post-time {
  font-size: 0.8rem;
  color: #64748b;
}
.apply-btn {
  padding: 8px 16px;
  background-color: #3b82f6;
  color: white;
  border: none;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
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
  background-color: #3b82f6;
  color: white;
  padding: 3px 8px;
  border-radius: 4px;
  font-size: 0.7rem;
  font-weight: 500;
}
@media (max-width: 768px) {
  .job-header {
    flex-direction: column;
    align-items: flex-start;
  }
  .favorite-btn {
    margin-top: 10px;
    align-self: flex-end;
  }
}
</style>
