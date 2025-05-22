<!-- SidebarComponent.vue -->
<template>
  <div class="sidebar">
    <div class="sidebar-header">EMPLOYERS DASHBOARD</div>
    <ul class="sidebar-nav">
      <li
        v-for="(item, index) in navItems"
        :key="index"
        :class="['sidebar-item', { active: item.isActive }]"
      >
        <a href="#" class="sidebar-link" @click.prevent="selectItem(index)">
          <i :class="['bi', item.icon]"></i>
          {{ item.label }}
        </a>
      </li>
    </ul>

    <div class="sidebar-footer">
      <a href="#" class="logout-link" @click.prevent="logout">
        <i class="bi bi-box-arrow-right"></i>
        Log-out
      </a>
    </div>
  </div>
</template>

<script>
import { ref } from "vue";

export default {
  name: "SidebarComponent",

  props: {
    initialActive: {
      type: Number,
      default: 0,
    },
  },

  setup(props, { emit }) {
    const navItems = ref([
      {
        label: "Overview",
        icon: "bi-grid",
        isActive: props.initialActive === 0,
        route: "EmployerDashboard",
      },

      {
        label: "Post a Job",
        icon: "bi-plus-square",
        isActive: props.initialActive === 2,
        route: "Postjob",
      },
      {
        label: "MyJobs",
        icon: "bi-briefcase",
        isActive: props.initialActive === 3,
        route: "MyJobs",
      },
    ]);
    const selectItem = (index) => {
      navItems.value.forEach((item) => (item.isActive = false));
      navItems.value[index].isActive = true;
      emit("navigate", navItems.value[index].route);
    };

    const logout = () => {
      emit("logout");
    };

    return {
      navItems,
      selectItem,
      logout,
    };
  },
};
</script>

<style scoped>
.sidebar {
  width: 250px;
  background-color: #fff;
  border-right: 1px solid #e9ecef;
  display: flex;
  flex-direction: column;
  position: relative;
  height: 100vh;
}

.sidebar-header {
  padding: 1rem;
  font-size: 0.75rem;
  font-weight: 500;
  color: #6c757d;
}

.sidebar-nav {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar-item {
  margin-bottom: 1px;
}

.sidebar-item.active {
  background-color: #e9f0ff;
}

.sidebar-item.active .sidebar-link {
  color: #2c7be5;
}

.sidebar-link {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  color: #495057;
  text-decoration: none;
}

.sidebar-link:hover {
  background-color: #f8f9fa;
  text-decoration: none;
}

.sidebar-link i {
  margin-right: 0.75rem;
  font-size: 1.1rem;
}

.sidebar-footer {
  margin-top: auto;
  padding: 1rem;
}

.logout-link {
  display: flex;
  align-items: center;
  color: #495057;
  text-decoration: none;
}

.logout-link i {
  margin-right: 0.5rem;
}
</style>
