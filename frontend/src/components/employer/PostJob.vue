<template>
  <div class="d-flex">
    <SidebarComponent />
    <div class="container mt-4 flex-grow-1">
      <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
          <h2 class="card-title mb-0">Post a Job</h2>
        </div>
        <div class="card-body">
          <div v-if="isLoading" class="loading-overlay">
            <div class="loading-spinner"></div>
            <p>{{ loadingMessage }}</p>
          </div>
          <div v-if="error" class="error-alert">
            <i class="bi bi-exclamation-circle"></i>
            <p>{{ error }}</p>
            <button @click="retryFetchCategories">Retry</button>
          </div>
          <form
            v-if="!isLoading && !error"
            @submit.prevent="validateAndSubmit"
            class="needs-validation"
            novalidate
            ref="jobForm"
          >
            <div class="mb-4">
              <label for="category" class="form-label fw-medium">
                Category <span class="text-danger">*</span>
              </label>
              <select
                class="form-select"
                id="category"
                v-model="jobData.category_id"
                required
                :disabled="categories.length === 0"
              >
                <option value="" disabled>Select...</option>
                <option
                  v-for="category in categories"
                  :key="category.id"
                  :value="category.name"
                >
                  {{ category.name }}
                </option>
              </select>
              <div class="invalid-feedback">Please select a category.</div>
              <div
                v-if="categories.length === 0"
                class="form-text text-warning"
              >
                No categories available. Contact support to add categories.
              </div>
            </div>
            <div class="mb-4">
              <label for="jobTitle" class="form-label fw-medium">
                Job Title <span class="text-danger">*</span>
              </label>
              <input
                type="text"
                class="form-control"
                id="jobTitle"
                placeholder="Add job title, role, vacancies etc"
                v-model="jobData.title"
                required
              />
              <div class="invalid-feedback">Please provide a job title.</div>
            </div>
            <div class="mb-4">
              <label class="form-label fw-medium">
                Salary <span class="text-danger">*</span>
              </label>
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="input-group has-validation">
                    <span class="input-group-text">Min</span>
                    <input
                      type="number"
                      class="form-control"
                      v-model="jobData.min_salary"
                      required
                      min="0"
                    />
                    <span class="input-group-text">USD</span>
                    <div class="invalid-feedback">
                      Please provide a minimum salary.
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group has-validation">
                    <span class="input-group-text">Max</span>
                    <input
                      type="number"
                      class="form-control"
                      v-model="jobData.max_salary"
                      required
                      min="0"
                    />
                    <span class="input-group-text">USD</span>
                    <div class="invalid-feedback">
                      Please provide a maximum salary.
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-4 border rounded p-4">
              <h5 class="mb-3">Job Type</h5>
              <div class="row g-3">
                <div class="col-md-12">
                  <select
                    class="form-select"
                    v-model="jobData.work_type"
                    required
                  >
                    <option value="" disabled>Select...</option>
                    <option v-for="type in jobTypes" :key="type" :value="type">
                      {{ type.charAt(0).toUpperCase() + type.slice(1) }}
                    </option>
                  </select>
                  <div class="invalid-feedback">Please select a job type.</div>
                </div>
              </div>
            </div>
            <div class="mb-4 border rounded p-4 bg-light">
              <h5 class="mb-3">Location</h5>
              <div class="mb-3">
                <label class="form-label fw-medium">
                  Location <span class="text-danger">*</span>
                </label>
                <input
                  type="text"
                  class="form-control"
                  v-model="jobData.location"
                  :disabled="jobData.is_remote"
                  required
                />
                <div class="invalid-feedback">Please enter a location.</div>
              </div>
              <div class="form-check mt-2">
                <input
                  class="form-check-input"
                  type="checkbox"
                  v-model="jobData.is_remote"
                  id="remotePosition"
                  @change="handleRemoteChange"
                />
                <label class="form-check-label" for="remotePosition">
                  Fully Remote Position - Worldwide
                </label>
              </div>
            </div>
            <div class="mb-4 border rounded p-4">
              <h5 class="mb-3">Job Benefits</h5>
              <div class="d-flex flex-wrap gap-2 mb-3">
                <button
                  v-for="benefit in availableBenefits"
                  :key="benefit"
                  type="button"
                  class="btn btn-sm"
                  :class="
                    jobData.benefits.includes(benefit)
                      ? 'btn-primary'
                      : 'btn-outline-secondary'
                  "
                  @click="toggleBenefit(benefit)"
                >
                  {{ benefit }}
                </button>
              </div>
            </div>
            <div class="mb-4">
              <h5 class="mb-2">
                Job Description <span class="text-danger">*</span>
              </h5>
              <textarea
                class="form-control"
                rows="6"
                v-model="jobData.description"
                placeholder="Add your job description..."
                required
                minlength="50"
              ></textarea>
              <div class="invalid-feedback">
                Please provide a job description (minimum 50 characters).
              </div>
              <div class="form-text">Minimum 50 characters required.</div>
            </div>
            <div class="mb-4">
              <h5 class="mb-2">
                Requirements <span class="text-danger">*</span>
              </h5>
              <textarea
                class="form-control"
                rows="4"
                v-model="jobData.requirements"
                placeholder="Add job requirements..."
                required
                minlength="20"
              ></textarea>
              <div class="invalid-feedback">
                Please provide job requirements (minimum 20 characters).
              </div>
            </div>
            <div class="mb-4">
              <h5 class="mb-2">
                Responsibilities <span class="text-danger">*</span>
              </h5>
              <textarea
                class="form-control"
                rows="4"
                v-model="jobData.responsibilities"
                placeholder="Add job responsibilities..."
                required
                minlength="20"
              ></textarea>
              <div class="invalid-feedback">
                Please provide job responsibilities (minimum 20 characters).
              </div>
            </div>
            <div class="mb-4">
              <label for="deadline" class="form-label fw-medium">
                Expiration Date <span class="text-danger">*</span>
              </label>
              <input
                type="date"
                class="form-control"
                id="deadline"
                v-model="jobData.deadline"
                required
              />
              <div class="invalid-feedback">
                Please select an expiration date.
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <p class="text-danger small mb-0">
                <span class="text-danger fw-bold">*</span> Required fields
              </p>
              <div>
                <button
                  type="button"
                  class="btn btn-outline-secondary px-4 me-2"
                  @click="saveAsDraft"
                  :disabled="isLoading"
                >
                  Save Draft
                </button>
                <button
                  type="submit"
                  class="btn btn-primary px-4"
                  :disabled="isLoading || categories.length === 0"
                >
                  Post Job
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import SidebarComponent from "./SidebarComponent.vue";
import { useAuthStore } from "../../services/authStore";

const router = useRouter();
const authStore = useAuthStore();
const jobForm = ref(null);
const isLoading = ref(false);
const loadingMessage = ref("Loading categories...");
const error = ref(null);
const categories = ref([]);

const jobData = reactive({
  category_id: "",
  title: "",
  description: "",
  requirements: "",
  responsibilities: "",
  benefits: [],
  work_type: "",
  location: "",
  min_salary: "",
  max_salary: "",
  deadline: "",
  is_remote: false,
  status: "published",
});

const toggleBenefit = (benefit) => {
  const i = jobData.benefits.indexOf(benefit);
  i === -1 ? jobData.benefits.push(benefit) : jobData.benefits.splice(i, 1);
};

const handleRemoteChange = () => {
  if (jobData.is_remote) {
    jobData.location = "Remote";
  } else {
    jobData.location = "";
  }
};

const validateAndSubmit = async () => {
  if (jobForm.value && !jobForm.value.checkValidity()) {
    jobForm.value.classList.add("was-validated");
    return;
  }

  if (parseFloat(jobData.max_salary) < parseFloat(jobData.min_salary)) {
    error.value = "Maximum salary cannot be less than minimum salary.";
    return;
  }

  await submitJob();
};

const saveAsDraft = async () => {
  jobData.status = "draft";
  await submitJob();
};

const submitJob = async () => {
  try {
    isLoading.value = true;
    loadingMessage.value = "Submitting job...";
    error.value = null;

    const payload = {
      category_id: jobData.category_id,
      title: jobData.title,
      description: jobData.description,
      responsibilities: jobData.responsibilities,
      requirements: jobData.requirements,
      benefits: jobData.benefits.join(", "),
      work_type: jobData.work_type,
      location: jobData.is_remote ? "Remote" : jobData.location,
      min_salary: jobData.min_salary,
      max_salary: jobData.max_salary,
      deadline: jobData.deadline,
      status: jobData.status,
    };

    const response = await axios.post(
      "http://localhost:8000/api/addjobs",
      payload,
      {
        headers: { Authorization: `Bearer ${authStore.token}` },
      }
    );

    if (response.status === 201) {
      alert("Job posted successfully!");
      router.push("/employer/jobs");
    }
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to post job";
    console.error("Job submission error:", err);
  } finally {
    isLoading.value = false;
    loadingMessage.value = "Loading categories...";
  }
};

const fetchCategories = async () => {
  try {
    isLoading.value = true;
    error.value = null;
    console.log("Fetching categories with token:", authStore.token);

    const response = await axios.get("http://localhost:8000/api/categories", {
      headers: { Authorization: `Bearer ${authStore.token}` },
    });

    console.log("Categories response:", response.data);
    categories.value = response.data || [];
    if (categories.value.length === 0) {
      error.value = "No categories found. Please contact support.";
    }
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to load categories";
    console.error("Category fetch error:", err);
    if (err.response?.status === 401) {
      error.value = "Authentication failed. Please log in again.";
      authStore.logout();
      router.push("/login");
    }
  } finally {
    isLoading.value = false;
  }
};

const retryFetchCategories = async () => {
  await fetchCategories();
};

onMounted(async () => {
  if (!authStore.user || !authStore.token) {
    try {
      await authStore.fetchUser();
    } catch (err) {
      router.push("/login");
      return;
    }
  }
  const date = new Date();
  date.setDate(date.getDate() + 30);
  jobData.deadline = date.toISOString().split("T")[0];
  await fetchCategories();
});

const jobTypes = ["remote", "onsite", "hybrid"];
const availableBenefits = [
  "401k Salary",
  "Distributed Team",
  "Async",
  "Vision Insurance",
  "Dental Insurance",
  "Medical Insurance",
  "Unlimited vacation",
  "4 day work/week",
  "401k matching",
  "Company retreats",
  "Learning budget",
  "Free gym membership",
  "Pay in crypto",
  "Profit Sharing",
  "Equity Compensation",
  "No whiteboard interview",
  "No politics at work",
  "We hire old (and young)",
];
</script>

<style scoped>
.card {
  border: none;
  border-radius: 8px;
  overflow: hidden;
}

.card-header {
  border-bottom: 1px solid #e9ecef;
}

.card-title {
  font-weight: 600;
  color: #344767;
}

.form-label {
  color: #344767;
  font-size: 0.875rem;
}

.form-control:focus,
.form-select:focus {
  border-color: #2c7be5;
  box-shadow: 0 0 0 0.25rem rgba(44, 123, 229, 0.1);
}

.btn-primary {
  background-color: #2c7be5;
  border-color: #2c7be5;
}

.btn-primary:hover {
  background-color: #1a68d1;
  border-color: #1a68d1;
}

.btn-outline-secondary {
  color: #5c6a82;
  border-color: #5c6a82;
}

.btn-outline-secondary:hover {
  background-color: #5c6a82;
  border-color: #5c6a82;
  color: white;
}

.badge {
  border-radius: 4px;
  font-weight: 500;
}

.form-control.is-invalid,
.form-select.is-invalid {
  border-color: #dc3545;
  padding-right: calc(1.5em + 0.75rem);
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right calc(0.375em + 0.1875rem) center;
  background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.was-validated .form-check-input:invalid {
  border-color: #dc3545;
}

.was-validated .form-check-input:valid {
  border-color: #198754;
  background-color: #198754;
}

.was-validated .form-control:valid,
.was-validated .form-select:valid {
  border-color: #198754;
  padding-right: calc(1.5em + 0.75rem);
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right calc(0.375em + 0.1875rem) center;
  background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.input-group .form-control.is-invalid,
.input-group .form-select.is-invalid {
  z-index: 1;
}

.input-group .form-control.is-invalid:focus,
.input-group .form-select.is-invalid:focus {
  z-index: 3;
}

textarea.form-control {
  resize: vertical;
}

.form-text {
  font-size: 0.75rem;
  color: #6c757d;
}

.form-text.text-warning {
  color: #ffc107;
}

.form-check-input:checked {
  background-color: #2c7be5;
  border-color: #2c7be5;
}

.loading-overlay {
  position: absolute;
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

@media (max-width: 768px) {
  .row.g-3 > .col-md-6,
  .row.g-3 > .col-md-12 {
    margin-bottom: 1rem;
  }
}
</style>
