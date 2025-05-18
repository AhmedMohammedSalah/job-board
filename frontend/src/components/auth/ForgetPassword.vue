<template>
  <div class="container-fluid login-container">
    <div class="row g-0">
      <!-- Forgot Password Form Section -->
      <div class="col-lg-6 login-form-section">
        <div class="login-form-wrapper">
          <div class="header">
            <p class="register-prompt">Remembered your password? <a href="/login">Sign In</a></p>
            <h1 class="title">Forgot Password</h1>
          </div>

          <div v-if="alert.show" class="alert alert-dismissible fade show" :class="`alert-${alert.type}`">
            {{ alert.message }}
            <button type="button" class="btn-close" @click="alert.show = false"></button>
          </div>

          <form @submit.prevent="handleReset" class="needs-validation" :class="{ 'was-validated': validated }" novalidate>
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input
                type="email"
                class="form-control"
                id="email"
                v-model="email"
                required
                pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"
              >
              <div class="invalid-feedback">Please enter a valid email address</div>
            </div>

            <button type="submit" class="continue-btn" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              {{ loading ? 'Sending...' : 'Send Reset Link →' }}
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
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const email = ref('')
const validated = ref(false)
const loading = ref(false)
const router = useRouter()

const alert = ref({
  show: false,
  type: 'danger',
  message: ''
})

// Alert display helper
const showAlert = (type, message) => {
  alert.value = { show: true, type, message }
  setTimeout(() => { alert.value.show = false }, 5000)
}

const handleReset = async () => {
  validated.value = true
  loading.value = true
  
  // Basic form validation
  const form = document.querySelector('.needs-validation')
  if (!form.checkValidity()) {
    loading.value = false
    return
  }

  try {
    // Case-insensitive email search
    const response = await axios.get(
  `http://localhost:3000/users?email_like=${encodeURIComponent(email.value)}`
)

const matchedUser = response.data.find(
  user => user.email.toLowerCase() === email.value.toLowerCase()
)

if (!matchedUser) {
  throw new Error('Email not found in our system')
}


    // If email exists, redirect to reset password page
    showAlert('success', 'Please set your new password')
    setTimeout(() => {
      router.push(`/reset-password?email=${encodeURIComponent(email.value)}`)
    }, 1500)

  } catch (error) {
    // Handle both server and manual error
    if (error.response && error.response.status === 404) {
      showAlert('danger', 'Email not found in our system')
    } else {
      showAlert('danger', error.message || 'Failed to process your request')
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@import './style.css';
</style>