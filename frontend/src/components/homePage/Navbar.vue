<template>
  <header class="navbar">
    <div class="navbar-container">
      <!-- Logo Section -->
      <router-link to="/" class="logo-section">
        <BriefcaseIcon class="logo-icon" />
        <span class="logo-text">JobPilot</span>
        <div class="logo-badge">Pro</div>
      </router-link>
       <div class="nav-links">
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
        <div class="nav-link"></div>
       </div>

      

      <!-- User Actions -->
      <div class="user-actions">
        <button
          v-if="!isLoggedIn"
          class="auth-btn login-btn"
          @click="navigateToLogin"
        >
          <ArrowRightOnRectangleIcon class="btn-icon" />
          <span>Sign In</span>
        </button>

        <button
          v-if="!isLoggedIn"
          class="auth-btn primary-btn"
          @click="navigateToPostJob"
        >
          <PlusCircleIcon class="btn-icon" />
          <span>Post Job</span>
          <RocketLaunchIcon class="btn-decorator" />
        </button>

        <div v-if="isLoggedIn" class="user-profile">
          <!-- <button class="icon-btn notification-btn">
            <BellAlertIcon class="icon" />
            <span class="notification-badge">3</span>
          </button> -->

          <div class="user-avatar">
            <UserCircleIcon class="avatar-icon" />
          </div>

          <div class="user-info">
            <span class="user-greeting">Welcome back</span>
            <span class="user-name">{{ userName }}</span>
          </div>

          <button class="logout-btn" @click="logout">
            <ArrowLeftOnRectangleIcon class="btn-icon" />
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import {
  BriefcaseIcon,
  ArrowRightOnRectangleIcon,
  PlusCircleIcon,
  ArrowLeftOnRectangleIcon,
  Squares2X2Icon,
  BuildingOffice2Icon,
  UserGroupIcon,
  BellIcon,
  BellAlertIcon,
  UserCircleIcon,
  RocketLaunchIcon,
} from "@heroicons/vue/24/outline";

const router = useRouter();
const isLoggedIn = ref(false);
const userName = ref("");
const token = ref(localStorage.getItem("auth_token") || "");

// Fetch current user data
const fetchCurrentUser = async () => {
  if (token.value) {
    try {
      const response = await axios.get(
        "http://localhost:8000/api/auth/get_current_user",
        {
          headers: { Authorization: `Bearer ${token.value}` },
        }
      );
      isLoggedIn.value = true;
      userName.value = response.data.name || "User";
    } catch (error) {
      console.error("Error fetching user:", error);
      isLoggedIn.value = false;
      userName.value = "";
      token.value = "";
    }
  } else {
    const userData = JSON.parse(localStorage.getItem("user"));
    if (userData) {
      isLoggedIn.value = true;
      userName.value = userData.name || userData.username || "User";
    }
  }
};

onMounted(() => {
  token.value = localStorage.getItem("auth_token") || "";
  fetchCurrentUser();
});

const navigateToLogin = () => router.push("/login");
const navigateToPostJob = () => router.push("/login");

const logout = async () => {
  try {
    await axios.get("http://localhost:8000/sanctum/csrf-cookie");
    if (token.value) {
      await axios.post(
        "http://localhost:8000/api/logout",
        {},
        {
          headers: { Authorization: `Bearer ${token.value}` },
        }
      );
    }
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user");
    isLoggedIn.value = false;
    userName.value = "";
    token.value = "";
    router.push("/login");
  } catch (error) {
    console.error("Logout error:", error);
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user");
    isLoggedIn.value = false;
    userName.value = "";
    token.value = "";
    router.push("/login");
  }
};
</script>

<style scoped>
.navbar {
  background: #ffffff;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  padding: 0.5rem 0;
  position: sticky;
  top: 0;
  z-index: 1000;
  border-bottom: 1px solid #f1f5f9;
}

.navbar-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 70px;
}

/* Logo Section */
.logo-section {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  text-decoration: none;
  position: relative;
}

.logo-icon {
  width: 28px;
  height: 28px;
  color: #3b82f6;
  background: #eff6ff;
  padding: 6px;
  border-radius: 8px;
}

.logo-text {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  background: linear-gradient(90deg, #3b82f6, #6366f1);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.logo-badge {
  position: absolute;
  top: -8px;
  right: -20px;
  background: #10b981;
  color: white;
  font-size: 0.6rem;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 10px;
  text-transform: uppercase;
}

/* Navigation Links */
.nav-links {
  display: flex;
  gap: 1.5rem;
  margin-left: 3rem;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
  color: #64748b;
  font-weight: 500;
  font-size: 0.95rem;
  padding: 0.5rem 0;
  position: relative;
  transition: all 0.3s ease;
}

.nav-link:hover {
  color: #3b82f6;
}

.nav-link.router-link-active {
  color: #3b82f6;
}

.nav-link.router-link-active::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 2px;
  background: #3b82f6;
  border-radius: 2px;
}

.nav-icon {
  width: 18px;
  height: 18px;
}

/* User Actions */
.user-actions {
  margin: 0 30rem;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.auth-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.7rem 1.3rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
  position: relative;
  overflow: hidden;
}

.login-btn {
  background: white;
  color: #3b82f6;
  border: 1px solid #e2e8f0;
}

.login-btn:hover {
  background: #f8fafc;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
}

.primary-btn {
  background: linear-gradient(135deg, #3b82f6, #6366f1);
  color: white;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
  padding-right: 2.5rem;
}

.primary-btn:hover {
  background: linear-gradient(135deg, #2563eb, #4f46e5);
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(59, 130, 246, 0.25);
}

.btn-decorator {
  position: absolute;
  right: 10px;
  width: 16px;
  height: 16px;
  color: white;
  opacity: 0.8;
}

/* User Profile */
.user-profile {
  display: flex;
  align-items: center;
  gap: 1.25rem;
}

.icon-btn {
  background: none;
  border: none;
  cursor: pointer;
  position: relative;
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.icon-btn:hover {
  background: #f1f5f9;
}

.notification-btn {
  color: #64748b;
}

.notification-badge {
  position: absolute;
  top: 2px;
  right: 2px;
  background: #ef4444;
  color: white;
  font-size: 0.6rem;
  font-weight: 700;
  width: 16px;
  height: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #eff6ff;
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar-icon {
  width: 24px;
  height: 24px;
  color: #3b82f6;
}

.user-info {
  display: flex;
  flex-direction: column;
}

.user-greeting {
  font-size: 0.7rem;
  color: #64748b;
}

.user-name {
  font-size: 0.9rem;
  font-weight: 600;
  color: #1e293b;
}

.logout-btn {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.logout-btn:hover {
  background: #f1f5f9;
  color: #ef4444;
}

.btn-icon {
  width: 18px;
  height: 18px;
}

.icon {
  width: 20px;
  height: 20px;
}
</style>
