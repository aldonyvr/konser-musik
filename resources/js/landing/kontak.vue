<template>
  <nav>
    <KTHeader />
  </nav>

  <div class="contact-section">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-md-6">
          <img src="/media/musik/logo.png" alt="Logo" class="navbar-logo mb-4">
          <div class="row">
          <h2 class="mb-4 ">Customer Support MusicOnStage</h2>
          <i class=""></i>
        </div>
          
          <a href="https://wa.me/6285198362591?text=Halo,%20saya%20butuh%20bantuan%20mengenai%20MusicOnStage"
             target="_blank"
             class="btn btn-whatsapp">
            <i class="bi bi-whatsapp me-2 text-white"></i>
            Hubungi Kami via WhatsApp
          </a>
        </div>
      </div>
    </div>
  </div>

  <Footer />
</template>

<script setup lang="ts">
import KTHeader from "@/layouts/default-layout/components/header/NavbarLanding.vue";
import Footer from "@/Layouts/default-layout/components/footer/Footer.vue";
import axios from "@/libs/axios";
import { useRoute, useRouter } from "vue-router";
import { ref, onMounted, computed, watch } from "vue";
import Splide from "@splidejs/splide";
import "@splidejs/splide/dist/css/splide.min.css";
import { useAuthStore } from "@/stores/auth";

const konser = ref([]);
const cities = ref<string[]>([]);
const selectedCity = ref<string | null>(null);
const filteredKonser = ref([]);
const searchQuery = ref("");
const isLoading = ref(false);

const getKonser = async () => {
  try {
    const response = await axios.get("/konser");
    konser.value = response.data.data;
    applyFilters();
  } catch (error) {
    console.error("Error fetching concerts:", error);
  }
};

const recommendedKonser = computed(() => {
  return konser.value
    .filter(concert =>
      new Date(concert.tanggal) > new Date()
    )
    .sort((a, b) => {
      return new Date(a.tanggal).getTime() - new Date(b.tanggal).getTime();
    })
    .slice(0, 4);
});

const applyFilters = () => {
  if (!searchQuery.value && !selectedCity.value) {
    filteredKonser.value = konser.value;
    return;
  }

  let filtered = konser.value;

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter((concert) =>
      concert.title.toLowerCase().includes(query) ||
      concert.lokasi.toLowerCase().includes(query)
    );
  }

  if (selectedCity.value) {
    filtered = filtered.filter((concert) =>
      concert.lokasi.toLowerCase() === selectedCity.value?.toLowerCase()
    );
  }

  filteredKonser.value = filtered;
};

onMounted(() => {
  getKonser();
})
</script>

<style scoped>
.contact-section {
  min-height: 60vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.concert-description {
  border-left: 5px solid #007bff;
}

.shadow {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

ul {
  padding-left: 20px;
}

ul li {
  line-height: 1.8;
}

.navbar-logo {
  width: 250px; /* Adjust size as needed */
  height: auto;
  margin: 0 auto;
}

.btn-whatsapp {
  background-color: #25D366;
  color: white;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s ease;
  margin-top: 2rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: none;
}

.btn-whatsapp:hover {
  background-color: #128C7E;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(37, 211, 102, 0.2);
}
</style>
