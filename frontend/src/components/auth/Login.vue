

<template>
  <div class="container-fluid login-container">
    <div class="row g-0">
      <div class="col-lg-6 login-form-section">
        <div class="login-form-wrapper">
          <div class="header">
            <p class="register-prompt">Don't have account? <a href="/page">Create Account</a></p>
            <h1 class="title">SIGN IN</h1>
          </div>

          <div v-if="alert.show" class="alert alert-dismissible fade show" :class="`alert-${alert.type}`">
            {{ alert.message }}
            <button type="button" class="btn-close" @click="alert.show = false"></button>
          </div>

          <form @submit.prevent="handleLogin" class="needs-validation" :class="{ 'was-validated': validated }" novalidate>
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
              <div class="invalid-feedback">
                Please enter a valid email address
              </div>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input 
                type="password" 
                class="form-control" 
                id="password" 
                v-model="password" 
                required
                minlength="8"
              >
              <div class="invalid-feedback">
                Password must be at least 8 characters
              </div>
            </div>

            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="remember" v-model="rememberMe">
              <label class="form-check-label" for="remember">Remember Me</label>
            </div>

            <div class="mb-3 text-start">
              <a href="/ForgetPassword" class="text-primary">Forgot Password?</a>
            </div>
  
            <button type="submit" class="continue-btn" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              {{ loading ? 'Signing In...' : 'Sign In →' }}
            </button>
          </form>

          <div class="social-login">
            <p class="divider"><span>OR</span></p>
            <button class="social-btn facebook-btn">
              <i class="bi bi-facebook"></i> Sign In with Facebook
            </button>
            <button class="social-btn google-btn">
              <i class="bi bi-google"></i> Sign In with Google
            </button>
          </div>
        </div>
      </div>

      <div class="col-lg-6 login-side-section">
        <div class="side-image-wrapper"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

axios.defaults.withCredentials = true

const email = ref('')
const password = ref('')
const rememberMe = ref(false)
const validated = ref(false)
const loading = ref(false)
const router = useRouter()

const alert = ref({
  show: false,
  type: 'danger',
  message: ''
})

const showAlert = (type, message) => {
  alert.value = { show: true, type, message }
  setTimeout(() => { alert.value.show = false }, 5000)
}

const handleLogin = async () => {
  validated.value = true
  loading.value = true
  
  const form = document.querySelector('.needs-validation')
  if (!form.checkValidity()) {
    loading.value = false
    return
  }

  try {
    console.log('Fetching CSRF cookie...');
    const csrfResponse = await axios.get('http://localhost:8000/sanctum/csrf-cookie');
    console.log('CSRF cookie response:', csrfResponse);

    console.log('Sending login request with payload:', { email: email.value, password: password.value });
    const response = await axios.post('http://localhost:8000/api/auth/login', {
      email: email.value,
      password: password.value
    });
    console.log('Login response:', response);

    if (response.data.success) {
      localStorage.setItem('auth_token', response.data.token)
      if (rememberMe.value) {
        localStorage.setItem('user', JSON.stringify(response.data.user))
      }

      showAlert('success', 'Login successful! Redirecting...')
      setTimeout(() => {
        if (response.data.user.role === 'candidate') {
          router.push('/home')
        } else if (response.data.user.role === 'employer') {
          router.push('/home')
        } else {
          router.push('/home')
        }
      }, 1500)
    }
  } catch (error) {
    loading.value = false
    console.log('Login error:', error);
    console.log('Error response:', error.response);
    if (error.response) {
      if (error.response.status === 401) {
        showAlert('danger', 'Invalid email or password.')
      } else if (error.response.status === 422) {
        const errors = error.response.data.errors
        showAlert('danger', errors.email?.[0] || errors.password?.[0] || 'Validation failed.')
      } else {
        showAlert('danger', `Login failed: ${error.response.statusText || 'Server error.'}`)
      }
    } else {
      showAlert('danger', `Network error: ${error.message || 'Could not connect to the server.'}`)
    }
  }
}
</script>

<style scoped>
@import './style.css';
</style>


<!-- 
<template>
  <div class="container-fluid login-container">
    <div class="row g-0">
      <div class="col-lg-6 login-form-section">
        <div class="login-form-wrapper">
          <div class="header">
            <p class="register-prompt">Don't have account? <a href="/page">Create Account</a></p>
            <h1 class="title">SIGN IN</h1>
          </div>

          <div v-if="alert.show" class="alert alert-dismissible fade show" :class="`alert-${alert.type}`">
            {{ alert.message }}
            <button type="button" class="btn-close" @click="alert.show = false"></button>
          </div>

          <form @submit.prevent="handleLogin" class="needs-validation" :class="{ 'was-validated': validated }" novalidate>
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
              <div class="invalid-feedback">
                Please enter a valid email address
              </div>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input 
                type="password" 
                class="form-control" 
                id="password" 
                v-model="password" 
                required
                minlength="8"
              >
              <div class="invalid-feedback">
                Password must be at least 8 characters
              </div>
            </div>

            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="remember" v-model="rememberMe">
              <label class="form-check-label" for="remember">Remember Me</label>
            </div>

            <div class="mb-3 text-start">
              <a href="/ForgetPassword" class="text-primary">Forgot Password?</a>
            </div>
  
            <button type="submit" class="continue-btn" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              {{ loading ? 'Signing In...' : 'Sign In →' }}
            </button>
          </form>

          <div class="social-login">
            <p class="divider"><span>OR</span></p>
            <button class="social-btn facebook-btn">
              <i class="bi bi-facebook"></i> Sign In with Facebook
            </button>
            <button class="social-btn google-btn">
              <i class="bi bi-google"></i> Sign In with Google
            </button>
          </div>
        </div>
      </div>

      <div class="col-lg-6 login-side-section">
        <div class="side-image-wrapper"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const email = ref('')
const password = ref('')
const rememberMe = ref(false)
const validated = ref(false)
const loading = ref(false)
const router = useRouter()

const alert = ref({
  show: false,
  type: 'danger',
  message: ''
})

const showAlert = (type, message) => {
  alert.value = { show: true, type, message }
  setTimeout(() => { alert.value.show = false }, 5000)
}

const handleLogin = async () => {
  validated.value = true
  loading.value = true
  
  const form = document.querySelector('.needs-validation')
  if (!form.checkValidity()) {
    loading.value = false
    return
  }

  try {
    await axios.get('http://localhost:8000/sanctum/csrf-cookie')

    const response = await axios.post('http://localhost:8000/api/login', {
      email: email.value,
      password: password.value
    })

    if (response.data.success) {
      const token = response.data.token
      localStorage.setItem('auth_token', token)

      if (rememberMe.value) {
        localStorage.setItem('user', JSON.stringify(response.data.user))
      }

      showAlert('success', 'Login successful! Redirecting...')
      setTimeout(() => {
        const role = response.data.user.role
        if (role === 'candidate') {
          router.push('/home')
        } else if (role === 'employer') {
          router.push('/home')
        } else {
          router.push('/home')
        }
      }, 1500)
    } else {
      throw new Error('Login failed: Unexpected response')
    }
  } catch (error) {
    loading.value = false
    console.error('Login error:', error.response ? error.response.data : error.message)
    if (error.response) {
      if (error.response.status === 401) {
        showAlert('danger', 'Invalid email or password.')
      } else if (error.response.status === 422) {
        const errors = error.response.data.errors
        showAlert('danger', errors.email?.[0] || errors.password?.[0] || 'Validation failed.')
      } else {
        showAlert('danger', `Login failed: ${error.response.statusText || 'Server error.'}`)
      }
    } else {
      showAlert('danger', `Network error: ${error.message || 'Could not connect to the server.'}`)
    }
  }
}
</script>

<style scoped>
@import './style.css';
</style> -->