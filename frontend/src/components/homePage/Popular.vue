<template>
  <section class="how-it-works">
    <div class="section-header">
      <h2>How JobPilot Works</h2>
      <p class="section-subtitle">Find your dream job in just 4 simple steps</p>
    </div>
    
    <div class="steps-container">
      <div 
        v-for="(step, index) in steps" 
        :key="step.title" 
        class="step"
        :class="{ 'active-step': hoveredStep === index || activeStep === index }"
        @mouseenter="hoveredStep = index"
        @mouseleave="hoveredStep = null"
        @click="activeStep = index"
      >
        <div class="step-number">{{ index + 1 }}</div>
        <div class="step-icon">
          <component :is="step.icon" />
        </div>
        <h3>{{ step.title }}</h3>
        <p>{{ step.description }}</p>
        <div class="step-indicator"></div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { 
  UserCircleIcon, 
  DocumentTextIcon, 
  MagnifyingGlassIcon, 
  PaperAirplaneIcon 
} from '@heroicons/vue/24/outline';

const hoveredStep = ref(null);
const activeStep = ref(0);

const steps = [
  {
    title: "Create Your Account",
    description: "Get started in minutes with a simple registration process. Tell us about your professional background to personalize your experience.",
    icon: UserCircleIcon
  },
  {
    title: "Upload Your CV/Resume",
    description: "Our system intelligently analyzes your qualifications and extracts key information to match you with ideal opportunities.",
    icon: DocumentTextIcon
  },
  {
    title: "Discover Suitable Jobs",
    description: "Browse curated job listings that match your skills and preferences, with smart recommendations that improve over time.",
    icon: MagnifyingGlassIcon
  },
  {
    title: "Apply With One Click",
    description: "Submit applications effortlessly with your pre-analyzed profile. Track all your applications in one convenient dashboard.",
    icon: PaperAirplaneIcon
  }
];
</script>

<style scoped>
.how-it-works {
  background-color: #ffffff;
  padding: 80px 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.section-header {
  text-align: center;
  margin-bottom: 60px;
}

.how-it-works h2 {
  font-size: 2.2rem;
  color: #1a365d;
  margin-bottom: 15px;
  font-weight: 700;
}

.section-subtitle {
  color: #4a5568;
  font-size: 1.1rem;
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
}

.steps-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 25px;
  position: relative;
}

.step {
  background: #ffffff;
  padding: 30px 25px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  cursor: pointer;
  border: 1px solid #e2e8f0;
  position: relative;
  overflow: hidden;
  text-align: center;
}

.step:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.1);
}

.step.active-step {
  border-color: #3b82f6;
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
}

.step.active-step .step-indicator {
  width: 100%;
  background-color: #3b82f6;
}

.step-number {
  position: absolute;
  top: 15px;
  left: 15px;
  font-size: 0.9rem;
  font-weight: 700;
  color: #3b82f6;
  background-color: #ebf2ff;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.step-icon {
  width: 70px;
  height: 70px;
  background: linear-gradient(135deg, #ebf2ff, #d6e4ff);
  color: #3b82f6;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 25px;
  transition: all 0.3s ease;
}

.step:hover .step-icon,
.step.active-step .step-icon {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  transform: scale(1.05);
}

.step-icon svg {
  width: 30px;
  height: 30px;
}

.step h3 {
  font-size: 1.3rem;
  margin-bottom: 15px;
  color: #1a365d;
  font-weight: 600;
}

.step p {
  color: #4a5568;
  line-height: 1.6;
  font-size: 0.95rem;
  margin-bottom: 20px;
}

.step-indicator {
  position: absolute;
  bottom: 0;
  left: 0;
  height: 3px;
  width: 0;
  background-color: transparent;
  transition: all 0.3s ease;
}

@media (max-width: 768px) {
  .how-it-works {
    padding: 60px 20px;
  }
  
  .how-it-works h2 {
    font-size: 1.8rem;
  }
  
  .steps-container {
    grid-template-columns: 1fr;
    max-width: 500px;
    margin: 0 auto;
  }
  
  .step {
    padding: 25px 20px;
  }
}
</style>