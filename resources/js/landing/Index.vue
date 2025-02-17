<script setup lang="ts">
import KTHeader from "@/layouts/default-layout/components/header/NavbarLanding.vue";
import Footer from "@/Layouts/default-layout/components/footer/Footer.vue";
import axios from "@/libs/axios";
import { useRoute, useRouter } from "vue-router";
import { ref, onMounted, computed, watch, nextTick } from "vue";
import Splide from "@splidejs/splide";
import "@splidejs/splide/dist/css/splide.min.css";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
const route = useRoute();
const router = useRouter();
const konser = ref([]);
const cities = ref<string[]>([]);
const selectedCity = ref<string>('');
const filteredKonser = ref([]);
const searchQuery = ref("");
const isLoading = ref(false);

const getKonser = async () => {
  try {
    const response = await axios.get("/konser", {
      params: {
        per_page: 0 // Get all concerts
      }
    });
    
    if (response.data.data) {
      // Convert date strings to Date objects for comparison
      const processedData = response.data.data.map(concert => ({
        ...concert,
        parsedDate: new Date(concert.tanggal)
      }));
      konser.value = processedData;
      filteredKonser.value = processedData;
      extractUniqueCities();
    }
  } catch (error) {
    console.error("Error fetching concerts:", error);
  }
};

const extractUniqueCities = () => {
  const uniqueCities = new Set<string>();
  konser.value.forEach(concert => {
    if (concert.lokasi) {
      uniqueCities.add(concert.lokasi);
    }
  });
  cities.value = Array.from(uniqueCities).sort();
};

const filterKonserByCity = () => {
  if (!selectedCity.value) {
    // If no city selected, show all concerts
    filteredKonser.value = konser.value;
  } else {
    // Filter by selected city
    filteredKonser.value = konser.value.filter((concert) => {
      const concertCity = concert.konser?.lokasi?.toLowerCase() || '';
      const selectedCityValue = selectedCity.value?.toLowerCase() || '';
      return concertCity === selectedCityValue;
    });
  }

  // Apply search query filter if exists
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filteredKonser.value = filteredKonser.value.filter((concert) => {
      const title = concert.konser?.title?.toLowerCase() || '';
      return title.includes(query);
    });
  }
};

watch(selectedCity, (newCity) => {
  filterKonserByCity();
}, { immediate: true });

watch(searchQuery, (newQuery) => {
  filterKonserByCity();
}, { immediate: true });

const fetchCities = async () => {
  try {
    const response = await axios.get("/konser/cities");
    if (response.data.data) {
      cities.value = response.data.data.map(city => ({
        id: city.lokasi,
        text: city.lokasi
      }));
    }
  } catch (error) {
    console.error("Error fetching cities:", error);
    cities.value = [];
  }
};

const goToEventDetail = (eventData) => {
  // Check if eventData is a direct UUID or an object
  const uuid = typeof eventData === 'string' ? 
    eventData : 
    (eventData?.konser?.uuid || eventData?.uuid);

  if (!uuid) {
    console.error('UUID not found in event data:', eventData);
    return;
  }

  router.push(`/detail-konser/${uuid}`);
};

// Helper function to check if ticket data is empty/zero
const isTicketEmpty = (event: any) => {
  const gates = [
    event.gate_a_capacity,
    event.gate_b_capacity,
    event.gate_c_capacity,
    event.gate_d_capacity,
    event.gate_e_capacity
  ];

  const prices = [
    parseFloat(event.harga_regular) || 0,
    parseFloat(event.harga_vip) || 0
  ];

  const hasZeroGates = gates.every(gate => !gate || gate === 0);
  const hasZeroPrices = prices.every(price => price === 0);

  return hasZeroGates && hasZeroPrices;
};

const upcomingConcerts = computed(() => {
  const today = new Date();
  return konser.value.filter(event => {
    const concertDate = new Date(event.konser?.tanggal);
    return concertDate > today && isTicketEmpty(event);
  });
});

const availableConcerts = computed(() => {
  const today = new Date();
  return konser.value.filter(event => {
    const concertDate = new Date(event.konser?.tanggal);
    return concertDate > today && !isTicketEmpty(event);
  });
});

const pastConcerts = computed(() => {
  return konser.value.filter(concert => 
    new Date(concert.tanggal) < new Date()
  );
});

interface Banner {
  id: number
  image: string
  title?: string
}
const banners = ref<Banner[]>([])

const fetchBanners = async () => {
  try {
    const response = await axios.get('/banner')
    banners.value = response.data.data;

  } catch (error) {
    console.error('Error fetching banners:', error)
  }
}

fetchBanners()

const initializeSplide = () => {
  const element = document.getElementById('splide');
  if (!element) return;

  const splide = new Splide('#splide', {
    type: 'loop',
    speed: 1000,
    autoplay: true,
    interval: 3000,
    easing: 'ease',
    arrows: true,
    pagination: true,
    drag: true,
    perPage: 1,
    perMove: 1,
    pauseOnHover: true,
    rewind: true, 
    width: '100vw',
    fixedWidth: '100vw', 
    padding: '0',
    start: 0, // Start from first slide
  });

  splide.mount();

  // Trigger one automatic move after mounting
  setTimeout(() => {
    splide.go('+1'); // Move to next slide once
  }, 500); // Wait 500ms after mount
};

watch(banners, (newBanners) => {
  if (newBanners.length > 0) {
    nextTick(() => {
      initializeSplide();
    });
  }
});

onMounted(() => {
  getKonser();
  fetchCities();
});

const searchKonser = async () => {
  isLoading.value = true;
  try {
    // Build query parameters
    const params = new URLSearchParams();
    if (searchQuery.value) {
      params.append('search', searchQuery.value);
    }
    if (selectedCity.value) {
      params.append('kota', selectedCity.value);
    }

    const response = await axios.get("/konser", { 
      params: {
        search: searchQuery.value,
        kota: selectedCity.value,
        per_page: 999999
      }
    });

    if (response.data.data) {
      // Filter locally based on search query and selected city
      const data = response.data.data;
      filteredKonser.value = data.filter(concert => {
        const matchesSearch = !searchQuery.value || 
          concert.konser.title.toLowerCase().includes(searchQuery.value.toLowerCase());
        
        const matchesCity = !selectedCity.value || 
          concert.konser.lokasi.toLowerCase() === selectedCity.value.toLowerCase();
        
        return matchesSearch && matchesCity;
      });

      // Update available and upcoming concerts
      konser.value = filteredKonser.value;
    }
  } catch (error) {
    console.error("Error searching concerts:", error);
    toast.error("Gagal mencari konser");
  } finally {
    isLoading.value = false;
  }
};

const debounce = (fn: Function, delay: number) => {
  let timeoutId: NodeJS.Timeout;
  return function (...args: any[]) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => fn.apply(null, args), delay);
  };
};

const debouncedSearch = debounce(() => {
  searchKonser();
}, 300);

// Add pagination data
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = ref(8);

// Add load more function
const loadMore = async () => {
  if (currentPage.value >= totalPages.value) return;
  
  try {
    const nextPage = currentPage.value + 1;
    const response = await axios.get("/konser", {
      params: {
        per_page: perPage.value,
        page: nextPage
      }
    });
    
    if (response.data.data) {
      // Append new items to existing arrays
      konser.value = [...konser.value, ...response.data.data];
      filteredKonser.value = [...filteredKonser.value, ...response.data.data];
      currentPage.value = nextPage;
      totalPages.value = Math.ceil(response.data.total / perPage.value);
    }
  } catch (error) {
    console.error("Error loading more concerts:", error);
  }
};

// Add new refs for pagination control
const initialDisplayCount = 8; // 3 rows x 4 columns
const showAll = ref(false);

// Computed property for displayed concerts
const displayedKonser = computed(() => {
  if (showAll.value) {
    return filteredKonser.value;
  }
  return filteredKonser.value.slice(0, initialDisplayCount);
});

// Add method to toggle show more/less
const toggleShowMore = () => {
  showAll.value = !showAll.value;
  if (!showAll.value) {
    // Scroll back to the start of concerts section when showing less
    document.getElementById('semua-konser')?.scrollIntoView({ behavior: 'smooth' });
  }
};

// Add debug logging
watch([upcomingConcerts, availableConcerts], ([upcoming, available]) => {
  console.log('Upcoming concerts:', upcoming.length);
  console.log('Available concerts:', available.length);
});

// Update watchers
watch([searchQuery, selectedCity], () => {
  debouncedSearch();
}, { immediate: false });

// Add method to reset filters
const resetFilters = () => {
  searchQuery.value = '';
  selectedCity.value = '';
  searchKonser();
};
</script>

<template>
  <nav>
    <KTHeader />
  </nav>

  <main class="mt-20 ">
    <div class="mt-20">
    <div class="mt-20"></div>
      <div id="splide" class="splide ">
        <div class="splide__track">
          <ul class="splide__list">
            <li
              v-for="banner in banners"
              :key="banner.id"
              class="splide__slide"
            >
              <img
                :src="`${banner.image}`"
                :alt="banner.title || 'Banner Rekomendasi'"
                class="w-full h-[400px] object-cover"
              />
            </li>
          </ul>
        </div>
      </div>

    </div>

    <div v-if="!authStore.isAuthenticated" class="container mt-20">
      <div class="row justify-content-center align-items-center">
        <div class="mb-4">
          <div class="card p-10 me-5 d-flex flex-row align-items-center justify-content-between"
            style="color: white; border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <div class="text-start">
              <h4 class="fw-bold mb-3" style="font-family: 'Lobster', cursive;">Udah punya Akun belum?</h4>
              <h5 class="mb-3" style="font-family: 'Lobster', cursive;">Masuk ke akun Anda untuk memesan tiket dan
                menikmati pengalaman eksklusif.</h5>
            </div>
            <button @click="router.push('/sign-in')" class="btn btn-light text-primary fw-bold"
              style="border-radius: 20px;">
              Login <i class="fa-solid fa-right-to-bracket ms-1"></i>
            </button>
          </div>
        </div>
      </div>
    </div>


    <div class="text-center mt-15 fade-in">
      <h3 class="display-6 fw-bold mb-3">
        🎶 Temukan Sensasi Musik Terbaik di Konser Kami! 🎸
      </h3>
      <h3 class="display-7" style="color: #ffcc00; font-family: 'Lobster', cursive;">
        Nikmati pengalaman tak terlupakan dengan musisi favorit Anda di berbagai kota. 🎤✨
      </h3>
    </div>

    <div class="d-flex justify-content-center gap-5">
      <form class="d-flex justify-content-center mt-7" @submit.prevent="searchKonser">
        <div class="input-group mb-3" style="width: 650px;">
          <span class="input-group-text bg-secondary border-0" style="background-color: #ced4da !important;">
            <i class="fa fa-music"></i>
          </span>
          <input 
            v-model="searchQuery" 
            type="text" 
            class="form-control bg-secondary border-0 text-black"
            style="background-color: #ced4da !important;" 
            placeholder="Cari nama konser..." 
            aria-label="Search"
          >
          <button class="btn btn-secondary" type="submit" :disabled="isLoading">
            <i class="fa fa-search"></i>
          </button>
        </div>
      </form>

      <!-- Update city filter -->
      <div class="mt-7 d-flex gap-2">
        <select 
          v-model="selectedCity" 
          class="form-select" 
          style="min-width: 200px;"
        >
          <option value="">Semua Kota</option>
          <option 
            v-for="city in cities" 
            :key="city.id"
            :value="city.text"
          >
            {{ city.text }}
          </option>
        </select>

        <!-- Add reset button -->
        <button 
          v-if="searchQuery || selectedCity"
          @click="resetFilters" 
          class="btn btn-outline-secondary"
          title="Reset filter"
        >
          <i class="fas fa-undo-alt"></i>
        </button>
      </div>
    </div>

    <!-- Add loading indicator -->
    <div v-if="isLoading" class="text-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Add no results message -->
    <div v-else-if="filteredKonser.length === 0" class="text-center my-5">
      <p class="lead text-muted">
        Tidak ada konser yang ditemukan
        <span v-if="searchQuery || selectedCity">
          dengan filter yang dipilih
        </span>
      </p>
      <button @click="resetFilters" class="btn btn-outline-primary mt-2">
        Reset Filter
      </button>
    </div>

    <div class="container">
      <div class=" mt-18 mb-10">
        <h1 class=" fw-bold" style="font-family: 'Lobster', cursive;">
          Konser Yang Akan Datang
        </h1>
        <p class="text-muted">Konser yang akan segera dibuka penjualan tiketnya!</p>
      </div>

      <div class="row ms-2">
        <div v-if="upcomingConcerts.length === 0" class="text-center">
          <p>Tidak ada konser yang akan datang saat ini.</p>
        </div>
        <div v-for="event in upcomingConcerts" :key="event.uuid" class="col-lg-3 col-md-4 col-sm-6 mb-4">
          <div class="card recommended-concert-card" @click="goToEventDetail(event.konser.uuid)">
            <div class="card-img-wrapper">
              <img :src="event.konser.image" class="card-img-top" alt="Upcoming Event Image">
              <div class="upcoming-badge">Coming Soon</div>
            </div>
            <div class="card-body">
              <h5 class="card-title">{{ event.konser.title }}</h5>
              <p class="card-text mb-1">
                <small class="fs-7 fw-bold">
                  <i class="fa-solid fa-calendar-days me-1" style="color: #ffcc00;"></i> {{ event.konser.tanggal }}
                </small>
              </p>
              <p class="card-text mb-2">
                <small class="fs-7 fw-bold">
                  <i class="fa-solid fa-location-dot me-1" style="color: green;"></i> {{ event.konser.lokasi }}
                </small>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      <div class=" mt-20 mb-10">
        <h1 class=" fw-bold" style="font-family: 'Lobster', cursive;">
          Konser Yang Tersedia
        </h1>
        <p class="text-muted">Konser yang sedang tersedia untuk dibeli!</p>
      </div>

      <div class="row ms-2">
        <div v-if="availableConcerts.length === 0" class="text-center">
          <p>Tidak ada konser tersedia saat ini.</p>
        </div>
        <div v-for="event in availableConcerts" :key="event.uuid" class="col-lg-3 col-md-4 col-sm-6 mb-4">
          <div class="card concert-card" @click="goToEventDetail(event.konser.uuid)">
            <div class="card-img-wrapper">
              <img :src="event.konser.image" class="card-img-top" alt="Event Image">
              <div class="available-badge">Tersedia</div>
            </div>
            <div class="card-body">
              <h5 class="card-title">{{ event.konser.title }}</h5>
              <p class="card-text mb-1">
                <small class="fs-7 fw-bold">
                  <i class="fa-solid fa-calendar-days me-1" style="color: #ffcc00;"></i> {{ event.konser.tanggal }}
                </small>
              </p>
              <p class="card-text mb-2">
                <small class="fs-7 fw-bold">
                  <i class="fa-solid fa-location-dot me-1" style="color: green;"></i> {{ event.konser.lokasi }}
                </small>
              </p>
              <div class="d-flex justify-content-between align-items-center">
                <p class="card-text mb-0">
                  Rp {{ event.harga_regular }}
                </p>
                <a @click.stop="goToEventDetail(event.konser.uuid)" class="btn btn-primary btn-sm">
                  Tickets <i class="fa-solid fa-ticket ms-1"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modify the Semua Konser section -->
    <div id="semua-konser" class="container">
      <div class="mt-20 mb-10">
        <h1 class="fw-bold" style="font-family: 'Lobster', cursive;">
          Semua Konser
        </h1>
        <p class="text-muted">Jelajahi berbagai pilihan konser menarik di berbagai kota!</p>
      </div>

      <div class="row mt-15">
        <div v-if="filteredKonser.length === 0" class="text-center">
          <p>Tidak ada konser yang tersedia saat ini.</p>
        </div>
        <div v-for="event in displayedKonser" :key="event.id" class="col-lg-3 col-md-4 col-sm-6 mb-4">
          <div class="card concert-card" @click="goToEventDetail(event.konser.uuid)">
            <div class="card-img-wrapper">
              <img :src="`${event.konser.image}`" class="card-img-top" alt="Event Image">
            </div>
            <div class="card-body">
              <h5 class="card-title">{{ event.konser.title }}</h5>
              <p class="card-text mb-1">
                <small class="fs-7 fw-bold">
                  <i class="fa-solid fa-calendar-days me-1" style="color: #ffcc00;"></i> {{ event.konser.tanggal }}
                </small>
              </p>
              <p class="card-text mb-2">
                <small class="fs-7 fw-bold">
                  <i class="fa-solid fa-location-dot me-1" style="color: green;"></i> {{ event.konser.lokasi }}
                </small>
              </p>
              <div class="d-flex justify-content-between align-items-center">
                <p class="card-text mb-0"> {{ event.konser.harga }}</p>
                <a @click.stop="goToEventDetail(event.konser.uuid)" class="btn btn-primary btn-sm">
                  Tickets <i class="fa-solid fa-ticket ms-1"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Show More/Less Button -->
      <div v-if="filteredKonser.length > initialDisplayCount" class="text-center mt-4 mb-4">
        <button @click="toggleShowMore" class="btn btn-outline-primary">
          {{ showAll ? 'Show Less' : 'Show More' }}
          <i :class="showAll ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="ms-2"></i>
        </button>
      </div>
    </div>

    <div class="mt-20 text-center mb-20">
      <h2 class="display-6 fw-bold" style="font-family: 'Lobster', cursive;">Eksplorasi Kota dan Musik Bersama Kami!
      </h2>
    </div>
  </main>
  <Footer />
</template>

<style scoped>
.splide-3d {
  perspective: 1000px;
  overflow: hidden;
}

.splide__slide {
  transition: transform 0.5s ease, opacity 0.5s ease;
}

.splide__slide.is-active {
  transform: scale(1) translateZ(50px) rotateY(0deg);
  opacity: 1;
}

.splide__slide:not(.is-active) {
  transform: scale(0.8) translateZ(-100px) rotateY(-15deg);
  opacity: 0.7;
}

.splide__list {
  transform-style: preserve-3d;
}

.splide__arrow {
  background-color: #000;
  color: #fff;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.splide__pagination__page {
  background-color: #000;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  margin: 0 5px;
}

.splide__pagination__page.is-active {
  background-color: #f00;
}

.splide__slide img {
  width: 100%;
  height: 580px;
  object-fit: fill;
  border-radius: 8px;
}

/* Animated Background Gradient */
.concert-landing {
  background: linear-gradient(270deg, #1a1a1a, #2d2d2d, #1a1a1a);
  background-size: 600% 600%;
  animation: gradientShift 15s ease infinite;
}

.splide {
  width: 100%;
  max-width: 100vw;
  overflow: hidden;
  margin-top: 5%;
}

@keyframes gradientShift {
  0% {
    background-position: 0% 50%
  }

  50% {
    background-position: 100% 50%
  }

  100% {
    background-position: 0% 50%
  }
}

/* Enhanced Fade In Animation */
.fade-in {
  animation: fadeIn 1.5s ease-out forwards;
  opacity: 0;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Staggered Card Entrance */
.concert-card,
.recommended-concert-card {
  opacity: 0;
  animation: cardEntrance 0.8s ease forwards;
}

.concert-card:nth-child(1) {
  animation-delay: 0.1s;
}

.concert-card:nth-child(2) {
  animation-delay: 0.2s;
}

.concert-card:nth-child(3) {
  animation-delay: 0.3s;
}

.concert-card:nth-child(4) {
  animation-delay: 0.4s;
}

@keyframes cardEntrance {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(30px);
  }

  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

/* Enhanced Card Hover Effects */
.concert-card,
.recommended-concert-card {
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.concert-card:hover,
.recommended-concert-card:hover {
  transform: translateY(-10px) rotate(1deg);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
}

/* Pulsing Recommended Badge */
.recommended-badge {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }

  50% {
    transform: scale(1.05);
  }

  100% {
    transform: scale(1);
  }
}

/* Animated Search Bar */
.input-group {
  transition: all 0.3s ease;
}

.input-group:focus-within {
  transform: scale(1.02);
  box-shadow: 0 0 20px rgba(255, 204, 0, 0.2);
}

/* Button Hover Effects */
.btn {
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
}

.btn::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  transform: translate(-50%, -50%);
  transition: width 0.6s ease, height 0.6s ease;
}

.btn:hover::after {
  width: 200%;
  height: 200%;
}

/* Floating Icons Animation */
.fa-music,
.fa-calendar-days,
.fa-location-dot {
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-5px);
  }

  100% {
    transform: translateY(0);
  }
}

/* Smooth Scroll Behavior */
html {
  scroll-behavior: smooth;
}

/* Image Hover Zoom Effect */
.card-img-wrapper {
  overflow: hidden;
}

.card-img-top {
  transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.card-img-wrapper:hover .card-img-top {
  transform: scale(1.1) rotate(-2deg);
}

/* Price Tag Shake Animation */


@keyframes shake {
  0% {
    transform: translateX(0);
  }

  25% {
    transform: translateX(-5px);
  }

  75% {
    transform: translateX(5px);
  }

  100% {
    transform: translateX(0);
  }
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.recommended-concert-card {
  position: relative;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
  border: none;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 95%;
  height: 100%;
}

.recommended-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background-color: #ffcc00;
  color: black;
  padding: 5px 10px;
  border-radius: 20px;
  font-weight: bold;
  font-size: 0.8rem;
  z-index: 10;
}

.recommended-concert-card:hover {
  transform: scale(1.04) rotate(1.5deg);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.concert-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
  border: none;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 95%;
  height: 100%;
  margin-left: auto;
  /* This ensures the card aligns to the right */
  margin-right: 0;
  /* This removes the right margin */
}

.row.justify-content-start {
  margin-right: 0;
  /* Removes right margin from the row */
  margin-left: 0;
  /* Removes left margin from the row */
}

.concert-card:hover {
  transform: scale(1.04) rotate(1.5deg);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.card-img-wrapper {
  height: 150px;
  overflow: hidden;
  position: relative;
}

.card-img-top {
  width: 100%;
  height: 100%;
  object-fit: fill;
  transition: transform 0.3s;
}

.concert-card:hover .card-img-top,
.recommended-concert-card:hover .card-img-top {
  transform: scale(1.05);
}

.card-body {
  padding: 0.75rem;
}

.card-title {
  font-size: 1.4rem;
  margin-bottom: 0.5rem;
  font-family: var(--font-family-base);
}

.btn-primary {
  background-color: #007bff;
  border: none;
  transition: background-color 0.3s;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.btn-secondary {
  transition: transform 0.3s ease, background-color 0.3s ease;
}

.btn-secondary:hover {
  transform: scale(1.1);
  background-color: #0069d9;
}

.upcoming-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background-color: #ffc107;
  color: #000;
  padding: 5px 10px;
  border-radius: 20px;
  font-weight: bold;
  font-size: 0.8rem;
  z-index: 10;
  animation: pulse 2s infinite;
}

.available-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background-color: #28a745;
  color: #fff;
  padding: 5px 10px;
  border-radius: 20px;
  font-weight: bold;
  font-size: 0.8rem;
  z-index: 10;
}

/* Add smooth transition for cards */
.row > div {
  transition: all 0.3s ease-in-out;
}

/* Optional: Add animation for new cards */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.row > div:nth-child(n+13) {
  animation: fadeIn 0.5s ease-in-out;
}

.card-title, h1, h2, h3, h4, h5, h6 {
  font-family: var(--font-family-base);
}

h1, .h1 {
  font-weight: 800;
  letter-spacing: -0.02em;
}

h2, .h2 {
  font-weight: 700;
  letter-spacing: -0.01em;
}

h3, .h3 {
  font-weight: 600;
}

.display-6 {
  font-weight: 700;
  letter-spacing: -0.02em;
}

.card-title {
  font-weight: 600;
  letter-spacing: -0.01em;
}

.btn {
  letter-spacing: 0.02em;
  height: 40px;
}

/* Update other specific text styles */
.text-muted {
  font-weight: 400;
}

.fw-bold {
  font-weight: 700 !important;
}

.fs-6 {
  font-weight: 500;
}

.badge {
  font-weight: 600;
  letter-spacing: 0.03em;
}

/* Add styles for filter components */
.form-select {
  border-radius: 0.5rem;
  height: 40px;
  border: 1px solid #ced4da;
  transition: all 0.3s ease;
}

.form-select:focus {
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.btn-outline-secondary {
  border-radius: 0.5rem;
  transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
  background-color: #6c757d;
  color: white;
}
</style>