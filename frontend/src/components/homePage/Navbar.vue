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

        <!-- After Login -->
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

// تحميل حالة تسجيل الدخول عند التحميل
onMounted(() => {
  const userData = JSON.parse(localStorage.getItem('user'))
  if (userData) {
    isLoggedIn.value = true
    userName.value = userData.name || userData.username || 'User'
    console.log('User loaded:', userName.value) // للتأكد من تحميل البيانات
  }
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

const logout = () => {
  localStorage.removeItem('user')
  isLoggedIn.value = false
  userName.value = ''
  router.push('/login')
}

// إغلاق القوائم عند النقر خارجها
const handleClickOutside = (event) => {
  if (!event.target.closest('.location-selector')) {
    showLocationDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.navbar {
  background: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  padding: 1rem 0;
  position: sticky;
  top: 0;
  z-index: 1000;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

.navbar-content {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.5rem;
  font-weight: 700;
  color: #2563eb;
  text-decoration: none;
  margin-right: 1rem;
}

.logo-icon {
  width: 1.5rem;
  height: 1.5rem;
}

.location-selector {
  position: relative;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  background: #f8fafc;
  min-width: 120px;
}

.location-selector:hover {
  background: #f1f5f9;
}

.location-text {
  font-weight: 500;
  font-size: 0.9rem;
  color: #334155;
}

.icon {
  width: 1.1rem;
  height: 1.1rem;
  color: #64748b;
}

.chevron {
  width: 1rem;
  height: 1rem;
  color: #64748b;
  transition: transform 0.2s;
}

.rotate-180 {
  transform: rotate(180deg);
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  background: white;
  border-radius: 6px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  width: 100%;
  z-index: 10;
  margin-top: 0.5rem;
  overflow: hidden;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  font-size: 0.9rem;
  color: #334155;
  cursor: pointer;
}

.dropdown-item:hover {
  background: #f8fafc;
}

.dropdown-icon {
  width: 1rem;
  height: 1rem;
  color: #64748b;
}

.search-bar {
  flex: 1;
  display: flex;
  align-items: center;
  background: #f8fafc;
  border-radius: 6px;
  padding: 0.5rem 1rem;
  max-width: 500px;
  transition: all 0.2s;
}

.search-bar:focus-within {
  background: #f1f5f9;
  box-shadow: 0 0 0 2px #e0e7ff;
}

.search-icon {
  width: 1.1rem;
  height: 1.1rem;
  color: #64748b;
  margin-right: 0.5rem;
}

.search-bar input {
  flex: 1;
  border: none;
  background: transparent;
  outline: none;
  font-size: 0.9rem;
  color: #334155;
}

.search-bar input::placeholder {
  color: #94a3b8;
}

.action-buttons {
  display: flex;
  gap: 1rem;
  margin-left: auto;
}

.btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.2rem;
  border-radius: 6px;
  font-weight: 500;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-icon {
  width: 1.1rem;
  height: 1.1rem;
}

.btn-login {
  border: 1px solid #e2e8f0;
  background: white;
  color: #334155;
}

.btn-login:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
}

.btn-primary {
  background: #2563eb;
  color: white;
  border: none;
}

.btn-primary:hover {
  background: #1d4ed8;
}

/* User Profile Styles */
.user-profile {
  margin-left: auto;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-name {
  font-weight: 500;
  font-size: 0.95rem;
  color: #334155;
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem;
  border: none;
  background: transparent;
  cursor: pointer;
  color: #ef4444;
  font-size: 0.9rem;
}

.logout-btn:hover {
  color: #dc2626;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .navbar-content {
    gap: 1rem;
  }
  
  .search-bar {
    max-width: 350px;
  }
}

@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }
  
  .navbar-content {
    flex-wrap: wrap;
    gap: 0.75rem;
    padding: 0.5rem 0;
  }
  
  .search-bar {
    order: 3;
    width: 100%;
    max-width: 100%;
    margin-top: 0.5rem;
  }
  
  .location-selector {
    min-width: 100px;
  }
  
  .btn span {
    display: none;
  }
  
  .btn-icon {
    margin: 0;
  }
  
  .user-name {
    display: none;
  }
}
</style>
<!-- <style scoped>
@import './homestyle.css';
</style> -->