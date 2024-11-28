<script setup lang="ts">
import { ref } from 'vue';
import { useRoute } from 'vue-router'; // Import vue-router untuk route
import KTHeader from "@/layouts/default-layout/components/header/NavbarLanding.vue";
import Footer from '@/Layouts/default-layout/components/footer/Footer.vue';
// import Footer from '@/Layouts/Footer.vue'; // Import Footer component

// Mengambil route dengan vue-router
const route = useRoute();

// Data concert menggunakan ref
const concerts = ref([
  {
    id: 1,
    name: 'Rock Fest 2024',
    date: '2024-09-15',
    location: 'Madison Square Garden, NYC',
    harga: 'Rp. 60.000',
    description: 'An amazing rock festival featuring top bands.',
    image: '/media/musik/eventjazz.jpg'
  },
  {
    id: 2,
    name: 'Jazz Night',
    date: '2024-10-05',
    location: 'Blue Note Jazz Club, NYC',
    harga: 'Rp. 60.000',
    description: 'A night of smooth jazz with renowned artists.',
    image: '/media/musik/eventjazz.jpg'
  },
  {
    id: 3,
    name: 'Pop Fiesta',
    date: '2024-12-12',
    location: 'Staples Center, LA',
    harga: 'Rp. 75.000',
    description: 'The best pop event of the year.',
    image: '/media/musik/eventjazz.jpg'
  },
  {
    id: 4,
    name: 'Pop Fiesta',
    date: '2024-12-12',
    location: 'Staples Center, LA',
    harga: 'Rp. 75.000',
    description: 'The best pop event of the year.',
    image: '/media/musik/eventjazz.jpg'
  },
]);
</script>

<template>
  <!-- Menggunakan Bootstrap Navbar -->
  <nav>
    <KTHeader />
  </nav>

  <!-- Konten Riwayat Pembelian Tiket -->
  <div class="container mt-10">
    <h2 class="text-center display-6 font-weight-bold" style="font-family: 'Lobster', cursive;">Riwayat Pembelian Tiket Konser</h2>

    <!-- Jika tidak ada konser -->
    <div v-if="concerts.length === 0" class="text-center text-muted mt-5">
      <p class="lead">Tidak ada riwayat pembelian tiket konser ditemukan.</p>
    </div>

    <!-- Grid Konser dengan Bootstrap -->
    <div v-else class="row mt-15 mb-20  ">
      <div v-for="concert in concerts" :key="concert.id" class="col-md-6 mb-7">
        <div class="card shadow-lg hover-shadow">
          <div class="row g-0">
            <div class="col-md-4">
              <img :src="concert.image" class="img-fluid rounded-start align-items-center" alt="Concert Image">
            </div>
            <div class="col-md-8">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <h5 class="card-title">Pembelian</h5>
                  <span class="badge bg-success text-light align-self-center">Selesai</span>
                </div>
                <hr>
                <h5 class="text-primary">Tiket {{ concert.name }}</h5>
                <p class="card-text"><i class="fa-solid fa-calendar-day text-danger me-2"></i> Masa berlaku {{ concert.date }} sampai {{ concert.date }}</p>
                <p class="card-text"><i class="fa-solid fa-location-dot text-success me-2"></i> {{ concert.location }}</p>
                <p class="fw-bold">Total Pembayaran: {{ concert.harga }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <Footer />
</template>

<style scoped>
/* Optional custom styles if needed */
.card:hover {
  transform: scale(1.01);
  transition: transform 0.3s ease;
}
</style>
