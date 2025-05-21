import { defineStore } from "pinia";
import axios from "axios";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    isAuthenticated: false,
    isLoading: false,
    error: null,
  }),

  actions: {
    async login(credentials) {
      try {
        this.isLoading = true;
        const response = await axios.post("/api/auth/login", credentials);
        localStorage.setItem("token", response.data.token);
        this.user = response.data.user;
        this.isAuthenticated = true;
        this.error = null;
      } catch (err) {
        this.error = err.response?.data?.message || "Login failed";
        throw err;
      } finally {
        this.isLoading = false;
      }
    },

    async logout() {
      try {
        await axios.post("/api/auth/logout");
        localStorage.removeItem("token");
        this.user = null;
        this.isAuthenticated = false;
      } catch (err) {
        console.error("Logout failed:", err);
        throw err;
      }
    },

    async fetchUser() {
      try {
        this.isLoading = true;
        const response = await axios.get("/api/auth/user");
        this.user = response.data;
        this.isAuthenticated = true;
        this.error = null;
      } catch (err) {
        this.error = err.response?.data?.message || "Failed to fetch user";
        throw err;
      } finally {
        this.isLoading = false;
      }
    },
  },
});
