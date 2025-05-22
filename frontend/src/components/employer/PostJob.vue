<template>
  <div class="post-job-container">
    <SidebarComponent
      :initialActive="2"
      @navigate="handleNavigation"
      @logout="handleLogout"
    />

    <div class="main-content">
      <div class="content-wrapper">
        <div v-if="isLoading" class="loading-overlay">
          <div class="loading-spinner"></div>
          <p>Loading...</p>
        </div>

        <div v-if="error" class="error-alert">
          <i class="bi bi-exclamation-circle"></i>
          <p>{{ error }}</p>
          <button @click="fetchCategories">Retry</button>
        </div>

        <div v-if="!isLoading && !error" class="form-container">
          <div class="page-header">
            <h1>Post a New Job</h1>
            <p>Fill in the details to create a new job listing</p>
          </div>

          <form @submit.prevent="submitJob" class="job-form">
            <div class="form-group">
              <label for="category_id">Category *</label>
              <select
                id="category_id"
                v-model="form.category_id"
                required
                :disabled="categories.length === 0"
              >
                <option value="">Select a category</option>
                <option
                  v-for="category in categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
              <span v-if="errors.category_id" class="error-text">{{
                errors.category_id
              }}</span>
            </div>

            <div class="form-group">
              <label for="title">Job Title *</label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                maxlength="255"
                required
                placeholder="e.g., Senior Software Engineer"
              />
              <span v-if="errors.title" class="error-text">{{
                errors.title
              }}</span>
            </div>

            <div class="form-group">
              <label for="description">Description *</label>
              <textarea
                id="description"
                v-model="form.description"
                required
                placeholder="Describe the job role"
              ></textarea>
              <span v-if="errors.description" class="error-text">{{
                errors.description
              }}</span>
            </div>

            <div class="form-group">
              <label for="responsibilities">Responsibilities *</label>
              <textarea
                id="responsibilities"
                v-model="form.responsibilities"
                required
                placeholder="List key responsibilities"
              ></textarea>
              <span v-if="errors.responsibilities" class="error-text">{{
                errors.responsibilities
              }}</span>
            </div>

            <div class="form-group">
              <label for="requirements">Requirements *</label>
              <textarea
                id="requirements"
                v-model="form.requirements"
                required
                placeholder="List required skills and qualifications"
              ></textarea>
              <span v-if="errors.requirements" class="error-text">{{
                errors.requirements
              }}</span>
            </div>

            <div class="form-group">
              <label for="benefits">Benefits</label>
              <textarea
                id="benefits"
                v-model="form.benefits"
                placeholder="e.g., Health insurance, remote work"
              ></textarea>
              <span v-if="errors.benefits" class="error-text">{{
                errors.benefits
              }}</span>
            </div>

            <div class="form-group">
              <label for="work_type">Work Type *</label>
              <select id="work_type" v-model="form.work_type" required>
                <option value="">Select work type</option>
                <option value="remote">Remote</option>
                <option value="onsite">Onsite</option>
                <option value="hybrid">Hybrid</option>
              </select>
              <span v-if="errors.work_type" class="error-text">{{
                errors.work_type
              }}</span>
            </div>

            <div class="form-group">
              <label for="location">Location *</label>
              <input
                id="location"
                v-model="form.location"
                type="text"
                required
                placeholder="e.g., New York, NY"
              />
              <span v-if="errors.location" class="error-text">{{
                errors.location
              }}</span>
            </div>

            <div class="form-group">
              <label for="min_salary">Minimum Salary</label>
              <input
                id="min_salary"
                v-model.number="form.min_salary"
                type="number"
                min="0"
                placeholder="e.g., 50000"
              />
              <span v-if="errors.min_salary" class="error-text">{{
                errors.min_salary
              }}</span>
            </div>

            <div class="form-group">
              <label for="max_salary">Maximum Salary</label>
              <input
                id="max_salary"
                v-model.number="form.max_salary"
                type="number"
                min="0"
                placeholder="e.g., 70000"
              />
              <span v-if="errors.max_salary" class="error-text">{{
                errors.max_salary
              }}</span>
            </div>

            <div class="form-group">
              <label for="deadline">Application Deadline *</label>
              <input
                id="deadline"
                v-model="form.deadline"
                type="date"
                required
              />
              <span v-if="errors.deadline" class="error-text">{{
                errors.deadline
              }}</span>
            </div>

            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" v-model="form.status">
                <option value="">Select status</option>
                <option value="draft">Draft</option>
                <option value="pending">Pending</option>
                <option value="published">Published</option>
                <option value="expired">Expired</option>
              </select>
              <span v-if="errors.status" class="error-text">{{
                errors.status
              }}</span>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-submit" :disabled="isSubmitting">
                {{ isSubmitting ? "Posting..." : "Post Job" }}
              </button>
              <button
                type="button"
                class="btn-cancel"
                @click="$router.push('/employer/jobs')"
              >
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import SidebarComponent from "./SidebarComponent.vue";
import axios from "axios";

// Configure axios with auth
const axiosInstance = axios.create({
  baseURL: "/api",
});
axiosInstance.interceptors.request.use((config) => {
  const token = localStorage.getItem("auth_token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Auth management
const token = ref(localStorage.getItem("auth_token") || null);
const setToken = (newToken) => {
  token.value = newToken;
  localStorage.setItem("auth_token", newToken);
};
const clearToken = () => {
  token.value = null;
  localStorage.removeItem("auth_token");
};

const router = useRouter();

const isLoading = ref(false);
const error = ref(null);
const isSubmitting = ref(false);
const categories = ref([]);

// Reactive form data based on backend validation rules
const form = ref({
  category_id: "",
  title: "",
  description: "",
  responsibilities: "",
  requirements: "",
  benefits: null,
  work_type: "",
  location: "",
  min_salary: null,
  max_salary: null,
  deadline: "",
  status: "",
});

// Validation errors
const errors = ref({});

const fetchCategories = async () => {
  console.log("Fetching categories...");
  isLoading.value = true;
  error.value = null;

  try {
    const response = await axiosInstance.get("/categories");
    console.log("Categories response:", response.data);
    categories.value = response.data.data || [];
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to load categories";
    console.error("Fetch categories error:", err);
    if (err.response) {
      console.error("Response status:", err.response.status);
      console.error("Response data:", err.response.data);
    }
    if (err.response?.status === 401) {
      clearToken();
      router.push("/login");
    }
  } finally {
    isLoading.value = false;
  }
};

const validateForm = () => {
  errors.value = {};

  if (!form.value.category_id) {
    errors.value.category_id = "Category is required";
  }
  if (!form.value.title) {
    errors.value.title = "Job title is required";
  } else if (form.value.title.length > 255) {
    errors.value.title = "Job title must not exceed 255 characters";
  }
  if (!form.value.description) {
    errors.value.description = "Description is required";
  }
  if (!form.value.responsibilities) {
    errors.value.responsibilities = "Responsibilities are required";
  }
  if (!form.value.requirements) {
    errors.value.requirements = "Requirements are required";
  }
  if (!form.value.work_type) {
    errors.value.work_type = "Work type is required";
  } else if (!["remote", "onsite", "hybrid"].includes(form.value.work_type)) {
    errors.value.work_type = "Invalid work type";
  }
  if (!form.value.location) {
    errors.value.location = "Location is required";
  }
  if (
    form.value.min_salary !== null &&
    (isNaN(form.value.min_salary) || form.value.min_salary < 0)
  ) {
    errors.value.min_salary = "Minimum salary must be a valid number";
  }
  if (
    form.value.max_salary !== null &&
    (isNaN(form.value.max_salary) || form.value.max_salary < 0)
  ) {
    errors.value.max_salary = "Maximum salary must be a valid number";
  }
  if (form.value.min_salary !== null && form.value.max_salary !== null) {
    if (form.value.max_salary < form.value.min_salary) {
      errors.value.max_salary =
        "Maximum salary must be greater than or equal to minimum salary";
    }
  }
  if (!form.value.deadline) {
    errors.value.deadline = "Deadline is required";
  } else {
    const deadlineDate = new Date(form.value.deadline);
    if (isNaN(deadlineDate.getTime())) {
      errors.value.deadline = "Invalid date format";
    }
  }
  if (
    form.value.status &&
    !["draft", "pending", "published", "expired"].includes(form.value.status)
  ) {
    errors.value.status = "Invalid status";
  }

  return Object.keys(errors.value).length === 0;
};

const submitJob = async () => {
  console.log("Submitting job:", form.value);
  if (!validateForm()) {
    console.error("Validation failed:", errors.value);
    return;
  }

  isSubmitting.value = true;
  error.value = null;

  try {
    const response = await axiosInstance.post("/addjobs", {
      category_id: form.value.category_id,
      title: form.value.title,
      description: form.value.description,
      responsibilities: form.value.responsibilities,
      requirements: form.value.requirements,
      benefits: form.value.benefits || null,
      work_type: form.value.work_type,
      location: form.value.location,
      min_salary: form.value.min_salary || null,
      max_salary: form.value.max_salary || null,
      deadline: form.value.deadline,
      status: form.value.status || null,
    });
    console.log("Submit job response:", response.data);
    router.push("/employer/jobs");
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to post job";
    console.error("Submit job error:", err);
    if (err.response) {
      console.error("Response status:", err.response.status);
      console.error("Response data:", err.response.data);
      if (err.response.data.errors) {
        errors.value = err.response.data.errors;
      }
    }
    if (err.response?.status === 401) {
      clearToken();
      router.push("/login");
    }
  } finally {
    isSubmitting.value = false;
  }
};

const handleNavigation = (index) => {
  console.log("Handling navigation with index:", index);
  const routes = [
    "/employer/dashboard",
    "/employer/jobs",
    "/employer/post-job",
  ];

  if (index < 0 || index >= routes.length) {
    console.error("Invalid navigation index:", index);
    error.value = "Invalid navigation option selected";
    return;
  }

  const targetRoute = routes[index];
  console.log("Navigating to:", targetRoute);

  try {
    router.push(targetRoute).catch((err) => {
      console.error("Router push error:", err);
      error.value = `Failed to navigate to ${targetRoute}`;
    });
  } catch (err) {
    console.error("Navigation error:", err);
    error.value = "Navigation failed";
  }
};

const handleLogout = () => {
  console.log("Logging out");
  clearToken();
  router.push("/login").catch((err) => {
    console.error("Logout navigation error:", err);
    error.value = "Failed to navigate to login";
  });
};

onMounted(() => {
  console.log("PostJob mounted");
  console.log("Router available:", !!router);
  console.log("Auth token:", token.value);
  if (!token.value) {
    console.warn("No auth token, redirecting to login");
    router.push("/login");
  } else {
    fetchCategories();
  }
});
</script>

<style scoped>
.post-job-container {
  display: flex;
  height: 100vh;
  background-color: #f8f9fa;
}

.main-content {
  flex: 1;
  overflow: auto;
  position: relative;
}

.content-wrapper {
  padding: 1.5rem;
  position: relative;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 1.25rem;
  font-weight: 500;
  margin-bottom: 0.25rem;
}

.page-header p {
  color: #6c757d;
  font-size: 0.875rem;
  margin: 0;
}

.form-container {
  background-color: #fff;
  border-radius: 0.5rem;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  padding: 1.5rem;
}

.job-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #495057;
  margin-bottom: 0.25rem;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 0.5rem;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  font-size: 0.875rem;
  color: #495057;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #2c7be5;
  box-shadow: 0 0 0 0.2rem rgba(44, 123, 229, 0.25);
}

.form-group textarea {
  min-height: 100px;
  resize: vertical;
}

.form-group input[type="date"] {
  width: 200px;
}

.error-text {
  color: #dc3545;
  font-size: 0.75rem;
  margin-top: 0.25rem;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.btn-submit {
  background-color: #2c7be5;
  color: white;
  border: none;
  border-radius: 0.25rem;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  cursor: pointer;
}

.btn-submit:hover:not(:disabled) {
  background-color: #1a68d1;
}

.btn-submit:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}

.btn-cancel {
  background-color: #fff;
  color: #6c757d;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  cursor: pointer;
}

.btn-cancel:hover {
  background-color: #f8f9fa;
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(255, 255, 255, 0.8);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.loading-spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #2c7be5;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.error-alert {
  background-color: #f8d7da;
  color: #721c24;
  padding: 1rem;
  border-radius: 0.25rem;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.error-alert i {
  font-size: 1.5rem;
}

.error-alert button {
  margin-left: auto;
  background-color: #dc3545;
  color: white;
  border: none;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  cursor: pointer;
}
</style>
