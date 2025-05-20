<template>
  <section class="popular-categories">
    <div class="section-header">
      <h2>Popular category</h2>
    </div>
    
    <div class="categories-grid">
      <div 
        v-for="(category, index) in categories" 
        :key="category.name"
        class="category-card"
        :class="{ 'view-all-card': category.isViewAll }"
        @mouseenter="hoveredCategory = index"
        @mouseleave="hoveredCategory = null"
      >
        <div v-if="!category.isViewAll" class="category-content">
          <i :class="category.icon"></i>
          <div>
            <h3>{{ category.name }}</h3>
            <p>{{ category.positions }} Open position</p>
          </div>
        </div>
        <div v-else class="view-all-content">
          <i class="bi bi-arrow-right-circle"></i>
          View All
        </div>
      </div>
    </div>
    
    <div class="stats">
      1920 Hug × 554 Hug
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';

const hoveredCategory = ref(null);

const categories = [
  { name: "Graphics & Design", positions: 357, icon: "bi bi-brush" },
  { name: "Music & Audio", positions: 204, icon: "bi bi-music-note" },
  { name: "Code & Programming", positions: 312, icon: "bi bi-code-square" },
  { name: "Account & Finance", positions: 167, icon: "bi bi-calculator" },
  { name: "Digital Marketing", positions: 207, icon: "bi bi-megaphone" },
  { name: "Health & Care", positions: 125, icon: "bi bi-heart-pulse" },
  { name: "Video & Animation", positions: 247, icon: "bi bi-camera-reels" },
  { name: "Data & Science", positions: 57, icon: "bi bi-bar-chart-line" },
    { isViewAll: true }

];
</script>

<style scoped>
.popular-categories {
  background-color: #ffffff;
  padding: 80px 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.section-header {
  text-align: left;
  margin-bottom: 40px;
}

.popular-categories h2 {
  font-size: 2.2rem;
  color: #1a365d;
  font-weight: 700;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.category-card {
  background: #ffffff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  border: 1px solid #e2e8f0;
  cursor: pointer;
}

.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.1);
}

.category-content {
  display: flex;
  align-items: center;
  gap: 15px;
}

.category-content i {
  font-size: 1.8rem;
  color: #3b82f6;
}

.category-card:hover .category-content i {
  color: #2563eb;
}

.category-card h3 {
  font-size: 1.2rem;
  color: #1a365d;
  margin-bottom: 8px;
  font-weight: 600;
}

.category-card p {
  color: #4a5568;
  font-size: 0.95rem;
}

.view-all-card {
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #ebf2ff, #d6e4ff);
  color: #3b82f6;
  font-weight: 600;
}

.view-all-content {
  display: flex;
  align-items: center;
  gap: 10px;
}

.view-all-content i {
  font-size: 1.2rem;
}

.view-all-card:hover {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
}

.view-all-card:hover i {
  color: white;
}

.stats {
  margin-top: 40px;
  text-align: center;
  color: #718096;
  font-size: 0.9rem;
}

@media (max-width: 768px) {
  .popular-categories {
    padding: 60px 20px;
  }
  
  .popular-categories h2 {
    font-size: 1.8rem;
  }
  
  .categories-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  }
  
  .category-content {
    flex-direction: column;
    text-align: center;
    gap: 10px;
  }
  
  .category-content i {
    margin-bottom: 5px;
  }
}
</style>