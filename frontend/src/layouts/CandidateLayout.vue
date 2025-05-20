<script setup>
import CandidateSidebar from "../components/candidate/CandidateSidebar.vue";


import { RouterLink, RouterView } from "vue-router";
import AppHeader from "../components/homePage/AppHeader.vue";
import Navbar from "../components/homePage/Navbar.vue";
import AppFooter from "../components/homePage/AppFooter.vue";
</script>

<template>
  <AppHeader />
  <Navbar />
  <div class="candidate-layout">
    <CandidateSidebar />
    <main class="candidate-content">
      <router-view />
    </main>
  </div>
  <AppFooter />
</template>

<style scoped>
.candidate-layout {
  display: flex;
  min-height: 90vh;
  margin-top: 20px;
}

.candidate-content {
  flex: 1;
  margin-left: 280px; /* Sidebar width */
  padding: 2rem;
}
</style>
