import { defineStore } from "pinia";
import api from "./api";

export const useEmployerStore = defineStore("employer", {
  state: () => ({
    jobs: [],
    stats: {
      openJobs: 0,
      savedCandidates: 0,
    },
    selectedJob: null,
    isLoading: false,
    error: null,
    showApplicationsPanel: false,
  }),

  actions: {
    async fetchDashboardData() {
      try {
        this.isLoading = true;
        this.error = null;

        const [jobsResponse, userResponse] = await Promise.all([
          api.get("/jobs"),
          api.get("/auth/user"),
        ]);

        if (!Array.isArray(jobsResponse.data)) {
          throw new Error("Invalid jobs data format received from server");
        }

        this.jobs = jobsResponse.data.map((job) => ({
          id: job.id,
          title: job.title,
          job_type: job.job_type || "Full Time",
          timeRemaining: this.calculateTimeRemaining(
            job.expiry_date || job.created_at
          ),
          status: job.is_active ? "Active" : "Expired",
          applications_count: job.applications_count || 0,
          applicants: [],
          is_active: job.is_active,
        }));

        this.stats = {
          openJobs: this.jobs.filter((job) => job.status === "Active").length,
          savedCandidates: userResponse.data.saved_candidates || 0,
        };
      } catch (err) {
        this.error =
          err.response?.data?.message ||
          err.message ||
          "Failed to fetch dashboard data";
        console.error("Error fetching dashboard data:", err);
      } finally {
        this.isLoading = false;
      }
    },

    async fetchJobApplicants(jobId) {
      try {
        const response = await api.get(`/jobs/${jobId}/applications`);

        if (!Array.isArray(response.data)) {
          console.warn("Expected array of applicants but got:", response.data);
          return [];
        }

        return response.data.map((applicant) => ({
          name: applicant.candidate?.name || "Anonymous Candidate",
          position: applicant.candidate?.current_position || "Candidate",
          skills: applicant.candidate?.skills?.map((skill) => skill.name) || [],
        }));
      } catch (err) {
        console.error("Error fetching applicants:", err);
        throw err;
      }
    },

    async toggleJobStatus(jobId) {
      try {
        await api.patch(`/jobs/${jobId}/toggle-active`);

        const job = this.jobs.find((j) => j.id === jobId);
        if (job) {
          job.is_active = !job.is_active;
          job.status = job.is_active ? "Active" : "Expired";
          this.stats.openJobs = this.jobs.filter(
            (j) => j.status === "Active"
          ).length;
        }
      } catch (err) {
        console.error("Error toggling job status:", err);
        throw err;
      }
    },

    async showApplications(jobId) {
      this.setSelectedJob(jobId);

      const job = this.jobs.find((j) => j.id === jobId);
      if (job && (!job.applicants || job.applicants.length === 0)) {
        try {
          this.isLoading = true;
          job.applicants = await this.fetchJobApplicants(jobId);
        } catch (err) {
          this.error = "Failed to load applications";
          console.error("Failed to load applicants:", err);
        } finally {
          this.isLoading = false;
        }
      }

      this.showApplicationsPanel = true;
    },

    closeApplications() {
      this.showApplicationsPanel = false;
    },

    calculateTimeRemaining(expiryDate) {
      if (!expiryDate) return "No expiry date";
      const now = new Date();
      const expiry = new Date(expiryDate);
      const diffTime = expiry - now;
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

      return diffDays <= 0 ? "Expired" : `${diffDays} days remaining`;
    },

    setSelectedJob(jobId) {
      this.selectedJob = jobId;
    },

    clearError() {
      this.error = null;
    },
  },

  getters: {
    recentJobs: (state) => state.jobs,
    selectedJobDetails: (state) => {
      return state.jobs.find((job) => job.id === state.selectedJob) || null;
    },
  },
});
