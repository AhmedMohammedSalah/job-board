<template>
    <section class="candidate-section">
      <div class="container">
        <div class="section-header">
          <h1>Become a Candidate</h1>
          <p class="subtitle">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Circus cursus a dolor convallis efficitur.</p>
        </div>
  
        <div class="cards-container">
          <div class="card">
            <div class="card-image">
            
              <img src="/Pasted image.png" alt="Candidate" class="card-img">
            </div>
            <div class="card-content">
              <h2>Register Now</h2>
              <h3>Become a Candidate</h3>
              <p>Cras in massa palesem ipsum, nostra ligula nunc, luctus dui. Motus sed efficitur dolor. Pecenas augue risus, aliqua.</p>
              <button class="register-btn" href="/register"  >Register Now</button>
            </div>
          </div>
  
          <div class="card">
            <div class="card-image">
              <!-- Replace with your actual image -->
              <img src="/Pasted image (2).png" alt="Employer" class="card-img">
            </div>
            <div class="card-content">
              <h2>Register Now</h2>
              <h3>Become an Employer</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in dui mauris. Vivamus hendrerit arcu.</p>
              <!-- //link go to register page  br router -->

              <button class="register-btn" href="/register"  >Register Now</button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </template>
  
  <style scoped>
  .candidate-section {
    background-color: #1a1a1a;
    color: #e2e8f0;
    padding: 4rem 2rem;
  }
  
  .container {
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .section-header {
    text-align: center;
    margin-bottom: 3rem;
  }
  
  .section-header h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: #ffffff;
  }
  
  .subtitle {
    color: #a1a1aa;
    max-width: 600px;
    margin: 0 auto;
    font-size: 1.1rem;
    line-height: 1.6;
  }
  
  .cards-container {
    display: flex;
    gap: 2rem;
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .card {
    background-color: #2d2d2d;
    border-radius: 8px;
    overflow: hidden;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease;
  }
  
  .card:hover {
    transform: translateY(-5px);
  }
  
  .card-image {
    height: 250px;
    overflow: hidden;
  }
  
  .card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
  }
  
  .card:hover .card-img {
    transform: scale(1.05);
  }
  
  .card-content {
    padding: 2rem;
  }
  
  .card-content h2 {
    color: #60a5fa;
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
  }
  
  .card-content h3 {
    color: #ffffff;
    font-size: 1.8rem;
    margin-bottom: 1rem;
  }
  
  .card-content p {
    color: #a1a1aa;
    margin-bottom: 2rem;
    line-height: 1.6;
  }
  
  .register-btn {
    background-color: #60a5fa;
    color: #ffffff;
    border: none;
    padding: 0.8rem 2rem;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  .register-btn:hover {
    background-color: #3b82f6;
  }
  
  @media (max-width: 768px) {
    .cards-container {
      flex-direction: column;
      align-items: center;
    }
    
    .card {
      max-width: 100%;
    }
  }
  </style>