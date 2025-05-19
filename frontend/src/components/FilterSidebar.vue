<template>
  <aside class="filter-sidebar">
    <div class="filter-header">
      <h3>Filter</h3>
      <button v-if="activeFilterCount > 0" @click="clearAllFilters" class="clear-all">
        Clear all
      </button>
    </div>

    <!-- Active Filters -->
    <div class="active-filters" v-if="activeFilterCount > 0">
      <h4>Active Filters:</h4>
      <div class="filter-chips">
        <div v-if="filters.search" class="filter-chip">
          Search: {{ filters.search }}
          <button @click="removeFilter('search')">×</button>
        </div>
        <div v-if="filters.category" class="filter-chip">
          {{ filters.category }}
          <button @click="removeFilter('category')">×</button>
        </div>
        <div v-if="filters.jobType" class="filter-chip">
          {{ filters.jobType }}
          <button @click="removeFilter('jobType')">×</button>
        </div>
        <div v-if="filters.minSalary || filters.maxSalary" class="filter-chip">
          Salary ${{ filters.minSalary }}-${{ filters.maxSalary }}
          <button @click="removeSalaryFilter()">×</button>
        </div>
      </div>
    </div>

    <!-- Search Filter -->
    <div class="filter-group">
      <label>Search</label>
      <input 
        type="text" 
        v-model="localFilters.search" 
        placeholder="UI/UX, Prague, Czech" 
        @input="debounceFilter"
      >
    </div>

    <!-- Industry/Category Filter -->
    <div class="filter-group">
      <label>Industry</label>
      <select v-model="localFilters.category">
        <option value="">All Category</option>
        <option v-for="category in categories" :value="category">
          {{ category }}
        </option>
      </select>
    </div>

    <!-- Job Type Filter -->
    <div class="filter-group">
      <label>Job Type</label>
      <div class="checkbox-group">
        <label v-for="type in jobTypes">
          <input 
            type="radio" 
            v-model="localFilters.jobType" 
            :value="type.value"
          >
          {{ type.label }}
        </label>
      </div>
    </div>

    <!-- Salary Range Filter -->
    <div class="filter-group">
      <label>Salary (yearly)</label>
      <div class="salary-range">
        <div class="salary-inputs">
          <input 
            type="number" 
            v-model="localFilters.minSalary" 
            placeholder="Min"
          >
          <span>-</span>
          <input 
            type="number" 
            v-model="localFilters.maxSalary" 
            placeholder="Max"
          >
        </div>
        <div class="preset-ranges">
          <button 
            v-for="range in salaryRanges" 
            @click="setSalaryRange(range.min, range.max)"
            :class="{ active: isSalaryRangeActive(range) }"
          >
            {{ range.label }}
          </button>
        </div>
      </div>
    </div>

    <!-- Remote Job Filter -->
    <div class="filter-group">
      <label class="remote-filter">
        <input type="checkbox" v-model="localFilters.remoteOnly">
        Remote Job
      </label>
    </div>

    <button class="apply-btn" @click="applyFilters">Apply Filter</button>
  </aside>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
// import { debounce } from 'lodash-es'

const props = defineProps({
  filters: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['update:filters'])

// Local copy of filters to prevent immediate updates
const localFilters = ref({ ...props.filters })

// Filter options
const categories = ref([
  'Developments',
  'Business',
  'Finance & Accounting',
  'IT & Software',
  'Office Productivity',
  'Personal Development',
  'Design',
  'Marketing',
  'Photography & Video'
])

const jobTypes = ref([
  { value: 'Full Time', label: 'Full Time' },
  { value: 'Part-Time', label: 'Part-Time' },
  { value: 'Internship', label: 'Internship' },
  { value: 'Temporary', label: 'Temporary' },
  { value: 'Contract Base', label: 'Contract Base' }
])

const salaryRanges = ref([
  { min: 10, max: 100, label: '$10-$100' },
  { min: 100, max: 1000, label: '$100-$1,000' },
  { min: 1000, max: 10000, label: '$1,000-$10,000' },
  { min: 10000, max: 100000, label: '$10,000-$100,000' },
  { min: 100000, max: null, label: '$100,000 Up' }
])

// Computed properties
const activeFilterCount = computed(() => {
  return Object.values(localFilters.value).filter(val => 
    val !== '' && val !== null && val !== undefined
  ).length
})

// Methods
const debounceFilter = debounce(() => {
  emit('update:filters', localFilters.value)
}, 500)

const applyFilters = () => {
  emit('update:filters', localFilters.value)
}

const clearAllFilters = () => {
  localFilters.value = {
    search: '',
    category: '',
    jobType: '',
    minSalary: null,
    maxSalary: null,
    remoteOnly: false
  }
  emit('update:filters', localFilters.value)
}

const removeFilter = (filterKey) => {
  localFilters.value[filterKey] = ''
  emit('update:filters', localFilters.value)
}

const removeSalaryFilter = () => {
  localFilters.value.minSalary = null
  localFilters.value.maxSalary = null
  emit('update:filters', localFilters.value)
}

const setSalaryRange = (min, max) => {
  localFilters.value.minSalary = min
  localFilters.value.maxSalary = max
}

const isSalaryRangeActive = (range) => {
  return localFilters.value.minSalary === range.min && 
         localFilters.value.maxSalary === range.max
}

// Watch for external filter changes
watch(() => props.filters, (newVal) => {
  localFilters.value = { ...newVal }
}, { deep: true })
</script>

<style scoped>
.filter-sidebar {
  width: 280px;
  padding: 1.5rem;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.filter-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.filter-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
}

.clear-all {
  background: none;
  border: none;
  color: #3b82f6;
  cursor: pointer;
  font-size: 0.875rem;
}

.active-filters {
  margin-bottom: 1.5rem;
}

.active-filters h4 {
  margin: 0 0 0.5rem 0;
  font-size: 0.875rem;
  color: #64748b;
}

.filter-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.filter-chip {
  display: flex;
  align-items: center;
  padding: 0.25rem 0.5rem;
  background: #e2e8f0;
  border-radius: 9999px;
  font-size: 0.75rem;
}

.filter-chip button {
  margin-left: 0.25rem;
  background: none;
  border: none;
  cursor: pointer;
  color: #64748b;
}

.filter-group {
  margin-bottom: 1.5rem;
}

.filter-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
}

.filter-group input[type="text"],
.filter-group input[type="number"],
.filter-group select {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
}

.checkbox-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.checkbox-group label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: normal;
  cursor: pointer;
}

.salary-range {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.salary-inputs {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.salary-inputs input {
  flex: 1;
}

.preset-ranges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.preset-ranges button {
  padding: 0.25rem 0.5rem;
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  font-size: 0.75rem;
  cursor: pointer;
}

.preset-ranges button.active {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
}

.remote-filter {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.apply-btn {
  width: 100%;
  padding: 0.75rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}

.apply-btn:hover {
  background: #2563eb;
}
</style>