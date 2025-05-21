<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { library } from "@fortawesome/fontawesome-svg-core";
import { 
  faThLarge,
  faBriefcase,
  faHeart,
  faBell,
  faSignOutAlt,
  faUser,
  // overview 
  faUserCircle,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

// Add icons to library
library.add(faThLarge, faBriefcase, faHeart, faBell, faSignOutAlt,faUser);

const router = useRouter();
const activeItem = ref("/candidate"); // Default active item

const menuItems = [
  // home 
  { icon: faThLarge, title: "Home", route: "/candidateHomePage" },
  // overview

  { icon: faUserCircle, title: "Overview", route: "/candidate" },
  { 
    icon: faBriefcase, 
    title: "Recently Applied Jobs", 
    route: "/candidate/recentlyApplied" 
  },
  { 
    icon: faHeart,
    title: "Favorite Jobs",
    route: "/favorite-jobs"
  },
  
  { 
    icon: faUser, 
    title: "Settings", 
    route: "/candidate/settings" 
  }
  // { 
  //   icon: faSignOutAlt, 
  //   title: "Logout", 
  //   route: "logout" 
  // }
];

const navigateTo = (route) => {
  if (route === "logout") {
    // Handle logout logic
    return;
  }
  activeItem.value = route;
  router.push(route);
};
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
            v-for="item in menuItems.slice(0, 4)"
            :key="item.route"
            :class="{ active: activeItem === item.route }"
            @click="navigateTo(item.route)"
          >
            <span class="menu-icon">
              <font-awesome-icon :icon="item.icon" />
            </span>
            <span class="menu-title">{{ item.title }}</span>
          </li>
        </ul>
      </div>

      <div class="menu-section">
        <h5 class="section-title">Account</h5>
        <ul>
          <li
            v-for="item in menuItems.slice(4)"
            :key="item.route"
            :class="{ active: activeItem === item.route }"
            @click="navigateTo(item.route)"
          >
            <span class="menu-icon">
              <font-awesome-icon :icon="item.icon" />
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
</style>