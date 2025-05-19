<template>
  <div class="candidate-layout">
    <!-- Sidebar -->
    <CandidateSidebar class="sidebar" />
    
    <!-- Main Content -->
    <main class="main-content">
      
      <div class="content-container">
        <slot></slot>
      </div>
    </main>
  </div>
</template>

<script setup>
import CandidateSidebar from '@/components/candidate/CandidateSidebar.vue'
</script>

<style scoped>
.candidate-layout {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 280px;
  background-color: var(--bg-primary);
  border-right: 1px solid var(--border-color);
  position: fixed;
  height: 100vh;
  z-index: 10;
}

.main-content {
  flex: 1;
  margin-left: 280px;
  background-color: var(--bg-secondary);
  min-height: 100vh;
}

.content-container {
  padding: var(--space-lg);
}

@media (max-width: 992px) {
  .sidebar {
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }
  
  .sidebar.active {
    transform: translateX(0);
  }
  
  .main-content {
    margin-left: 0;
  }
}
</style>