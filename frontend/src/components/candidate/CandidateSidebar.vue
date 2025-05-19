<script setup>
import { ref, watch } from "vue";
import { useRouter, useRoute } from "vue-router";

const router = useRouter();
const route = useRoute();
const activeItem = ref(route.name); // Initialize to current route name

const menuItems = [
  { icon: "grid-view", title: "Overview", route: "candidate-overview" },
  { icon: "briefcase", title: "Applied Jobs", route: "applied-jobs" },
  { icon: "heart", title: "candidate-saved-jobs", route: "candidate-saved-jobs" },
  // { icon: 'alarm', title: 'Job Alerts', route: 'candidate-alerts' },
  { icon: "logout", title: "Logout", route: "logout" },
];

const navigateTo = async (routeName) => {
  if (routeName === "logout") {
    // Handle logout logic
    return;
  }
  if (route.name === routeName) {
    return; // Prevent redundant navigation
  }
  try {
    await router.push( { name: routeName } );
    
    activeItem.value = routeName;
    // reload the page to reflect the new route
    window.location.reload();
  } catch (error) {
    console.error("Navigation error:", error);
  }
};

// Watch for route changes to update activeItem accordingly
watch(
  () => route.name,
  (newName) => {
    activeItem.value = newName;
  }
);
</script>

<template>
  <div class="candidate-sidebar">
    <div class="sidebar-header">
      <div class="profile-summary">
        <div class="avatar">
          <!-- <img src="@/assets/images/avatar-placeholder.png" alt="Candidate Avatar"> -->
        </div>
        <!-- <div class="profile-info">
          <h4>John Doe</h4>
          <p>UI/UX Designer</p>
        </div> -->
      </div>
    </div>

    <div class="sidebar-menu">
      <div class="menu-section">
        <h5 class="section-title">Candidate Dashboard</h5>
        <ul>
          <li
            v-for="item in menuItems.slice(0, 5)"
            :key="item.route"
            :class="{ active: activeItem === item.route }"
            @click="navigateTo(item.route)"
          >
            <span class="menu-icon">
              <i :class="`icon-${item.icon}`"></i>
            </span>
            <span class="menu-title">{{ item.title }}</span>
          </li>
        </ul>
      </div>

      <div class="menu-section">
        <h5 class="section-title">Account</h5>
        <ul>
          <li
            v-for="item in menuItems.slice(5)"
            :key="item.route"
            :class="{ active: activeItem === item.route }"
            @click="navigateTo(item.route)"
          >
            <span class="menu-icon">
              <i :class="`icon-${item.icon}`"></i>
            </span>
            <span class="menu-title">{{ item.title }}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<style scoped>
.candidate-sidebar {
  width: 280px;
  height: 100vh;
  background: #ffffff;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
  position: fixed;
  left: 0;
  top: 0;
  padding: 20px 0;
  display: flex;
  flex-direction: column;
}

.sidebar-header {
  padding: 0 20px 20px;
  border-bottom: 1px solid #f0f0f0;
}

.profile-summary {
  display: flex;
  align-items: center;
  gap: 15px;
}

.avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  overflow: hidden;
}

.avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.profile-info h4 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
}

.profile-info p {
  margin: 5px 0 0;
  font-size: 13px;
  color: #6b6b6b;
}

.sidebar-menu {
  flex: 1;
  overflow-y: auto;
  padding: 20px 0;
}

.menu-section {
  margin-bottom: 25px;
}

.section-title {
  padding: 0 20px;
  font-size: 12px;
  text-transform: uppercase;
  color: #999;
  margin-bottom: 15px;
  font-weight: 500;
}

ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

li {
  padding: 10px 20px;
  display: flex;
  align-items: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

li:hover {
  background: #f8f8f8;
}

li.active {
  background: #f0f7ff;
  position: relative;
}

li.active::before {
  content: "";
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 3px;
  background: #1967d2;
}

.menu-icon {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12px;
  color: #6b6b6b;
}

li.active .menu-icon {
  color: #1967d2;
}

.menu-title {
  font-size: 14px;
  font-weight: 500;
  color: #1a1a1a;
}

li.active .menu-title {
  color: #1967d2;
  font-weight: 600;
}

/* Simple icon representation - replace with actual icon component or font icons */
[class^="icon-"]::before {
  font-family: "Material Icons";
  font-style: normal;
  font-weight: normal;
  speak: never;
  display: inline-block;
  text-decoration: none;
  width: 1em;
  text-align: center;
  font-variant: normal;
  text-transform: none;
  line-height: 1;
  -webkit-font-smoothing: antialiased;
}

.icon-grid-view::before {
  content: "\e8a8";
}
.icon-profile::before {
  content: "\e853";
}
.icon-briefcase::before {
  content: "\e8ae";
}
.icon-heart::before {
  content: "\e87e";
}
.icon-alarm::before {
  content: "\e855";
}
.icon-message::before {
  content: "\e0cb";
}
.icon-resume::before {
  content: "\e24a";
}
.icon-password::before {
  content: "\e899";
}
.icon-logout::before {
  content: "\e9ba";
}
</style>
