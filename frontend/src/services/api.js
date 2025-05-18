import axios from "axios";
import router from "@/router";

// Create axios instance
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || "http://localhost:8000/api",
  timeout: 10000,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

// Request interceptor
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("auth_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      switch (error.response.status) {
        case 401:
          if (router.currentRoute.value.name !== "Login") {
            // localStorage.removeItem("auth_token");
            router.push({ name: "Login" });
          }
          break;
        case 404:
          router.push({ name: "NotFound" });
          break;
        case 500:
          // Handle server errors
          break;
      }
    }
    return Promise.reject(error);
  }
);

// API endpoints
export default {
  // make object candidate to include all candidate end points
  candidate: {
    getProfile() {
      return api.get("/candidate");
    },
    getJobsRecentlyApplied() {
      return api.get("/recently-applied");
    },
    getSkills() {
      return api.get("/skills");
    },
    getCandidateSkills() {
      return api.get("/candidate-skills");
    },
    addSkill(name) {
      return api.post("/skills", { name });
    },
    addCandidateSkill(skillId, level) {
      return api.post("/candidate-skills", { skillId, level });
    },
    updateProfile(data, id) {
      return api.put(`candidate/${id}`, data);
    },
  },
};
