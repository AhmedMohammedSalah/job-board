import { defineStore } from "pinia";
import axios from "axios";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    token: localStorage.getItem("auth_token") || null,
  }),
  actions: {
    async login(credentials) {
      try {
        const response = await axios.post(
          "http://localhost:8000/api/auth/login",
          credentials
        );
        this.token = response.data.token;
        this.user = response.data.user;
        localStorage.setItem("auth_token", this.token);
        axios.defaults.headers.common["Authorization"] = `Bearer ${this.token}`;
        return response.data;
      } catch (error) {
        throw error.response?.data?.message || "Login failed";
      }
    },
    async fetchUser() {
      try {
        const response = localStorage.getItem("user");
        this.user = response.data;
        return response.data;
      } catch (error) {
        this.logout();
        throw error.response?.data?.message || "Failed to fetch user";
      }
    },
    logout() {
      this.user = null;
      this.token = null;
      localStorage.removeItem("token");
      delete axios.defaults.headers.common["Authorization"];
    },
  },
});
