<template>
  <header class="navbar">
    <div class="container">
      <div class="navbar-content">
        <!-- Logo -->
        <router-link to="/" class="logo">
          <BriefcaseIcon class="logo-icon" />
          <span>Jobpilot</span>
        </router-link>

        <!-- Location Dropdown -->
        <div class="location-selector" @click="toggleLocationDropdown">
          <MapPinIcon class="icon" />
          <span class="location-text">{{ selectedLocation }}</span>
          <ChevronDownIcon class="chevron" :class="{ 'rotate-180': showLocationDropdown }" />
          
          <div v-if="showLocationDropdown" class="dropdown-menu">
            <div 
              v-for="location in locations" 
              :key="location"
              class="dropdown-item"
              @click.stop="selectLocation(location)"
            >
              <MapPinIcon class="dropdown-icon" />
              {{ location }}
            </div>
          </div>
        </div>

        <!-- Search Bar -->
        <div class="search-bar">
          <MagnifyingGlassIcon class="search-icon" />
          <input 
            type="text" 
            placeholder="Job title, keyword, company" 
            v-model="searchQuery"
            @keyup.enter="performSearch"
          />
        </div>

        <!-- Before Login -->
        <div class="action-buttons" v-if="!isLoggedIn">
          <button class="btn btn-login" @click="navigateToLogin">
            <ArrowRightOnRectangleIcon class="btn-icon" />
            <span>Sign In</span>
          </button>
          <button class="btn btn-primary" @click="navigateToPostJob">
            <PlusCircleIcon class="btn-icon" />
            <span>Post A Job</span>
          </button>
        </div>

     
        <div class="user-profile" v-else>
          <div class="user-info">
            <span class="user-name">Welcome, {{ userName }}</span>
            <button class="logout-btn" @click="logout">
              <ArrowLeftOnRectangleIcon class="btn-icon" />
              <span>Logout</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { 
  BriefcaseIcon,
  MapPinIcon,
  ChevronDownIcon,
  MagnifyingGlassIcon,
  ArrowRightOnRectangleIcon,
  PlusCircleIcon,
  ArrowLeftOnRectangleIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const showLocationDropdown = ref(false)
const selectedLocation = ref('India')
const searchQuery = ref('')
const isLoggedIn = ref(false)
const userName = ref('')
const token = ref(localStorage.getItem('auth_token') || '')

// Fetch current user data
const fetchCurrentUser = async () => {
  if (token.value) {
    try {
      const response = await axios.get('http://localhost:8000/api/auth/get_current_user', {
        headers: { Authorization: `Bearer ${token.value}` }
      })
      isLoggedIn.value = true
      userName.value = response.data.name || 'User'
      console.log('Current user:', response.data)
    } catch (error) {
      console.error('Error fetching user:', error.response ? error.response.data : error.message)
      isLoggedIn.value = false
      userName.value = ''
      // localStorage.removeItem('auth_token')
      token.value = ''
    }
  } else {
    // Fallback to localStorage user data if token is missing
    const userData = JSON.parse(localStorage.getItem('user'))
    if (userData) {
      isLoggedIn.value = true
      userName.value = userData.name || userData.username || 'User'
    }
  }
}

onMounted(() => {
  token.value = localStorage.getItem('auth_token') || ''
  fetchCurrentUser()
  document.addEventListener('click', handleClickOutside)
})

const locations = [
  'India',
  'United States',
  'United Kingdom',
  'Canada',
  'Australia',
  'Germany',
  'Singapore'
]

const toggleLocationDropdown = () => {
  showLocationDropdown.value = !showLocationDropdown.value
}

const selectLocation = (location) => {
  selectedLocation.value = location
  showLocationDropdown.value = false
}

const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ path: '/jobs', query: { q: searchQuery.value } })
  }
}

const navigateToLogin = () => {
  router.push('/login')
}

const navigateToPostJob = () => {
  router.push('/post-job')
}

const logout = async () => {
  try {
    // Fetch CSRF cookie for Sanctum (required for POST requests)
    await axios.get('http://localhost:8000/sanctum/csrf-cookie')

    // Call logout endpoint with token
    if (token.value) {
      await axios.post('http://localhost:8000/api/logout', {}, {
        headers: { Authorization: `Bearer ${token.value}` }
      })
    }

    // Clear localStorage and reset state
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
    isLoggedIn.value = false
    userName.value = ''
    token.value = ''
    
    // Redirect to login page
    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error.response ? error.response.data : error.message)
    // Clear state and redirect even if API call fails
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
    isLoggedIn.value = false
    userName.value = ''
    token.value = ''
    router.push('/login')
  }
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.location-selector')) {
    showLocationDropdown.value = false
  }
}

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
@import './homestyle.css';
.logout-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.2rem;
  border: none;
  background: linear-gradient(90deg, #ff4d4d, #ff6b6b); /* Gradient red background */
  color: white;
  border-radius: 6px;
  font-weight: 500;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(255, 77, 77, 0.3); /* Subtle shadow */
}

.logout-btn:hover {
  background: linear-gradient(90deg, #ff3333, #ff5555); /* Darker gradient on hover */
  transform: translateY(-2px); /* Slight lift effect */
  box-shadow: 0 4px 8px rgba(255, 77, 77, 0.4); /* Enhanced shadow on hover */
}

.logout-btn:active {
  transform: translateY(0); /* Reset on click */
  box-shadow: 0 1px 2px rgba(255, 77, 77, 0.3); /* Reduced shadow on click */
}

.logout-btn .btn-icon {
  width: 1.1rem;
  height: 1.1rem;
  color: white; /* Ensure icon matches text color */
}
</style>