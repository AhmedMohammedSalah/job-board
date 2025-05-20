<template>
  <div class="error-page d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <!-- Animated illustration -->
          <div class="error-illustration mb-4 position-relative">
            <font-awesome-icon 
              icon="building" 
              class="text-primary fa-5x mb-3 building-icon"
            />
            <font-awesome-icon 
              icon="map-marker-alt" 
              class="text-danger fa-3x position-absolute pin-icon"
              style="top: 20%; right: 30%;"
            />
            <font-awesome-icon 
              icon="money-bill-wave" 
              class="text-success fa-2x position-absolute money-icon"
              style="bottom: 15%; left: 25%;"
            />
            <div class="error-code display-1 fw-bold text-dark mt-3">404</div>
          </div>

          <h1 class="display-4 fw-bold mb-3 text-dark">Oops! Page Not Found</h1>
          <p class="lead text-secondary mb-4">
            The page you're looking for doesn't exist or has been moved. 
            Maybe our <font-awesome-icon icon="briefcase" /> careers page or 
            <font-awesome-icon icon="chart-line" /> analytics dashboard would interest you?
          </p>

          <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
            <router-link 
              to="/" 
              class="btn btn-primary btn-lg px-4 py-2 rounded-pill shadow-sm"
            >
              <font-awesome-icon icon="arrow-right" class="me-2" />
              Go to Homepage
            </router-link>
            
            <router-link 
              to="/contact" 
              class="btn btn-outline-primary btn-lg px-4 py-2 rounded-pill"
            >
              <font-awesome-icon icon="envelope" class="me-2" />
              Contact Support
            </router-link>
          </div>

          <div class="social-links mt-4">
            <p class="text-muted mb-3">Or follow us on social media</p>
            <div class="d-flex justify-content-center gap-3">
              <a href="#" class="btn btn-outline-secondary rounded-circle p-3">
                <font-awesome-icon :icon="['fab', 'facebook-f']" />
              </a>
              <a href="#" class="btn btn-outline-secondary rounded-circle p-3">
                <font-awesome-icon :icon="['fab', 'twitter']" />
              </a>
              <a href="#" class="btn btn-outline-secondary rounded-circle p-3">
                <font-awesome-icon :icon="['fab', 'linkedin-in']" />
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFoundPage',
  mounted() {
    // Add animation class after component mounts
    setTimeout(() => {
      const building = document.querySelector('.building-icon');
      const pin = document.querySelector('.pin-icon');
      const money = document.querySelector('.money-icon');
      
      if (building) building.classList.add('animate__animated', 'animate__bounceIn');
      if (pin) pin.classList.add('animate__animated', 'animate__bounce');
      if (money) money.classList.add('animate__animated', 'animate__rubberBand');
    }, 100);
  }
}
</script>

<style scoped>
.error-page {
  background-color: var(--bg-primary);
}

.error-illustration {
  max-width: 300px;
  margin: 0 auto;
  position: relative;
}

.error-code {
  font-size: 6rem;
  line-height: 1;
  color: var(--primary-color);
  text-shadow: 2px 2px 0 rgba(0,0,0,0.1);
  position: relative;
}

.error-code::after {
  content: "";
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 4px;
  background: var(--accent-color);
  border-radius: 2px;
}

.building-icon {
  opacity: 0;
  transform: scale(0.5);
}

.pin-icon, .money-icon {
  opacity: 0;
}

.animate__bounceIn {
  opacity: 1;
  animation-delay: 0.3s;
}

.animate__bounce {
  opacity: 1;
  animation-delay: 0.8s;
}

.animate__rubberBand {
  opacity: 1;
  animation-delay: 1.2s;
}

@media (min-width: 768px) {
  .error-code {
    font-size: 8rem;
  }
  
  .error-illustration {
    max-width: 400px;
  }
}

/* Add some subtle animation to the buttons on hover */
.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md) !important;
  transition: all 0.2s ease;
}

.btn-outline-primary:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-sm);
  transition: all 0.2s ease;
}
</style>