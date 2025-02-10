<script setup lang="ts">
import KTHeader from "@/layouts/default-layout/components/header/NavbarLanding.vue";
import Footer from "@/Layouts/default-layout/components/footer/Footer.vue";

const registrationSteps = [
  {
    number: "01",
    title: "Buat Akun",
    description: "Daftar dan verifikasi email Anda untuk mulai menjelajahi konser.",
    icon: "fas fa-user-plus",
    color: "primary"
  },
  {
    number: "02",
    title: "Pilih Konser",
    description: "Temukan konser favorit Anda dari berbagai genre musik.",
    icon: "fas fa-music",
    color: "success"
  },
  {
    number: "03",
    title: "Pilih Tiket",
    description: "Pilih kategori tiket dan lokasi duduk yang Anda inginkan.",
    icon: "fas fa-ticket-alt",
    color: "info"
  },
  {
    number: "04",
    title: "Pembayaran",
    description: "Lakukan pembayaran melalui berbagai metode yang tersedia.",
    icon: "fas fa-credit-card",
    color: "warning"
  }
];

const features = [
  {
    icon: "fas fa-bolt",
    title: "Booking Instan",
    description: "Proses pemesanan tiket yang cepat dan mudah"
  },
  {
    icon: "fas fa-shield-alt",
    title: "Pembayaran Aman",
    description: "Transaksi dijamin aman dan terpercaya"
  },
  {
    icon: "fas fa-hand-holding-heart",
    title: "Pengalaman Premium",
    description: "Layanan pelanggan 24/7 dan fitur eksklusif"
  }
];
</script>

<template>
  <div class="blog-page">
    <KTHeader />
    
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-content">
        <h1>MusicOnStage</h1>
        <p>Temukan Berita Terbaru & Tips Seputar Konser</p>
      </div>
    </section>

    <!-- Registration Guide Section -->
    <section class="registration-section">
      <div class="container">
        <h2 class="section-title text-center">Cara Pemesanan Tiket</h2>
        <div class="steps-container">
          <div v-for="step in registrationSteps" :key="step.number" class="step-card">
            <div class="step-number" :class="`bg-${step.color}`">{{ step.number }}</div>
            <div class="step-icon">
              <i :class="step.icon"></i>
            </div>
            <h3>{{ step.title }}</h3>
            <p>{{ step.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
      <div class="container">
        <h2 class="section-title text-center">Keunggulan Kami</h2>
        <div class="features-grid">
          <div v-for="feature in features" :key="feature.title" class="feature-card">
            <i :class="feature.icon"></i>
            <h3>{{ feature.title }}</h3>
            <p>{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <Footer />
  </div>
</template>

<style scoped>

.hero-section {
  height: 50vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;

  margin-top: 64px;
}

.hero-content h1 {
  font-size: 4rem;
  font-weight: 700;
  animation: fadeInUp 1.5s ease;
}

.hero-content p {
  font-size: 1.5rem;
  animation: fadeInUp 1s ease 0.3s;
  opacity: 0;
  animation-fill-mode: forwards;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 500;
  margin-bottom: 3rem;
  position: relative;
  padding-bottom: 1rem;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 4px;
  background: #007bff;
}

.news-section {
  padding: 5rem 0;
}

.news-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.news-card {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  transition: transform 0.3s ease;
}

.news-card:hover {
  transform: translateY(-10px);
}

.news-image {
  position: relative;
  height: 200px;
}

.news-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.category-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: #007bff;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.8rem;
}

.news-content {
  padding: 1.5rem;
}

.news-date {
  color: #666;
  font-size: 0.9rem;
}

.read-more {
  background: none;
  border: 2px solid #007bff;
  color: #007bff;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  transition: all 0.3s ease;
}

.read-more:hover {
  background: #007bff;
}

.registration-section {
  padding: 1rem 0;
}

.steps-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}

.step-card {
  text-align: center;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  position: relative;
  overflow: hidden;
}

.step-number {
  position: absolute;
  top: -15px;
  right: -15px;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}

.step-icon {
  font-size: 2.5rem;
  color: #007bff;
  margin-bottom: 1rem;
}

.features-section {
  padding: 5rem 0;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}

.feature-card {
  text-align: center;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  transition: transform 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-10px);
}

.feature-card i {
  font-size: 3rem;
  color: #007bff;
  margin-bottom: 1rem;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 768px) {
  .hero-content h1 {
    font-size: 2.5rem;
  }
  
  .hero-content p {
    font-size: 1.2rem;
  }
  
  .section-title {
    font-size: 2rem;
  }
}
</style>
