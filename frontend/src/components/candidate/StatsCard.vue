<template>
  <div class="stats-card" :class="`stats-card--${type}`">
    <div class="stats-card__icon">
      <slot name="icon">
        <!-- Default icon if none provided -->
        <div class="default-icon">
          {{ iconMap[type] }}
        </div>
      </slot>
    </div>
    
    <div class="stats-card__content">
      <p class="stats-card__value">{{ value }}</p>
      <h4 class="stats-card__title">{{ title }}</h4>
      <p v-if="changePercentage !== null" 
         class="stats-card__change" 
         :class="{'positive': changePercentage >= 0, 'negative': changePercentage < 0}">
        {{ Math.abs(changePercentage) }}% {{ changePercentage >= 0 ? '↑' : '↓' }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  type: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'applications', 'saved', 'interviews', 'profile'].includes(value)
  },
  changePercentage: {
    type: Number,
    default: null
  }
});

const iconMap = {
  default: '📊',
  applications: '👜',
  saved: '❤️',
  interviews: '🗓️',
  profile: '📝',
  alert: '🔔',
};
</script>

<style scoped>
.stats-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stats-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.stats-card__icon {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  font-size: 24px;
}

.stats-card--applications .stats-card__icon {
  background-color: #E8F0FE;
  color: #1967D2;
}

.stats-card--saved .stats-card__icon {
  background-color: #FFEEF0;
  color: #F94144;
}

.stats-card--interviews .stats-card__icon {
  background-color: #E6F7EE;
  color: #34A853;
}

.stats-card--profile .stats-card__icon {
  background-color: #F5F0FF;
  color: #673AB7;
}

.stats-card__content {
  flex: 1;
}

.stats-card__title {
  font-size: 14px;
  color: #6B6B6B;
  margin-bottom: 4px;
}

.stats-card__value {
  font-size: 24px;
  font-weight: 600;
  color: #1A1A1A;
  margin-bottom: 4px;
}

.stats-card__change {
  font-size: 12px;
}

.stats-card__change.positive {
  color: #34A853;
}

.stats-card__change.negative {
  color: #EA4335;
}

.default-icon {
  font-size: 24px;
}
</style>