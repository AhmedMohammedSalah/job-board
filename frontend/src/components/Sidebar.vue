<template>
  <div class="sidebar">
    <div class="logo">Inventory System</div>
    <nav>
      <router-link to="/pending-jobs" class="nav-item">
        <i class="fas fa-box"></i> pending jobs
      </router-link>
      <router-link to="/job2" class="nav-item">
        <i class="fas fa-tasks"></i> jobs
      </router-link>
      <a href="#" class="nav-item logout" @click.prevent="logout">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </nav>
  </div>
</template>

<script>
export default {
  name: 'Sidebar',
  methods: {
    logout() {
      // Add your logout logic here
      console.log('Logging out...')
      // Example: this.$store.dispatch('logout')
      // Example: this.$router.push('/login')
    }
  }
}
</script>

<style scoped>
.sidebar {
  width: 200px;
  height: 100vh;
  background: #2c3e50;
  color: white;
  position: fixed;
  left: 0;
  top: 0;
  padding: 20px 0;
}

.logo {
  padding: 0 20px 20px;
  font-size: 1.2rem;
  font-weight: bold;
  border-bottom: 1px solid #34495e;
  margin-bottom: 20px;
  text-align: center;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  color: #ecf0f1;
  text-decoration: none;
  transition: all 0.3s;
}

.nav-item:hover {
  background: #34495e;
}

.nav-item i {
  margin-right: 10px;
  width: 20px;
  text-align: center;
}

.logout {
  margin-top: 20px;
  border-top: 1px solid #34495e;
  padding-top: 20px;
  color: #e74c3c;
}
</style>