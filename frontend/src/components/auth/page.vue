<template>
  <div class="role-selection-container">
    <div class="role-selection-card">
      <div class="header-section">
        <p class="login-prompt">Already have an account? <router-link to="/login">Log in</router-link></p>
        <h1 class="title">CREATE ACCOUNT AS A...</h1>
      </div>

      <div class="role-selection-section">
        <div class="error-message" v-if="error">{{ error }}</div>
        
        <div class="role-options-grid">
          <label class="role-option" :class="{ 'selected': role === 'candidate' }">
            <input type="radio" v-model="role" value="candidate" required>
            <div class="role-content">
              <div class="role-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
              </div>
              <span class="role-label">Candidate</span>
              <p class="role-description">Looking for your next career opportunity</p>
            </div>
          </label>

          <label class="role-option" :class="{ 'selected': role === 'employer' }">
            <input type="radio" v-model="role" value="employer" required>
            <div class="role-content">
              <div class="role-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                  <line x1="8" y1="21" x2="16" y2="21"></line>
                  <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg>
              </div>
              <span class="role-label">Employer</span>
              <p class="role-description">Hiring top talent for your organization</p>
            </div>
          </label>
        </div>
      </div>

      <button class="continue-button" @click="validateSelection">
        Continue
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 12h14M12 5l7 7-7 7"></path>
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const role = ref('')
const error = ref('')
const router = useRouter()

const validateSelection = () => {
  if (!role.value) {
    error.value = 'Please select a role to continue'
    return
  }
  
  router.push({
    path: '/register',
    query: { role: role.value }
  })
}
</script>

<style scoped>
.role-selection-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f8fafc;
  padding: 2rem;
}

.role-selection-card {
  width: 100%;
  max-width: 500px;
  background: white;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  padding: 2.5rem;
  text-align: center;
}

.header-section {
  margin-bottom: 2rem;
}

.login-prompt {
  color: #64748b;
  font-size: 0.95rem;
  margin-bottom: 1.5rem;
}

.login-prompt a {
  color: #4f46e5;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}

.login-prompt a:hover {
  color: #4338ca;
  text-decoration: underline;
}

.title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 1.5rem;
}

.role-selection-section {
  margin-bottom: 2rem;
}

.error-message {
  color: #ef4444;
  font-size: 0.875rem;
  margin-bottom: 1rem;
  font-weight: 500;
}

.role-options-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}

.role-option {
  display: block;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  padding: 1.5rem;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  background: white;
  position: relative;
  overflow: hidden;
}

.role-option:hover {
  border-color: #c7d2fe;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
}

.role-option.selected {
  border-color: #4f46e5;
  background-color: #f5f7ff;
}

.role-option.selected::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 24px;
  height: 24px;
  background-color: #4f46e5;
  clip-path: polygon(100% 0, 0 0, 100% 100%);
}

.role-option.selected::before {
  content: '✓';
  position: absolute;
  top: 2px;
  right: 2px;
  color: white;
  font-size: 0.7rem;
  z-index: 1;
}

.role-option input[type="radio"] {
  position: absolute;
  opacity: 0;
}

.role-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
}

.role-icon {
  width: 56px;
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #e0e7ff;
  border-radius: 50%;
  color: #4f46e5;
  transition: all 0.3s;
}

.role-option.selected .role-icon {
  background-color: #4f46e5;
  color: white;
}

.role-icon svg {
  width: 24px;
  height: 24px;
}

.role-label {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1e293b;
  transition: color 0.2s;
}

.role-option.selected .role-label {
  color: #4f46e5;
}

.role-description {
  color: #64748b;
  font-size: 0.9rem;
  margin-top: 0.25rem;
  line-height: 1.5;
}

.continue-button {
  width: 100%;
  padding: 0.875rem;
  background-color: #4f46e5;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.continue-button:hover {
  background-color: #4338ca;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
}

.continue-button:active {
  transform: translateY(0);
}

.continue-button svg {
  width: 16px;
  height: 16px;
}

@media (max-width: 640px) {
  .role-selection-card {
    padding: 1.5rem;
  }
  
  .title {
    font-size: 1.3rem;
  }
  
  .role-option {
    padding: 1.25rem;
  }
}
</style>