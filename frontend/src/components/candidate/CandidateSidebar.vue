<template>
  <aside class="sidebar">
    <!-- Profile Section -->
    <div class="profile-section">
      <div class="profile-image">
        <img :src="user.avatar || '/default-avatar.jpg'" alt="Profile">
        <span class="online-status"></span>
      </div>
      <div class="profile-info">
        <h4>{{ user.name }}</h4>
        <p class="text-secondary">{{ user.title }}</p>
      </div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="sidebar-menu">
      <router-link 
        v-for="item in menuItems" 
        :key="item.path" 
        :to="item.path"
        class="menu-item"
        active-class="active"
      >
        <i :class="item.icon"></i>
        <span>{{ item.label }}</span>
        <span v-if="item.badge" class="badge">{{ item.badge }}</span>
      </router-link>
    </nav>
    
    <!-- Footer Section -->
    <div class="sidebar-footer">
      <button class="btn btn-outline btn-sm">
        <i class="fas fa-cog"></i> Settings
      </button>
      <button class="btn btn-outline btn-sm" @click="logout">
        <i class="fas fa-sign-out-alt"></i> Logout
      </button>
    </div>
  </aside>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const user = ref({
  name: 'Mohamed Ahmed',
  title: 'Senior Frontend Developer',
  avatar: ''
})

const menuItems = ref([
  { path: '/candidate/dashboard', label: 'Dashboard', icon: 'fas fa-home' },
  { path: '/candidate/profile', label: 'My Profile', icon: 'fas fa-user' },
  { path: '/candidate/jobs', label: 'Job Applications', icon: 'fas fa-briefcase' },
  { path: '/candidate/saved-jobs', label: 'Saved Jobs', icon: 'fas fa-bookmark', badge: 3 },
  { path: '/candidate/messages', label: 'Messages', icon: 'fas fa-envelope', badge: 5 },
  { path: '/candidate/notifications', label: 'Notifications', icon: 'fas fa-bell', badge: 2 },
  { path: '/candidate/skills', label: 'My Skills', icon: 'fas fa-code' },
  { path: '/candidate/resume', label: 'My Resume', icon: 'fas fa-file-alt' },
  { path: '/candidate/settings', label: 'Settings', icon: 'fas fa-cog' }
])

const logout = () => {
  // Handle logout logic
  router.push('/login')
}
</script>

<style scoped>
.sidebar {
  padding: var(--space-lg) var(--space-md);
  display: flex;
  flex-direction: column;
  height: 100%;
}

.profile-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-bottom: var(--space-lg);
  margin-bottom: var(--space-md);
  border-bottom: 1px solid var(--border-color);
}

.profile-image {
  position: relative;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  overflow: hidden;
  margin-bottom: var(--space-sm);
}

.profile-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.online-status {
  position: absolute;
  bottom: 5px;
  right: 5px;
  width: 15px;
  height: 15px;
  border-radius: 50%;
  background-color: var(--secondary-color);
  border: 2px solid var(--bg-primary);
}

.profile-info {
  text-align: center;
}

.profile-info h4 {
  margin-bottom: var(--space-xs);
}

.sidebar-menu {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: var(--space-xs);
}

.menu-item {
  display: flex;
  align-items: center;
  padding: var(--space-sm) var(--space-md);
  border-radius: var(--border-radius);
  color: var(--text-secondary);
  transition: all 0.2s ease;
  position: relative;
}

.menu-item:hover {
  background-color: var(--bg-secondary);
  color: var(--primary-color);
}

.menu-item.active {
  background-color: var(--primary-light);
  color: var(--primary-color);
  font-weight: 500;
}

.menu-item i {
  margin-right: var(--space-sm);
  width: 20px;
  text-align: center;
}

.badge {
  margin-left: auto;
  background-color: var(--primary-color);
  color: white;
  font-size: 0.75rem;
  padding: 0.15rem 0.5rem;
  border-radius: 9999px;
}

.sidebar-footer {
  display: flex;
  flex-direction: column;
  gap: var(--space-sm);
  padding-top: var(--space-md);
  border-top: 1px solid var(--border-color);
}

.btn-sm {
  padding: 0.5rem;
  font-size: 0.875rem;
}
</style>