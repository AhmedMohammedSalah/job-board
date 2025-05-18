<template>
  <header class="header">
    <nav class="nav-container">
      <div class="nav-links">
        <router-link 
          to="/" 
          class="nav-link"
          active-class="nav-link-active"
          exact
        >
          <HomeIcon class="nav-icon" />
          <span>Home</span>
        </router-link>
        
        <router-link 
          to="/find-job" 
          class="nav-link"
          active-class="nav-link-active"
        >
          <BriefcaseIcon class="nav-icon" />
          <span>Find Job</span>
        </router-link>
        
        <router-link 
          to="/employers" 
          class="nav-link"
          active-class="nav-link-active"
        >
          <BuildingOfficeIcon class="nav-icon" />
          <span>Employers</span>
        </router-link>
        
        <router-link 
          to="/candidates" 
          class="nav-link"
          active-class="nav-link-active"
        >
          <UserGroupIcon class="nav-icon" />
          <span>Candidates</span>
        </router-link>
        
        <router-link 
          to="/pricing" 
          class="nav-link"
          active-class="nav-link-active"
        >
          <CurrencyDollarIcon class="nav-icon" />
          <span>Pricing</span>
        </router-link>
        
        <router-link 
          to="/support" 
          class="nav-link"
          active-class="nav-link-active"
        >
          <LifebuoyIcon class="nav-icon" />
          <span>Support</span>
        </router-link>
      </div>
      
      <div class="nav-contact">
        <div class="contact-item">
          <PhoneIcon class="contact-icon" />
          <span class="phone">+1-202-555-0178</span>
        </div>
        <div class="contact-item">
          <GlobeAltIcon class="contact-icon" />
          <span class="language">English</span>
          <ChevronDownIcon class="dropdown-icon" />
        </div>
      </div>
    </nav>
  </header>
</template>

<script setup>
import { 
  HomeIcon,
  BriefcaseIcon,
  BuildingOfficeIcon,
  UserGroupIcon,
  CurrencyDollarIcon,
  LifebuoyIcon,
  PhoneIcon,
  GlobeAltIcon,
  ChevronDownIcon
} from '@heroicons/vue/24/outline'
</script>

<style scoped>
.header {
  background-color: #ffffff;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  padding: 1rem 2rem;
  position: sticky;
  top: 0;
  z-index: 50;
}

.nav-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1400px;
  margin: 0 auto;
}

.nav-links {
  display: flex;
  gap: 1.5rem;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
  color: #4b5563;
  font-weight: 500;
  padding: 0.5rem 0.75rem;
  border-radius: 0.375rem;
  transition: all 0.3s ease;
}

.nav-link:hover {
  color: #1e40af;
  background-color: #f3f4f6;
}

.nav-link-active {
  color: #1e40af;
  background-color: #eff6ff;
  font-weight: 600;
}

.nav-icon {
  width: 1.25rem;
  height: 1.25rem;
}

.nav-contact {
  display: flex;
  gap: 1.5rem;
  align-items: center;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.contact-item:hover {
  background-color: #f3f4f6;
}

.phone, .language {
  color: #4b5563;
  font-size: 0.9rem;
  font-weight: 500;
}

.contact-icon {
  width: 1.2rem;
  height: 1.2rem;
  color: #6b7280;
}

.dropdown-icon {
  width: 1rem;
  height: 1rem;
  color: #9ca3af;
}
</style>