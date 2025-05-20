<script setup>
import StatsCard from "@/components/candidate/StatsCard.vue";
import RecentlyApplied from "@/components/candidate/RecentlyApplied.vue";
</script>

<template>
  <div class="content">
    <h2>Candidate Overview</h2>
    <div class="stats-container">
      <div class="stats-grid">
        <StatsCard title="Applied Jobs" value="24" type="applications" />

        <StatsCard title="Favorite Jobs" value="8" type="saved" />

        <StatsCard title="Job Alert" value="85" type="alert" />
      </div>
    </div>
    <!-- recently applied section  -->
    
    <RecentlyApplied />
  </div>
</template>

<style scoped>
.content {
  padding: 20px;
}

.stats-container {
  width: 100%;
  margin-top: 20px;
}

.stats-grid {
  display: flex;
  gap: 20px;
  justify-content: space-between;
}

.stats-grid > * {
  flex: 1;
  min-width: 0; /* Prevents flex item overflow */
}

@media (max-width: 768px) {
  .stats-grid {
    flex-direction: column;
    gap: 15px;
  }

  .stats-grid > * {
    width: 100%;
  }
}
</style>
