<template>
  <div class="container-fluid vacancies-container">
    <div class="row">
      <div class="col-12">
        <h2 class="vacancies-title">Most Popular Vacancies</h2>
        <div class="vacancies-grid">
          <div class="vacancy-card" v-for="vacancy in vacancies" :key="vacancy.title">
            <h3 class="vacancy-title">{{ vacancy.title }}</h3>
            <p class="vacancy-positions">{{ vacancy.positions }} Open Positions</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Hardcoded data based on the image
const vacancies = ref([
  { title: 'Anesthesiologists', positions: '43,484' },
  { title: 'Maxillofacial Surgeons', positions: '74,875' },
  { title: 'Surgeons', positions: '50,384' },
  { title: 'Software Developer', positions: '43,530' },
  { title: 'Psychiatrists', positions: '18,500' },
  { title: 'Obstetricians-Gynecologists', positions: '4,339' },
  { title: 'Orthodontists', positions: '20,079' },
  { title: 'Data Scientist', positions: '28,200' },
  { title: 'Financial Manager', positions: '61,297' },
  { title: 'Management Analyst', positions: '92,048' },
  { title: 'IT Manager', positions: '50,933' },
  { title: 'Operations Research Analyst', positions: '18,627' }
])
</script>

<style scoped>
.vacancies-container {
  padding: 20px;
  background-color: #f8f9fa;
}

.vacancies-title {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 20px;
  color: #333;
}

.vacancies-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
}

.vacancy-card {
  background-color: #fff;
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 5px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.vacancy-title {
  font-size: 16px;
  color: #007bff;
  margin-bottom: 10px;
}

.vacancy-positions {
  font-size: 14px;
  color: #666;
}
</style>