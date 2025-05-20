<template>
  <div class="app-layout">
    <Sidebar />
    <div class="main-content">
      <div class="page-header">
        <h1><i class="fas fa-clock"></i> Pending Jobs</h1>
        <div class="header-actions">
          <button class="refresh-btn" @click="fetchPendingJobs">
            <i class="fas fa-sync-alt"></i> Refresh
          </button>
        </div>
      </div>

      <div class="content-card">
        <div v-if="loading" class="loading-spinner">
          <i class="fas fa-spinner fa-spin"></i> Loading...
        </div>

        <div v-else-if="error" class="error-message">
          <i class="fas fa-exclamation-circle"></i> {{ error }}
        </div>

        <div v-else-if="pendingJobs.length === 0" class="no-jobs">
          <i class="fas fa-check-circle"></i> No pending jobs at the moment.
        </div>

        <div v-else class="jobs-list">
          <div v-for="job in pendingJobs" :key="job.id" class="job-card">
            <div class="job-header">
              <h3>{{ job.title }}</h3> <div class="job-meta">
                <span><i class="fas fa-id-card"></i> ID: {{ job.id }}</span> <span><i class="fas fa-briefcase"></i> Category: {{ job.category_id }}</span>
                <span><i class="fas fa-building"></i> Employer: {{ job.employer_id }}</span> </div>
            </div>

            <div class="job-details">
              <div class="job-section">
                <h4>Description:</h4>
                <p>{{ job.description || 'Not specified' }}</p>
              </div>

              <div class="job-section">
                <h4>Responsibilities:</h4>
                <p>{{ job.responsibilities || 'Not specified' }}</p>
              </div>

              <div class="job-section">
                <h4>Requirements:</h4>
                <p>{{ job.requirements || 'Not specified' }}</p>
              </div>

              <div class="job-section">
                <h4>Benefits:</h4>
                <p>{{ job.benefits || 'Not specified' }}</p>
              </div>

              <div class="job-meta-grid">
                <div class="meta-item">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>Location: {{ job.location || 'Remote' }}</span>
                </div>
                <div class="meta-item">
                  <i class="fas fa-briefcase"></i>
                  <span>Type: {{ job.work_type || 'Full-time' }}</span>
                </div>
                <div class="meta-item">
                  <i class="fas fa-money-bill-wave"></i>
                  <span>Salary: ¥{{ job.min_salary || '0' }} - ¥{{ job.max_salary || '0' }}</span>
                </div>
                <div class="meta-item">
                  <i class="fas fa-calendar-times"></i>
                  <span>Deadline: {{ formatDate(job.deadline) }}</span>
                </div>
                <div class="meta-item">
                  <i class="fas fa-calendar-plus"></i>
                  <span>Posted: {{ formatDate(job.created_at) }}</span>
                </div>
                <div class="meta-item">
                  <i class="fas fa-calendar-check"></i>
                  <span>Updated: {{ formatDate(job.updated_at) }}</span>
                </div>
              </div>
            </div>

            <div class="job-actions">
              <button class="approve-btn" @click="approveJob(job.id)"> <i class="fas fa-check"></i> Approve
              </button>
              <button class="reject-btn" @click="rejectJob(job.id)"> <i class="fas fa-times"></i> Reject
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'

export default {
  name: 'PendingJobsView',
  components: {
    Sidebar
  },
  data() {
    return {
      pendingJobs: [],
      loading: false,
      error: null,
      // Define your API base URL here, ideally from .env.local or .env
      // Make sure VUE_APP_API_URL is set in your .env file (e.g., VUE_APP_API_URL=http://127.0.0.1:8000/api)
      apiBaseUrl: process.env.VUE_APP_API_URL || 'http://127.0.0.1:8000/api'
    }
  },
  created() {
    this.fetchPendingJobs()
  },
  methods: {
    async fetchPendingJobs() {
      this.loading = true
      this.error = null
      try {
        const token = localStorage.getItem('token')
        if (!token) {
          throw new Error('Authentication token not found. Please log in.')
        }

        const response = await fetch(`${this.apiBaseUrl}/jobs/pending`, { // Using apiBaseUrl
          headers: {
            'Authorization': `Bearer ${token}`, // Using the actual token from localStorage
            'Content-Type': 'application/json'
          }
        })

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }

        const data = await response.json()
        this.pendingJobs = data
      } catch (err) {
        this.error = err.message || 'Failed to fetch pending jobs'
        console.error('Error fetching pending jobs:', err)
      } finally {
        this.loading = false
      }
    },
    formatDate(dateString) {
      if (!dateString) return 'Not specified'
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },
    async approveJob(jobId) {
      try {
        const token = localStorage.getItem('token')
        if (!token) {
          throw new Error('Authentication token not found. Please log in.')
        }

        const response = await fetch(`${this.apiBaseUrl}/jobs/${jobId}/approve`, { // Using apiBaseUrl
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`, // Using the actual token from localStorage
            'Content-Type': 'application/json'
          }
        })

        if (!response.ok) {
          const errorData = await response.json()
          throw new Error(errorData.message || 'Failed to approve job')
        }

        // Assuming this.$toast is available via a plugin
        if (this.$toast) {
          this.$toast.success('Job approved successfully')
        } else {
          alert('Job approved successfully!'); // Fallback if toast not available
        }
        this.fetchPendingJobs()
      } catch (err) {
        if (this.$toast) {
          this.$toast.error(err.message || 'Failed to approve job')
        } else {
          alert(`Error: ${err.message || 'Failed to approve job'}`); // Fallback
        }
        console.error('Error approving job:', err)
      }
    },
    async rejectJob(jobId) {
      try {
        const token = localStorage.getItem('token')
        if (!token) {
          throw new Error('Authentication token not found. Please log in.')
        }

        const response = await fetch(`${this.apiBaseUrl}/jobs/${jobId}/reject`, { // Using apiBaseUrl
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`, // Using the actual token from localStorage
            'Content-Type': 'application/json'
          }
        })

        if (!response.ok) {
          const errorData = await response.json()
          throw new Error(errorData.message || 'Failed to reject job')
        }

        // Assuming this.$toast is available via a plugin
        if (this.$toast) {
          this.$toast.success('Job rejected successfully')
        } else {
          alert('Job rejected successfully!'); // Fallback
        }
        this.fetchPendingJobs()
      } catch (err) {
        if (this.$toast) {
          this.$toast.error(err.message || 'Failed to reject job')
        } else {
          alert(`Error: ${err.message || 'Failed to reject job'}`); // Fallback
        }
        console.error('Error rejecting job:', err)
      }
    }
  }
}
</script>

<style scoped>
/* Your existing CSS styles remain the same */
.app-layout {
  display: flex;
  min-height: 100vh;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.main-content {
  flex: 1;
  margin-left: 200px;
  padding: 30px;
  background-color: #f8fafc;
  min-height: 100vh;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 15px;
  border-bottom: 1px solid #e2e8f0;
}

.page-header h1 {
  color: #2d3748;
  font-size: 1.8rem;
  margin: 0;
}

.page-header h1 i {
  margin-right: 10px;
  color: #4299e1;
}

.header-actions {
  display: flex;
  gap: 10px;
}

.refresh-btn {
  padding: 8px 16px;
  background-color: #4299e1;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
  transition: background-color 0.2s;
}

.refresh-btn:hover {
  background-color: #3182ce;
}

.refresh-btn i {
  font-size: 0.9rem;
}

.content-card {
  background: white;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.loading-spinner, .error-message, .no-jobs {
  padding: 30px;
  text-align: center;
  font-size: 1.1rem;
}

.loading-spinner i {
  margin-right: 10px;
  color: #4299e1;
  font-size: 1.2rem;
}

.error-message {
  color: #e53e3e;
}

.error-message i {
  margin-right: 10px;
}

.no-jobs {
  color: #38a169;
}

.no-jobs i {
  margin-right: 10px;
}

.jobs-list {
  display: grid;
  gap: 25px;
}

.job-card {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 25px;
  background: white;
  transition: all 0.2s;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.job-card:hover {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.job-header {
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 1px solid #edf2f7;
}

.job-header h3 {
  color: #2d3748;
  font-size: 1.4rem;
  margin: 0 0 10px 0;
}

.job-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  font-size: 0.85rem;
  color: #4a5568;
}

.job-meta i {
  margin-right: 5px;
  color: #718096;
}

.job-details {
  margin-top: 20px;
}

.job-section {
  margin-bottom: 20px;
}

.job-section h4 {
  color: #2d3748;
  font-size: 1.1rem;
  margin: 0 0 8px 0;
}

.job-section p {
  color: #4a5568;
  line-height: 1.6;
  margin: 0;
  white-space: pre-line;
}

.job-meta-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 15px;
  margin-top: 25px;
  padding-top: 15px;
  border-top: 1px solid #edf2f7;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
  color: #4a5568;
}

.meta-item i {
  color: #718096;
  width: 20px;
  text-align: center;
}

.job-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 25px;
  padding-top: 15px;
  border-top: 1px solid #edf2f7;
}

.approve-btn, .reject-btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
  transition: all 0.2s;
}

.approve-btn {
  background-color: #38a169;
  color: white;
}

.approve-btn:hover {
  background-color: #2f855a;
}

.reject-btn {
  background-color: #e53e3e;
  color: white;
}

.reject-btn:hover {
  background-color: #c53030;
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
    padding: 20px;
  }

  .job-meta-grid {
    grid-template-columns: 1fr;
  }

  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
}
</style>