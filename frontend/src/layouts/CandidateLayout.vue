<script setup>
import CandidateSidebar from "../components/candidate/CandidateSidebar.vue";

import { RouterLink, RouterView } from "vue-router";
</script>

<template>
  <div class="candidate-layout">
    <CandidateSidebar />
    <main class="candidate-content">
      <router-view />
    </main>
  </div>
</template>

<style scoped>
.candidate-layout {
  display: flex;
  min-height: 100vh;
}

.candidate-content {
  flex: 1;
  margin-left: 280px; /* Sidebar width */
  padding: 2rem;
}
</style>
