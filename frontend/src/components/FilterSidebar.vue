<template>
  <aside class="filter-sidebar">
    <div class="filter-header">
      <h3>Filter Jobs</h3>
      <button class="close-btn" @click="$emit('close')">×</button>
      <button v-if="activeFilterCount > 0" @click="clearAllFilters" class="clear-all">
        Clear all
      </button>
    </div>

    <div class="active-filters" v-if="activeFilterCount > 0">
      <h4>Active Filters:</h4>
      <div class="filter-chips">
        <div v-if="localFilters.search" class="filter-chip">
          Search: "{{ localFilters.search }}"
          <button @click="removeFilter('search')">×</button>
        </div>
        <div v-if="localFilters.work_type" class="filter-chip">
          {{ formatWorkType(localFilters.work_type) }}
          <button @click="removeFilter('work_type')">×</button>
        </div>
        <div v-if="localFilters.min_salary || localFilters.max_salary" class="filter-chip">
          Salary: ${{ localFilters.min_salary || '0' }} - ${{ localFilters.max_salary || '∞' }}
          <button @click="removeSalaryFilter()">×</button>
        </div>
        <div v-if="localFilters.remote" class="filter-chip">
          Remote Only
          <button @click="removeFilter('remote')">×</button>
        </div>
      </div>
    </div>

    <div class="filter-group">
      <label>Search Keywords</label>
      <input 
        type="text" 
        v-model="localFilters.search" 
        placeholder="Job title, company, or keywords"
      >
    </div>

    <div class="filter-group">
      <label>Job Type</label>
      <div class="checkbox-group">
        <label v-for="type in jobTypes" :key="type.value">
          <input 
            type="radio" 
            v-model="localFilters.work_type" 
            :value="type.value"
            name="work_type"
          >
          {{ type.label }}
        </label>
      </div>
    </div>

    <div class="filter-group">
      <label>Salary Range ($/year)</label>
      <div class="salary-range">
        <div class="salary-inputs">
          <input 
            type="number" 
            v-model="localFilters.min_salary" 
            placeholder="Min"
            min="0"
          >
          <span>to</span>
          <input 
            type="number" 
            v-model="localFilters.max_salary" 
            placeholder="Max"
            min="0"
          >
        </div>
      </div>
    </div>

    <div class="filter-group">
      <label class="remote-filter">
        <input type="checkbox" v-model="localFilters.remote">
        Remote Only
      </label>
    </div>

    <button class="apply-btn" @click="applyFilters">
      Apply Filters
    </button>
  </aside>
</template>

<script>
export default {
  props: {
    filters: {
      type: Object,
      required: true,
      default: () => ({
        search: '',
        work_type: '',
        min_salary: null,
        max_salary: null,
        remote: false
      })
    }
  },
  data() {
    return {
      localFilters: { ...this.filters },
      jobTypes: [
        { value: 'onsite', label: 'onsite' },
        { value: 'hyprid', label: 'hyprid' },
        { value: 'remote', label: 'remote' },

      ]
    }
  },
  computed: {
    activeFilterCount() {
      return Object.values(this.localFilters).filter(val => 
        val !== '' && val !== null && val !== undefined && val !== false
      ).length;
    }
  },
  methods: {
    formatWorkType(type) {
      const typeMap = {
        'onsite': 'onsite',
        'hyprid': 'hyprid',
        'remote': 'remote'
      };
      return typeMap[type] || 'Any Type';
    },
    applyFilters() {
      const filtersToEmit = {
        ...this.localFilters,
        min_salary: this.localFilters.min_salary ? Number(this.localFilters.min_salary) : null,
        max_salary: this.localFilters.max_salary ? Number(this.localFilters.max_salary) : null
      };
      this.$emit('update:filters', filtersToEmit);
      this.$emit('close');
    },
    clearAllFilters() {
      this.localFilters = {
        search: '',
        work_type: '',
        min_salary: null,
        max_salary: null,
        remote: false
      };
      this.$emit('update:filters', this.localFilters);
    },
    removeFilter(filterKey) {
      if (filterKey === 'remote') {
        this.localFilters[filterKey] = false;
      } else {
        this.localFilters[filterKey] = '';
      }
      this.$emit('update:filters', this.localFilters);
    },
    removeSalaryFilter() {
      this.localFilters.min_salary = null;
      this.localFilters.max_salary = null;
      this.$emit('update:filters', this.localFilters);
    }
  },
  watch: {
    filters: {
      handler(newFilters) {
        this.localFilters = { ...newFilters };
      },
      deep: true
    }
  }
}
</script>

<style scoped>
.filter-sidebar {
  position: fixed;
  top: 0;
  right: 0;
  width: 320px;
  height: 100vh;
  padding: 1.5rem;
  background: #fff;
  box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  overflow-y: auto;
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

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #666;
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
.filter-group input[type="number"] {
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

@media (max-width: 768px) {
  .filter-sidebar {
    width: 100%;
    max-width: 320px;
  }
}
</style>