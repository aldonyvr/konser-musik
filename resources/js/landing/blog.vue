<script setup lang="ts">
import KTHeader from "@/layouts/default-layout/components/header/NavbarLanding.vue";
import Footer from "@/Layouts/default-layout/components/footer/Footer.vue";

// const steps = [
//   {
//     number: 1,
//     title: "Cari Konser Favorit Anda",
//     description: "Jelajahi daftar konser musik yang tersedia di website kami.",
//     icon: "🎵",
//   },
//   {
//     number: 2,
//     title: "Pilih Tiket yang Sesuai",
//     description: "Pilih jenis tiket sesuai kebutuhan Anda, mulai dari reguler hingga VIP.",
//     icon: "🎫",
//   },
//   {
//     number: 3,
//     title: "Isi Data Pemesanan",
//     description: "Masukkan informasi Anda untuk memastikan tiket terkonfirmasi.",
//     icon: "📝",
//   },
//   {
//     number: 4,
//     title: "Lakukan Pembayaran",
//     description: "Bayar tiket Anda dengan metode pembayaran yang tersedia.",
//     icon: "💳",
//   },
//   {
//     number: 5,
//     title: "Nikmati Konsernya!",
//     description: "Bawa tiket Anda dan bersiaplah untuk pengalaman musik yang luar biasa.",
//     icon: "🎉",
//   },
// ];
</script>

<template>
  <div class="min-h-screen">
    <!-- Header -->
    <nav class="">
      <KTHeader />
    </nav>

    <!-- Hero Section -->
    <div class="py-10"></div>
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white text-center">
      <h1 class="text-5xl font-bold mt-20">Selamat Datang di Dunia Musik!</h1>
      <p class="mt-4 text-xl ">
        Nikmati pengalaman konser terbaik dengan pemesanan tiket yang mudah dan cepat.
      </p>
    </div>

    <div class="bg-gray-50 px-4">
      <div class="container mx-auto text-center">
        <p class="text-lg text-gray-600 leading-relaxed">
          Musik bukan hanya tentang melodi dan lirik, tetapi juga tentang cerita yang
          menghubungkan kita semua. Dalam setiap konser, ada ribuan jiwa yang datang
          bersama untuk merayakan cinta terhadap musik. Pemesanan tiket hanyalah langkah pertama
          untuk menjadi bagian dari momen magis ini. Bayangkan berdiri di tengah kerumunan,
          dikelilingi oleh lampu-lampu yang berkilauan, dan merasakan getaran bass di dada Anda.
        </p>
        <p class="mt-6 text-lg text-gray-600 leading-relaxed">
          Selamat datang di dunia di mana musik adalah bahasa universal. Tidak peduli siapa Anda
          atau dari mana Anda berasal, konser musik selalu memiliki tempat untuk Anda.
          Ayo, pesan tiket Anda hari ini, dan mari kita ciptakan kenangan yang tak terlupakan!
        </p>
      </div>
    </div>

    <!-- Steps Section -->
    <div class="container mx-auto mb-20 px-4 mt-15">
      <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">
        Langkah-Langkah Memesan Tiket Konser
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 bg-gray-100">
        <div
          v-for="step in steps"
          :key="step.number"
          class="shadow-lg rounded-lg p-6 text-center hover:shadow-xl transition-shadow duration-300"
        >
          <div class="text-5xl mb-4 text-indigo-600">{{ step.icon }}</div>
          <h3 class="text-xl font-semibold text-gray-800">
            {{ step.number }}. {{ step.title }}
          </h3>
          <p class="mt-2 text-gray-600">{{ step.description }}</p>
        </div>
      </div>
    </div>

    <!-- Story Section -->

    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
      <Footer />
    </footer>
  </div>
</template>

<style scoped>

.container {
  max-width: 1000px; /* Lebar maksimal untuk tampilan yang lebih minimalis */
}

h2 {
  font-size: 1.5rem; /* Ukuran font lebih kecil untuk kesan minimalis */
}

.grid div {
  padding: 1rem; /* Padding lebih kecil untuk tata letak bersih */
}

.grid div:hover {
  border-color: #6366f1; /* Ubah warna border saat hover untuk efek visual halus */
}

</style>
