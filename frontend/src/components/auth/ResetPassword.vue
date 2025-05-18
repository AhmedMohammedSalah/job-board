<template>
    <div class="container-fluid login-container">
      <div class="row g-0">
        <!-- Reset Password Form Section -->
        <div class="col-lg-6 login-form-section">
          <div class="login-form-wrapper">
            <div class="header">
              <h1 class="title">Reset Password</h1>
              <p class="instruction-text">Please enter your new password below</p>
            </div>
  
            <div v-if="alert.show" class="alert alert-dismissible fade show" :class="`alert-${alert.type}`">
              {{ alert.message }}
              <button type="button" class="btn-close" @click="alert.show = false"></button>
            </div>
  
            <form @submit.prevent="handleReset" class="needs-validation" :class="{ 'was-validated': validated }" novalidate>
              <input type="hidden" v-model="email">
              
              <div class="mb-3 password-field">
                <label for="password" class="form-label">New Password</label>
                <div class="input-group">
                  <input
                    type="password"
                    id="password"
                    v-model="password"
                    class="form-control"
                    placeholder="Enter new password"
                    required
                    minlength="6"
                    :class="{'is-invalid': validated && (!password || password.length < 6)}"
                  >
                </div>
                <div class="invalid-feedback" v-if="validated && (!password || password.length < 6)">
                  Password must be at least 6 characters
                </div>
              </div>
  
              <div class="mb-3 password-field">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <div class="input-group">
                  <input
                    type="password"
                    id="confirmPassword"
                    v-model="confirmPassword"
                    class="form-control"
                    placeholder="Confirm new password"
                    required
                    :class="{'is-invalid': validated && confirmPassword !== password}"
                  >
                </div>
                <div class="invalid-feedback" v-if="validated && confirmPassword !== password">
                  Passwords do not match
                </div>
              </div>
  
              <div class="divider my-4"></div>
  
              <button type="submit" class="continue-btn" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                {{ loading ? 'Updating...' : 'Reset Password' }}
              </button>
            </form>
          </div>
        </div>
  
        <!-- Side Image Section -->
        <div class="col-lg-6 login-side-section">
          <div class="side-image-wrapper"></div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const email = ref(route.query.email || '')
const password = ref('')
const confirmPassword = ref('')
const validated = ref(false)
const loading = ref(false)

const alert = ref({
  show: false,
  type: 'danger',
  message: ''
})

const showAlert = (type, message) => {
  alert.value = { show: true, type, message }
  setTimeout(() => { alert.value.show = false }, 5000)
}

const handleReset = async () => {
  validated.value = true
  loading.value = true
  alert.value.show = false

  // Validate inputs
  if (!password.value || password.value.length < 6) {
    showAlert('danger', 'Password must be at least 6 characters')
    loading.value = false
    return
  }

  if (password.value !== confirmPassword.value) {
    showAlert('danger', 'Passwords do not match')
    loading.value = false
    return
  }

  try {
    // Find user by exact email match
    const response = await axios.get(`http://localhost:3000/users?email=${encodeURIComponent(email.value)}`)
    
    if (response.data.length === 0) {
      throw new Error('User account not found')
    }

    const user = response.data[0]
    
    // Update password using PUT instead of PATCH for full replacement
    await axios.put(`http://localhost:3000/users/${user.id}`, {
      ...user, // Keep all existing user data
      password: password.value // Only update the password
    })

    showAlert('success', 'Password updated successfully! Redirecting to login...')
    setTimeout(() => router.push('/login'), 2000)

  } catch (error) {
    showAlert('danger', error.message || 'Failed to reset password. Please try again.')
    console.error('Reset password error:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (!email.value) {
    showAlert('danger', 'No email provided. Please request a new password reset link.')
  }
})
</script>

<style scoped>
@import './style.css';

</style>
