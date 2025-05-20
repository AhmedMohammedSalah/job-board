<template>
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
      <router-link to="/job2" class="nav-item">
        <i class="fas fa-clipboard-list"></i>
        <span>Active Jobs</span>
      </router-link>
      <a href="#" class="nav-item logout" @click.prevent="logout">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </nav>
  </div>
</template>

<script>
export default {
  name: 'Sidebar',
  methods: {
    logout() {
      console.log('Logging out...');
    }
  }
}
</script>

<style scoped>
/* Modern sidebar styling */
.sidebar {
  width: 260px;
  height: 100vh;
  background: linear-gradient(135deg, #1976d2, #0d47a1);
  color: white;
  position: fixed;
  left: 0;
  top: 0;
  padding: 25px 0;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
  font-family: 'Segoe UI', Arial, sans-serif;
}

/* Logo with icon aligned beside text */
.logo {
  display: flex;
  align-items: center;
  padding: 0 20px 25px;
  font-size: 1.5rem;
  font-weight: 700;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  margin-bottom: 25px;
}

.logo i {
  margin-right: 15px; /* Proper icon spacing */
  font-size: 1.8rem;
}

/* Menu items with perfectly aligned icons */
.nav-item {
  display: flex;
  align-items: center;
  padding: 16px 20px;
  color: #ecf0f1;
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 1.1rem;
  font-weight: 600;
  border-left: 4px solid transparent;
  margin: 8px 12px;
  border-radius: 5px;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.15);
  border-left: 4px solid #fff;
}

/* Icon alignment - perfectly beside text */
.nav-item i {
  width: 24px; /* Fixed width for perfect alignment */
  margin-right: 15px;
  font-size: 1.3rem;
  text-align: center;
}

/* Logout button with icon alignment */
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
</style>