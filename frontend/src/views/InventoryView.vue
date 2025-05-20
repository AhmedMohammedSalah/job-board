<template>
  <div class="app-layout">
    <Sidebar />
    <div class="main-content">
      <div class="inventory-container">
        <h1>Inventory Management</h1>
        <p>This is a clean inventory page with no data.</p>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'

export default {
  name: 'InventoryView',
  components: {
    Sidebar
  }
}
</script>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
}

.main-content {
  flex: 1;
  margin-left: 200px;
  padding: 20px;
}

.inventory-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  background: white;
  border-radius: 5px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
</style>