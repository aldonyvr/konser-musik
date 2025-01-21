<script setup lang="ts">
import { ref } from 'vue';
import { useRoute } from 'vue-router'; // Import vue-router untuk route
import KTHeader from "@/layouts/default-layout/components/header/NavbarLanding.vue";
import Footer from '@/Layouts/default-layout/components/footer/Footer.vue';

const route = useRoute();

const concerts = ref([
    // {
    //   id: 1,
    //   name: 'Rock Fest 2024',
    //   date: '2024-09-15',
    //   location: 'Madison Square Garden, NYC',
    //   harga: 'Rp. 60.000',
    //   description: 'An amazing rock festival featuring top bands.',
    //   image: '/media/musik/eventjazz.jpg'
    // },
    // {
    //   id: 2,
    //   name: 'Jazz Night',
    //   date: '2024-10-05',
    //   location: 'Blue Note Jazz Club, NYC',
    //   harga: 'Rp. 60.000',
    //   description: 'A night of smooth jazz with renowned artists.',
    //   image: '/media/musik/eventjazz.jpg'
    // },
  
]);
</script>

<template>
  <nav>
    <KTHeader />
  </nav>

  <!-- Konten Riwayat Pembelian Tiket -->
   <div class="mt-20"></div>
    <div class="container flex-grow-1 mt-15">
      <div class="text-center " style="font-family: 'Lobster', cursive; font-size: 25px;">Riwayat Pembelian Tiket Konser</div>

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

   <div class=" h-full flex bottom-0 justify-content-end items-end">
    <Footer />
  </div>
</template>

<style scoped>
.card:hover {
  transform: scale(1.01);
  transition: transform 0.3s ease;
}
</style>
