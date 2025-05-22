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
        <h1><i class="fas fa-comments"></i> All Comments</h1>
        <div class="header-actions">
          <button class="refresh-btn" @click="fetchComments">
            <i class="fas fa-sync-alt"></i> Refresh
          </button>
        </div>
      </div>

      <div class="content-card">
        <div v-if="loading" class="loading-spinner">
          <i class="fas fa-spinner fa-spin"></i> Loading comments...
        </div>

        <div v-else-if="error" class="error-message">
          <i class="fas fa-exclamation-circle"></i> {{ error }}
        </div>

        <div v-else-if="comments.length === 0" class="no-comments">
          <i class="fas fa-comment-slash"></i> No comments found.
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Job Title</th>
                <th>Comment Content</th>
                <th>User</th>
                <th>Posted</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="comment in comments" :key="comment.id">
                <td>
                  <router-link 
                    :to="`/jobs/${comment.job_id}`" 
                    class="job-title-link"
                  >
                    {{ comment.job.title }}
                  </router-link>
                </td>
                <td>{{ comment.content }}</td>
                <td>
                  <div class="user-info">
                    <img 
                      :src="comment.user.avatar || '/images/default-avatar.png'" 
                      class="user-avatar"
                      alt="User avatar"
                    >
                    <span class="user-name">{{ comment.user.name }}</span>
                  </div>
                </td>
                <td>{{ formatDate(comment.created_at) }}</td>
                <td>
                  <button 
                    class="btn btn-danger btn-sm"
                    @click="confirmDelete(comment)"
                  >
                    <i class="fas fa-trash"></i> Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div v-if="showDeleteModal" class="modal-overlay">
        <div class="modal-content">
          <div class="modal-header">
            <h5>Confirm Delete</h5>
            <button class="close-btn" @click="showDeleteModal = false">
              &times;
            </button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete this comment?</p>
            <p class="comment-preview">{{ selectedComment?.content }}</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="showDeleteModal = false">
              Cancel
            </button>
            <button 
              class="btn btn-danger" 
              @click="deleteComment(selectedComment)"
            >
              Confirm Delete
            </button>
          </div>
        </div>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="alert alert-success mt-3">
        <i class="fas fa-check-circle"></i> {{ successMessage }}
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'

export default {
    name: 'CommentsManagement',
  components: {
    Sidebar
  },
  data() {
    return {
      comments: [],
      loading: false,
      error: null,
      successMessage: '',
      showDeleteModal: false,
      selectedComment: null
    }
  },
  created() {
    this.fetchComments()
  },
  methods: {
    
    async fetchComments() {
      this.loading = true
      this.error = null
      try {
        const response = await fetch('http://localhost:8000/api/commentss', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
          }
        })
        
        if (!response.ok) throw new Error('Failed to fetch comments')
        
        const data = await response.json()
        this.comments = data
      } catch (err) {
        this.error = err.message
      } finally {
        this.loading = false
      }
    },

    formatDate(dateString) {
      if (!dateString) return 'N/A'
      const options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }
      return new Date(dateString).toLocaleDateString('en-US', options)
    },

    confirmDelete(comment) {
      this.selectedComment = comment
      this.showDeleteModal = true
    },

    async deleteComment(comment) {
      try {
        const response = await fetch(
          `http://localhost:8000/api/comments/${comment.id}`,
          {
            method: 'DELETE',
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
            }
          }
        )

        if (!response.ok) throw new Error('Failed to delete comment')

        this.successMessage = 'Comment deleted successfully'
        this.showDeleteModal = false
        this.fetchComments()
        setTimeout(() => this.successMessage = '', 3000)
      } catch (err) {
        this.error = err.message
      }
    },

    logout() {
      localStorage.removeItem('auth_token')
      this.$router.push('/login')
    }
  }
}
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


/* Reuse styles from pending-jobs and add specific styles */
.job-title-link {
  color: #2d3748;
  text-decoration: none;
  font-weight: 500;
}

.job-title-link:hover {
  color: #4299e1;
  text-decoration: underline;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.user-avatar {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  object-fit: cover;
}

.user-name {
  font-weight: 500;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 25px;
  border-radius: 8px;
  width: 450px;
  max-width: 95%;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.modal-header h5 {
  margin: 0;
  font-size: 1.3rem;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
}

.modal-body p {
  margin: 10px 0;
}

.comment-preview {
  background: #f8f9fa;
  padding: 10px;
  border-radius: 4px;
  font-style: italic;
}

.modal-footer {
  margin-top: 20px;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.table {
  margin-top: 20px;
}

.table th {
  font-weight: 600;
  background-color: #f8fafc;
}
.main-content{
    flex: 1;
    margin-left: 300px;
    padding: 20px;
}
.alert-success {
  position: fixed;
  bottom: 20px;
  right: 20px;
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from { transform: translateY(100%); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
</style>