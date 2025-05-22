<template>
  <div class="app-layout">
    <div class="sidebar">
      <div class="logo">
        <i class="fas fa-boxes"></i>
        <span>Inventory System</span>
      </div>
      <nav>
        <router-link to="/pending-jobs" class="nav-item">
          <i class="fas fa-clock"></i>
          <span>Pending Jobs</span>
        </router-link>
        <router-link to="/adminComments" class="nav-item">
          <i class="fas fa-clipboard-list"></i>
          <span>Comment Managments</span>
        </router-link>
        <a href="#" class="nav-item logout" @click.prevent="logout">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </a>
      </nav>
    </div>

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
              <h3>{{ job.title }}</h3>
              <div class="job-meta">
                <span><i class="fas fa-id-card"></i> ID: {{ job.id }}</span>
                <span
                  ><i class="fas fa-briefcase"></i> Category:
                  {{ job.category_id }}</span
                >
                <span
                  ><i class="fas fa-building"></i> Employee:
                  {{ job.employee_id }}</span
                >
              </div>
            </div>

            <div class="job-details">
              <div class="job-section">
                <h4>Description:</h4>
                <p>{{ job.description || "Not specified" }}</p>
              </div>

              <div class="job-section">
                <h4>Responsibilities:</h4>
                <p>{{ job.responsibilities || "Not specified" }}</p>
              </div>

              <div class="job-section">
                <h4>Requirements:</h4>
                <p>{{ job.requirements || "Not specified" }}</p>
              </div>

              <div class="job-section">
                <h4>Benefits:</h4>
                <p>{{ job.benefits || "Not specified" }}</p>
              </div>

              <div class="job-meta-grid">
                <div class="meta-item">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>Location: {{ job.location || "Remote" }}</span>
                </div>
                <div class="meta-item">
                  <i class="fas fa-briefcase"></i>
                  <span>Type: {{ job.work_type || "Full-time" }}</span>
                </div>
                <div class="meta-item">
                  <i class="fas fa-money-bill-wave"></i>
                  <span
                    >Salary: ¥{{ job.min_salary || "0" }} - ¥{{
                      job.max_salary || "0"
                    }}</span
                  >
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
              <button class="approve-btn" @click="approveJob(job.id)">
                <i class="fas fa-check"></i> Approve
              </button>
              <button class="reject-btn" @click="rejectJob(job.id)">
                <i class="fas fa-times"></i> Reject
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  // axios 
  import axios from "axios";
export default {
  name: "PendingJobsView",
  data() {
    return {
      pendingJobs: [],
      loading: false,
      error: null,

    };
  },
  created() {
    this.fetchPendingJobs();
  },
  methods: {
    async fetchPendingJobs() {
      this.loading = true;
      this.error = null;
      try {
        const token = localStorage.getItem("auth_token");
        const response = await fetch(`http://localhost:8000/api/jobs/pending`, {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        this.pendingJobs = data.data;
      } catch (err) {
        this.error = err.message || "Failed to fetch pending jobs";
        console.error("Error fetching pending jobs:", err);
      } finally {
        this.loading = false;
      }
    },
    formatDate(dateString) {
      if (!dateString) return "Not specified";
      const date = new Date(dateString);
      return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
      });
    },
async approveJob(jobId) {
  try {
    const token = localStorage.getItem("auth_token");
    const response = await fetch(
      `http://127.0.0.1:8000/api/jobs/${jobId}/approve`,
      {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      }
    );

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || "Failed to approve job");
    }

    this.$toast.success("Job approved successfully");
    // Remove router push and refresh data directly
    this.fetchPendingJobs();
  } catch (err) {
    this.$toast.error("Failed to approve job");
    console.error("Error approving job:", err);
  }
},

async rejectJob(jobId) {
  try {
    const token = localStorage.getItem("auth_token");
    const response = await fetch(
      `http://127.0.0.1:8000/api/jobs/${jobId}/reject`,
      {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      }
    );

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || "Failed to reject job");
    }

    this.$toast.success("Job rejected successfully");
    this.fetchPendingJobs();
  } catch (err) {
    this.$toast.error("Failed to reject job");
    console.error("Error rejecting job:", err);
  }
},
   async logout () {
    const token = localStorage.getItem("auth_token");
    if (!token) {
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");
      this.$router.push("/login");
      return;
    }

    try {
      await axios.get("http://localhost:8000/sanctum/csrf-cookie");
      await axios.post(
        "http://localhost:8000/api/logout",
        {},
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );
    } catch (error) {
      console.error("Logout error:", error);
    } finally {
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");
      this.$router.push("/login");
    }
  },
  },
};
</script>

<style scoped>
/* ====================
   LAYOUT STRUCTURE
   ==================== */
.app-layout {
  display: flex;
  min-height: 100vh;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

/* ====================
   SIDEBAR (ENHANCED)
   ==================== */
.sidebar {
  width: 280px;
  height: 100vh;
  background: linear-gradient(135deg, #1976d2, #0d47a1);
  position: fixed;
  left: 0;
  top: 0;
  padding: 25px 0;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
  z-index: 100;
  transition: width 0.3s ease;
}

.logo {
  display: flex;
  align-items: center;
  padding: 0 25px 25px;
  font-size: 1.5rem;
  font-weight: 700;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  margin-bottom: 25px;
  color: white;
}

.logo i {
  margin-right: 15px;
  font-size: 1.8rem;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 16px 25px;
  color: #ecf0f1;
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 1.1rem;
  font-weight: 600;
  border-left: 4px solid transparent;
  margin: 8px 15px;
  border-radius: 6px;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.15);
  border-left: 4px solid #fff;
}

.nav-item i {
  width: 24px;
  margin-right: 15px;
  font-size: 1.3rem;
  text-align: center;
}

.logout {
  margin-top: 35px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding-top: 18px;
  color: #ff6b6b;
}

.logout:hover {
  background: rgba(255, 107, 107, 0.15);
  border-left: 4px solid #ff6b6b;
}

/* ====================
   MAIN CONTENT AREA
   ==================== */
.main-content {
  flex: 1;
  margin-left: 280px;
  padding: 40px;
  background-color: #f8fafc;
  min-height: 100vh;
  transition: margin-left 0.3s ease;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40px;
  padding-bottom: 15px;
  border-bottom: 1px solid #e2e8f0;
}

.page-header h1 {
  color: #2d3748;
  font-size: 1.8rem;
  margin: 0;
  font-weight: 600;
}

.page-header h1 i {
  margin-right: 12px;
  color: #4299e1;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.refresh-btn {
  padding: 10px 18px;
  background-color: #4299e1;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.95rem;
  font-weight: 500;
  transition: background-color 0.2s;
}

.refresh-btn:hover {
  background-color: #3182ce;
}

.refresh-btn i {
  font-size: 0.95rem;
}

/* ====================
   CONTENT CARD
   ==================== */
.content-card {
  background: white;
  padding: 35px;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  max-width: 1200px;
  margin: 0 auto;
}

.loading-spinner,
.error-message,
.no-jobs {
  padding: 30px;
  text-align: center;
  font-size: 1.1rem;
}

.loading-spinner i {
  margin-right: 12px;
  color: #4299e1;
  font-size: 1.3rem;
}

.error-message {
  color: #e53e3e;
  font-weight: 500;
}

.error-message i {
  margin-right: 12px;
  font-size: 1.2rem;
}

.no-jobs {
  color: #38a169;
  font-weight: 500;
}

.no-jobs i {
  margin-right: 12px;
  font-size: 1.2rem;
}

/* ====================
   JOB CARDS
   ==================== */
.jobs-list {
  display: grid;
  gap: 30px;
}

.job-card {
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 30px;
  background: white;
  transition: all 0.2s;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.job-card:hover {
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-3px);
}

.job-header {
  margin-bottom: 25px;
  padding-bottom: 15px;
  border-bottom: 1px solid #edf2f7;
}

.job-header h3 {
  color: #2d3748;
  font-size: 1.5rem;
  font-weight: 600;
  margin: 0 0 12px 0;
}

.job-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  font-size: 0.9rem;
  color: #4a5568;
}

.job-meta i {
  margin-right: 8px;
  color: #718096;
}

.job-details {
  margin-top: 25px;
}

.job-section {
  margin-bottom: 25px;
}

.job-section h4 {
  color: #2d3748;
  font-size: 1.2rem;
  font-weight: 600;
  margin: 0 0 10px 0;
}

.job-section p {
  color: #4a5568;
  line-height: 1.7;
  margin: 0;
  white-space: pre-line;
}

.job-meta-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #edf2f7;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.95rem;
  color: #4a5568;
}

.meta-item i {
  color: #718096;
  width: 22px;
  text-align: center;
  font-size: 1rem;
}

/* ====================
   JOB ACTIONS
   ==================== */
.job-actions {
  display: flex;
  justify-content: flex-end;
  gap: 15px;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #edf2f7;
}

.approve-btn,
.reject-btn {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 1rem;
  font-weight: 500;
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

/* ====================
   RESPONSIVE DESIGN
   ==================== */
@media (max-width: 1200px) {
  .sidebar {
    width: 260px;
  }
  .main-content {
    margin-left: 260px;
    padding: 35px;
  }
}

@media (max-width: 992px) {
  .sidebar {
    width: 240px;
  }
  .main-content {
    margin-left: 240px;
    padding: 30px;
  }
  .content-card {
    padding: 30px;
  }
}

@media (max-width: 768px) {
  .sidebar {
    width: 100%;
    height: auto;
    position: relative;
  }
  .main-content {
    margin-left: 0;
    padding: 25px;
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

@media (max-width: 576px) {
  .main-content {
    padding: 20px;
  }
  .content-card {
    padding: 25px 20px;
  }
  .job-card {
    padding: 25px 20px;
  }
  .job-actions {
    flex-direction: column;
  }
}
</style>