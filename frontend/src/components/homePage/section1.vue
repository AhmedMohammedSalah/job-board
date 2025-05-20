<template>
  <section class="job-search-section">
    <div class="hero-container">
      <div class="hero-main-content">
        <div class="left-section">
          <div class="search-header">
            <h1>Find a job that suits your interest & skills.</h1>
            <p class="subtitle">Alliqum vitae turpis diam convallis finibus at risus. Nullam in scelerisque leo, eget sollicitudin velit vestibulum.</p>
          </div>

          <div class="search-box">
            <div class="search-inputs">
              <div class="input-group">
                <span class="input-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                  </svg>
                </span>
                <input
                  type="text"
                  v-model="searchQuery"
                  placeholder="Job title, Keyword..."
                  @input="searchJobs"
                >
              </div>

              <div class="input-group">
                <span class="input-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
                    <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                    <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                  </svg>
                </span>
                <input
                  type="text"
                  v-model="locationQuery"
                  placeholder="Your location"
                  @input="searchJobs"
                >
              </div>

              <button class="search-button" @click="searchJobs">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                  <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                </svg>
                Find Job
              </button>
            </div>

            <div class="search-suggestions">
              <span class="suggestion-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-lightbulb" viewBox="0 0 16 16">
                  <path d="M2 6a6 6 0 1 1 10.174 4.31c-.203.196-.359.4-.453.619l-.762 1.769A.5.5 0 0 1 10.5 13a.5.5 0 0 1 0 1 .5.5 0 0 1 0 1l-.224.447a1 1 0 0 1-.894.553H6.618a1 1 0 0 1-.894-.553L5.5 15a.5.5 0 0 1 0-1 .5.5 0 0 1 0-1 .5.5 0 0 1-.46-.302l-.761-1.77a1.964 1.964 0 0 0-.453-.618A5.984 5.984 0 0 1 2 6zm6-5a5 5 0 0 0-3.479 8.592c.263.254.514.564.676.941l.71 1.653c.08.186.232.361.46.497a.5.5 0 0 0 .478-.002l.647-.646a.5.5 0 0 1 .2-.1l1.875-.125a.5.5 0 0 1 .4.1l.647.646a.5.5 0 0 0 .478.002c.228-.136.38-.311.46-.497l.708-1.65c.162-.377.413-.687.676-.941A5 5 0 0 0 8 1z"/>
                  <path d="M6.5 6.586v1a.5.5 0 0 0 1 0v-1h1a.5.5 0 0 0 0-1h-1v-1a.5.5 0 0 0-1 0v1h-1a.5.5 0 0 0 0 1h1z"/>
                </svg>
                Suggestion:
              </span>
              <span
                v-for="category in categories"
                :key="category.id"
                class="suggestion-tag"
                @click="searchByCategory(category.name)"
              >
                {{ category.name }}
              </span>
            </div>
          </div>
        </div>

        <div class="hero-image">
          <img src="https://framerusercontent.com/images/TwNKOlvvIIIICip9RjbDbH8AC7A.png" alt="Job seekers">
        </div>
      </div>

      <div class="stats-container">
        <div class="stat-item">
          <div class="stat-number">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-briefcase" viewBox="0 0 16 16">
              <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1h-3zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5zm1.886 6.914L15 7.151V12.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5V7.15l6.614 1.764a1.5 1.5 0 0 0 .772 0zM1.5 4h13a.5.5 0 0 1 .5.5v1.616L8.129 7.948a.5.5 0 0 1-.258 0L1 6.116V4.5a.5.5 0 0 1 .5-.5z"/>
            </svg>
            1,75,324
          </div>
          <div class="stat-label">Live Jobs</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-building" viewBox="0 0 16 16">
              <path d="M4 2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1h1a.5.5 0 0 1 .5.5v11a.5.5 0 0 1-.5.5H1a.5.5 0 0 1-.5-.5V4a.5.5 0 0 1 .5-.5h1v-1zM2 4v10h12V4H2zm2.5 0a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.5-.5h-1zm3 0a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.5-.5h-1zm3 0a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.5-.5h-1z"/>
            </svg>
            97,354
          </div>
          <div class="stat-label">Companies</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
              <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.629 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
            </svg>
            38,47,154
          </div>
          <div class="stat-label">Candidates</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
              <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm0-1A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm.5-7.5V4a.5.5 0 0 0-1 0v4a.5.5 0 0 0 .146.354l2 2a.5.5 0 0 0 .707-.708L8.5 7.5z"/>
            </svg>
            7,532
          </div>
          <div class="stat-label">New Jobs</div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// البيانات
const searchQuery = ref('');
const locationQuery = ref('');
const categories = ref([
  { id: 1, name: 'Designer' },
  { id: 2, name: 'Programming' },
  { id: 3, name: 'Digital Marketing' },
  { id: 4, name: 'Video' },
  { id: 5, name: 'Animation' },
]);

// دالة البحث (Placeholder)
const searchJobs = () => {
  console.log(`Searching for: ${searchQuery.value} in ${locationQuery.value}`);
};

// دالة البحث حسب الفئة
const searchByCategory = (category) => {
  searchQuery.value = category;
  locationQuery.value = ''; // Clear location when searching by category
  searchJobs();
};

// تحميل البيانات عند تحميل المكون
onMounted(() => {
  console.log('Component mounted, ready to fetch data if needed.');
});
</script>

<style scoped>
.job-search-section {
  background: #f8f9fa;
  padding: 100px 0;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
  position: relative;
  overflow: hidden;
}

.hero-container {
  max-width: 1300px;
  margin: 0 auto;
  padding: 0 20px;
}

.hero-main-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start; /* Align items to the top to raise the image */
  padding: 40px 0;
}

.left-section {
  flex: 1;
  padding-right: 40px; /* Space between text/search and image */
}

.search-header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 16px;
  line-height: 1.2;
  text-align: left;
}

.search-header .subtitle {
  color: #64748b;
  font-size: 1rem;
  line-height: 1.6;
  margin-bottom: 32px;
  text-align: left;
  max-width: 90%;
}

.hero-image {
  flex: 1;
  display: flex;
  justify-content: flex-end; /* Align image to the right */
  align-items: flex-start; /* Align image to the top */
  padding-top: 20px; /* Adjust this value to move the image higher or lower */
}

.hero-image img {
  width: 100%;
  max-width: 500px;
  height: auto;
  border-radius: 10px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.hero-image img:hover {
  transform: scale(1.03);
}

.search-box {
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
  padding: 32px;
  margin-bottom: 32px;
  text-align: left;
  transition: box-shadow 0.3s ease;
}

.search-box:hover {
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.search-inputs {
  display: flex;
  gap: 12px;
  margin-bottom: 24px;
  align-items: stretch;
}

.input-group {
  flex: 1;
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 12px;
  color: #9ca3af;
  transition: color 0.3s ease;
}

.input-group:hover .input-icon {
  color: #3b82f6;
}

.input-group input {
  width: 100%;
  padding: 12px 12px 12px 40px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 1rem;
  color: #1f2937;
  transition: all 0.3s ease;
}

.input-group input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: #3b82f6;
  color: #ffffff;
  border: none;
  border-radius: 6px;
  padding: 12px 20px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  height: 100%;
}

.search-button:hover {
  background: #2563eb;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.search-suggestions {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
  font-size: 0.875rem;
}

.suggestion-label {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #64748b;
  font-weight: 500;
}

.suggestion-tag {
  background: #f1f5f9;
  color: #1e293b;
  padding: 6px 12px;
  border-radius: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.875rem;
  font-weight: 500;
}

.suggestion-tag:hover {
  background: #e2e8f0;
  transform: translateY(-1px);
}

.stats-container {
  display: flex; /* Use flexbox for horizontal arrangement */
  flex-wrap: wrap; /* Allow items to wrap to the next line */
  justify-content: space-between; /* Distribute space between items */
  gap: 20px; /* Space between stat items */
  width: 100%;
  margin-top: 40px; /* Add margin to separate from the main hero section */
}

.stat-item {
  flex: 1 1 calc(25% - 15px); /* Four items per row with gap */
  text-align: center;
  background: #ffffff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease;
}

.stat-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
}

.stat-number {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 1.875rem;
  font-weight: 700;
  color: #3b82f6;
  margin-bottom: 8px;
}

.stat-label {
  color: #64748b;
  font-size: 0.875rem;
  font-weight: 500;
}

@media (max-width: 1200px) {
  .hero-container {
    padding: 0 16px;
  }
  .search-header h1 {
    font-size: 2rem;
  }
}

@media (max-width: 1024px) {
  .hero-main-content {
    flex-direction: column; /* Stack sections on smaller screens */
    align-items: center;
  }
  .left-section {
    padding-right: 0;
    margin-bottom: 40px; /* Space between stacked sections */
  }
  .search-header h1,
  .search-header .subtitle {
    text-align: center;
    max-width: 100%;
    margin-left: auto;
    margin-right: auto;
  }
  .hero-image {
    justify-content: center;
    padding-top: 0; /* Remove top padding when stacked */
  }
  .stats-container {
    justify-content: center; /* Center align items when they wrap */
    gap: 15px;
  }
  .stat-item {
    flex: 1 1 calc(50% - 10px); /* 2 items per row on tablets */
  }
}

@media (max-width: 768px) {
  .hero-content {
    padding: 16px 0;
  }
  .search-inputs {
    flex-direction: column;
  }
  .search-button {
    width: 100%;
    padding: 12px;
  }
  .stats-container {
    gap: 16px;
  }
  .stat-item {
    flex: 1 1 100%; /* Stack stats on very small screens */
  }
  .search-header h1 {
    font-size: 1.5rem;
  }
}

@media (max-width: 480px) {
  .job-search-section {
    padding: 60px 0;
  }
  .search-header h1 {
    font-size: 1.25rem;
  }
  .search-header .subtitle {
    font-size: 0.875rem;
  }
  .search-box {
    padding: 16px;
  }
  .stat-number {
    font-size: 1.5rem;
  }
}
</style>