<template>
  <div class="d-flex">
    <SidebarComponent />
    <div class="container mt-4 flex-grow-1">
      <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
          <h2 class="card-title mb-0">Post a job</h2>
        </div>
        <div class="card-body">
          <form
            @submit.prevent="validateAndSubmit"
            class="needs-validation"
            novalidate
            ref="jobForm"
          >
            <div class="mb-4">
              <label for="jobTitle" class="form-label fw-medium"
                >Job Title <span class="text-danger">*</span></label
              >
              <input
                type="text"
                class="form-control"
                id="jobTitle"
                placeholder="Add job title, role, vacancies etc"
                v-model="jobData.jobTitle"
                required
              />
              <div class="invalid-feedback">Please provide a job title.</div>
            </div>
            <div class="row mb-4">
              <div class="col-md-8">
                <label for="tags" class="form-label fw-medium">Tags</label>
                <input
                  type="text"
                  class="form-control"
                  id="tags"
                  placeholder="Job keyword, tags etc..."
                  v-model="tagInput"
                  @keydown.enter.prevent="addTag"
                />
                <div class="mt-2 d-flex flex-wrap gap-2">
                  <span
                    v-for="(tag, index) in jobData.tags"
                    :key="index"
                    class="badge bg-light text-dark p-2 d-flex align-items-center"
                  >
                    {{ tag }}
                    <button
                      type="button"
                      class="btn-close ms-2"
                      @click="removeTag(index)"
                    ></button>
                  </span>
                </div>
              </div>
              <div class="col-md-4">
                <label for="jobRole" class="form-label fw-medium"
                  >Job Role <span class="text-danger">*</span></label
                >
                <select
                  class="form-select"
                  id="jobRole"
                  v-model="jobData.jobRole"
                  required
                >
                  <option value="" disabled>Select...</option>
                  <option v-for="role in jobRoles" :key="role" :value="role">
                    {{ role }}
                  </option>
                </select>
                <div class="invalid-feedback">Please select a job role.</div>
              </div>
            </div>
            <div class="mb-4">
              <label class="form-label fw-medium"
                >Salary <span class="text-danger">*</span></label
              >
              <div class="row g-3">
                <div class="col-md-4">
                  <div class="input-group has-validation">
                    <span class="input-group-text">Min</span>
                    <input
                      type="number"
                      class="form-control"
                      v-model="jobData.minSalary"
                      required
                    />
                    <span class="input-group-text">USD</span>
                    <div class="invalid-feedback">
                      Please provide a minimum salary.
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group has-validation">
                    <span class="input-group-text">Max</span>
                    <input
                      type="number"
                      class="form-control"
                      v-model="jobData.maxSalary"
                      required
                    />
                    <span class="input-group-text">USD</span>
                    <div class="invalid-feedback">
                      Please provide a maximum salary.
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <select
                    class="form-select"
                    v-model="jobData.salaryType"
                    required
                  >
                    <option value="" disabled>Select...</option>
                    <option
                      v-for="type in salaryTypes"
                      :key="type"
                      :value="type"
                    >
                      {{ type }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    Please select a salary type.
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-4 border rounded p-4">
              <h5 class="mb-3">Advanced Information</h5>
              <div class="row g-3 mb-3">
                <div class="col-md-4">
                  <label class="form-label fw-medium"
                    >Education <span class="text-danger">*</span></label
                  >
                  <select
                    class="form-select"
                    v-model="jobData.education"
                    required
                  >
                    <option value="" disabled>Select...</option>
                    <option
                      v-for="edu in educationLevels"
                      :key="edu"
                      :value="edu"
                    >
                      {{ edu }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    Please select an education level.
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-medium"
                    >Experience <span class="text-danger">*</span></label
                  >
                  <select
                    class="form-select"
                    v-model="jobData.experience"
                    required
                  >
                    <option value="" disabled>Select...</option>
                    <option
                      v-for="exp in experienceLevels"
                      :key="exp"
                      :value="exp"
                    >
                      {{ exp }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    Please select an experience level.
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-medium"
                    >Job Type <span class="text-danger">*</span></label
                  >
                  <select
                    class="form-select"
                    v-model="jobData.jobType"
                    required
                  >
                    <option value="" disabled>Select...</option>
                    <option v-for="type in jobTypes" :key="type" :value="type">
                      {{ type }}
                    </option>
                  </select>
                  <div class="invalid-feedback">Please select a job type.</div>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label fw-medium"
                    >Vacancies <span class="text-danger">*</span></label
                  >
                  <select
                    class="form-select"
                    v-model="jobData.vacancies"
                    required
                  >
                    <option value="" disabled>Select...</option>
                    <option v-for="n in 10" :key="n" :value="n">{{ n }}</option>
                  </select>
                  <div class="invalid-feedback">
                    Please select number of vacancies.
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-medium"
                    >Expiration Date <span class="text-danger">*</span></label
                  >
                  <input
                    type="date"
                    class="form-control"
                    v-model="jobData.expirationDate"
                    required
                  />
                  <div class="invalid-feedback">
                    Please select an expiration date.
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-medium"
                    >Job Level <span class="text-danger">*</span></label
                  >
                  <select
                    class="form-select"
                    v-model="jobData.jobLevel"
                    required
                  >
                    <option value="" disabled>Select...</option>
                    <option
                      v-for="level in jobLevels"
                      :key="level"
                      :value="level"
                    >
                      {{ level }}
                    </option>
                  </select>
                  <div class="invalid-feedback">Please select a job level.</div>
                </div>
              </div>
            </div>
            <div class="mb-4 border rounded p-4 bg-light">
              <h5 class="mb-3">Location</h5>
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-medium"
                    >Country <span class="text-danger">*</span></label
                  >
                  <select
                    class="form-select"
                    v-model="jobData.country"
                    @change="onCountryChange"
                    required
                    :disabled="jobData.isRemote"
                  >
                    <option value="" disabled>Select...</option>
                    <option
                      v-for="country in countries"
                      :key="country"
                      :value="country"
                    >
                      {{ country }}
                    </option>
                  </select>
                  <div class="invalid-feedback">Please select a country.</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-medium"
                    >City <span class="text-danger">*</span></label
                  >
                  <select
                    class="form-select"
                    v-model="jobData.city"
                    :disabled="!jobData.country || jobData.isRemote"
                    required
                  >
                    <option value="" disabled>Select...</option>
                    <option
                      v-for="city in availableCities"
                      :key="city"
                      :value="city"
                    >
                      {{ city }}
                    </option>
                  </select>
                  <div class="invalid-feedback">Please select a city.</div>
                </div>
              </div>
              <div class="form-check mt-2">
                <input
                  class="form-check-input"
                  type="checkbox"
                  v-model="jobData.isRemote"
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
                v-model="jobData.jobDescription"
                placeholder="Add your job description..."
                required
                minlength="50"
              ></textarea>
              <div class="invalid-feedback">
                Please provide a job description (minimum 50 characters).
              </div>
              <div class="form-text">Minimum 50 characters required.</div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <p class="text-danger small mb-0">
                <span class="text-danger fw-bold">*</span> Required fields
              </p>
              <div>
                <button
                  type="button"
                  class="btn btn-outline-secondary px-4 me-2"
                >
                  Save Draft
                </button>
                <button type="submit" class="btn btn-primary px-4">
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
import { ref, reactive, computed, onMounted } from "vue";
import SidebarComponent from "./SidebarComponent.vue";

const jobForm = ref(null);
const formSubmitted = ref(false);

const jobData = reactive({
  jobTitle: "",
  tags: [],
  jobRole: "",
  minSalary: "",
  maxSalary: "",
  salaryType: "",
  education: "",
  experience: "",
  jobType: "",
  vacancies: "",
  expirationDate: "",
  jobLevel: "",
  country: "",
  city: "",
  isRemote: false,
  benefits: [],
  jobDescription: "",
});

const tagInput = ref("");
const addTag = () => {
  if (tagInput.value.trim()) {
    jobData.tags.push(tagInput.value.trim());
    tagInput.value = "";
  }
};

const removeTag = (index) => jobData.tags.splice(index, 1);

const toggleBenefit = (benefit) => {
  const i = jobData.benefits.indexOf(benefit);
  i === -1 ? jobData.benefits.push(benefit) : jobData.benefits.splice(i, 1);
};

const onCountryChange = () => {
  jobData.city = "";
};

const handleRemoteChange = () => {
  if (jobData.isRemote) {
    jobData.country = "";
    jobData.city = "";
  }
};

const validateAndSubmit = () => {
  formSubmitted.value = true;

  if (jobForm.value && !jobForm.value.checkValidity()) {
    jobForm.value.classList.add("was-validated");
    return;
  }

  if (parseFloat(jobData.maxSalary) < parseFloat(jobData.minSalary)) {
    alert("Maximum salary cannot be less than minimum salary!");
    return;
  }

  submitJob();
};

const submitJob = () => {
  console.log("Job Data:", jobData);

  if (jobForm.value) {
    jobForm.value.classList.remove("was-validated");
  }

  alert("Job posted successfully!");
};

onMounted(() => {
  const date = new Date();
  date.setDate(date.getDate() + 30);
  jobData.expirationDate = date.toISOString().split("T")[0];
});

const jobRoles = [
  "Software Developer",
  "Product Manager",
  "UX Designer",
  "Data Scientist",
  "Project Manager",
];
const salaryTypes = ["Per Year", "Per Month", "Per Week", "Per Hour"];
const educationLevels = [
  "High School",
  "Associate Degree",
  "Bachelor's Degree",
  "Master's Degree",
  "PhD",
];
const experienceLevels = [
  "Entry Level (0-1 years)",
  "Junior (1-3 years)",
  "Mid-Level (3-5 years)",
  "Senior (5-8 years)",
  "Expert (8+ years)",
];
const jobTypes = [
  "Full-time",
  "Part-time",
  "Contract",
  "Temporary",
  "Internship",
];
const jobLevels = [
  "Entry",
  "Junior",
  "Mid-Level",
  "Senior",
  "Manager",
  "Director",
  "Executive",
];
const countries = [
  "United States",
  "United Kingdom",
  "Canada",
  "Australia",
  "Germany",
  "France",
  "India",
];
const citiesByCountry = {
  "United States": [
    "New York",
    "San Francisco",
    "Chicago",
    "Los Angeles",
    "Seattle",
  ],
  "United Kingdom": [
    "London",
    "Manchester",
    "Birmingham",
    "Edinburgh",
    "Glasgow",
  ],
  Canada: ["Toronto", "Vancouver", "Montreal", "Calgary", "Ottawa"],
  Australia: ["Sydney", "Melbourne", "Brisbane", "Perth", "Adelaide"],
  Germany: ["Berlin", "Munich", "Hamburg", "Frankfurt", "Cologne"],
  France: ["Paris", "Lyon", "Marseille", "Toulouse", "Nice"],
  India: ["Mumbai", "Delhi", "Bangalore", "Hyderabad", "Chennai"],
};
const availableCities = computed(() => citiesByCountry[jobData.country] || []);
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

.was-validated .form-check-input:valid ~ .form-check-label,
.was-validated .form-check-input:invalid ~ .form-check-label {
  color: inherit;
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

.form-check-input:checked {
  background-color: #2c7be5;
  border-color: #2c7be5;
}

@media (max-width: 768px) {
  .row.g-3 > .col-md-4,
  .row.g-3 > .col-md-6 {
    margin-bottom: 1rem;
  }
}
</style>
