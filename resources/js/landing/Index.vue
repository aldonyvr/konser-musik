<script setup lang="ts">
import KTHeader from "@/layouts/default-layout/components/header/NavbarLanding.vue";
import Footer from "@/Layouts/default-layout/components/footer/Footer.vue";
import axios from "@/libs/axios";
import { useRoute, useRouter } from "vue-router";
import { ref, onMounted, computed } from "vue";
import Splide from "@splidejs/splide";
import "@splidejs/splide/dist/css/splide.min.css";

const route = useRoute();
const router = useRouter();
const konser = ref([]);
const cities = ref<string[]>([]);
const selectedCity = ref<string | null>(null);
const filteredKonser = ref([]);

const getKonser = async () => {
  try {
    const response = await axios.get("/konser");
    konser.value = response.data.data;
    filteredKonser.value = konser.value;
  } catch (error) {
    console.error("Error fetching concerts:", error);
  }
};

const fetchCities = async () => {
  try {
    const response = await axios.get("/konser/cities");
    console.log('Cities Response:', response.data);

    if (response.data.data) {
      cities.value = response.data.data;
    } else if (Array.isArray(response.data)) {
      cities.value = response.data;
    } else {
      console.error('Unexpected response structure:', response.data);
      cities.value = [];
    }
  } catch (error) {
    console.error("Error fetching cities:", error);
    cities.value = [];
  }
};

const filterKonserByCity = () => {
  if (selectedCity.value) {
    filteredKonser.value = konser.value.filter(
      (concert) =>
        concert.lokasi.toLowerCase() === selectedCity.value?.toLowerCase()
    );
  } else {
    filteredKonser.value = konser.value;
  }
};

const goToEventDetail = (id: number) => {
  router.push(`/detail-konser/${id}`);
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

onMounted(() => {
  new Splide('#splide', {
    type: 'loop', 
    speed: 1000, 
    autoplay: true,
    interval: 3000,
    easing: 'ease',
    arrows: false, 
    pagination: true, 
  }).mount();
  getKonser();
  fetchCities();
});
</script>

<template>
  <nav>
    <KTHeader />
  </nav>

  <main class="mt-6">
    <div class="mt-20">
      <div id="splide" class="splide">
        <div class="splide__track">
          <ul class="splide__list">
            <li class="splide__slide">
              <img src="/media/musik/rockmusic.jpg" alt="Rock Music"
                style="object-fit: fill; height: 580px; width: 100%;" />
            </li>
            <li class="splide__slide">
              <img src="/media/musik/eventjazz.jpg" alt="Festival"
                style="object-fit: fill; height: 580px; width: 100%;" />
            </li>
            <li class="splide__slide">
              <img src="/media/musik/festval.jpg" alt="Festival"
                style="object-fit: fill; height: 580px; width: 100%;" />
            </li>
            <li class="splide__slide">
              <img src="/media/musik/event.jpg" alt="Event" style="object-fit: fill; height: 580px; width: 100%;" />
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="container mt-20">
      <div class="row justify-content-center align-items-center">
        <div class=" mb-4">
          <div class="card p-10 me-5 d-flex flex-row align-items-center justify-content-between" style="color: white; border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <div class="text-start">
              <h4 class="fw-bold mb-3" style="font-family: 'Lobster', cursive;">Udah punya Akun belum?</h4>
              <h5 class="mb-3" style="font-family: 'Lobster', cursive;">Masuk ke akun Anda untuk memesan tiket dan menikmati pengalaman eksklusif.</h5>
            </div>
            <button @click="router.push('/sign-in')" class="btn btn-light text-primary fw-bold" style="border-radius: 20px;">
              Login <i class="fa-solid fa-right-to-bracket ms-1"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    
    <div class="text-center mt-15 fade-in">
      <h3 class="display-6 fw-bold mb-3" style="font-family: 'Lobster', cursive;">
        🎶 Temukan Sensasi Musik Terbaik di Konser Kami! 🎸
      </h3>
      <h3 class="display-7" style="color: #ffcc00; font-family: 'Lobster', cursive;">
        Nikmati pengalaman tak terlupakan dengan musisi favorit Anda di berbagai kota. 🎤✨
      </h3>
    </div>
    
    <div class="d-flex justify-content-center gap-5">
      <form class="d-flex justify-content-center mt-7">
        <div class="input-group mb-3" style="width: 650px;">
          <span class="input-group-text bg-secondary border-0" style="background-color: #ced4da !important;">
            <i class="fa fa-music"></i>
          </span>
          <input type="text" class="form-control bg-secondary border-0" style="background-color: #ced4da !important;"
            placeholder="Cari Konser . . ." aria-label="Search" aria-describedby="button-addon2">
          <button class="btn btn-secondary" type="button" id="button-addon2">
            <i class="fa fa-search"></i>
          </button>
        </div>
      </form>
      <div class="mt-7">
        <select v-model="selectedCity" @change="filterKonserByCity" class="form-select" placeholder="Pilih Kota">
          <option value="">Semua Kota</option>
          <option v-for="city in cities" :key="city" :value="city">
            {{ city.lokasi }}
          </option>
        </select>
      </div>
    </div>
    
    <div class="container">
      <div class=" mt-18 mb-10">
        <h1 class=" fw-bold" style="font-family: 'Lobster', cursive;">
          Konser Rekomendasi Spesial 
        </h1>
        <p class="text-muted">Jangan lewatkan konser spektakuler yang akan segera hadir!</p>
      </div>

      <div class="row">
        <div v-for="event in recommendedKonser" :key="event.id" class="col-lg-3 col-md-4 col-sm-6 mb-4">
          <div class="card recommended-concert-card" @click="goToEventDetail(event.uuid)">
            <div class="card-img-wrapper">
              <img :src="`${event.image}`" class="card-img-top" alt="Recommended Event Image">
              <div class="recommended-badge">Recommended</div>
            </div>
            <div class="card-body">
              <h5 class="card-title">{{ event.title }}</h5>
              <p class="card-text mb-1">
                <small class="fs-7 fw-bold">
                  <i class="fa-solid fa-calendar-days me-1" style="color: #ffcc00;"></i> {{ event.tanggal }}
                </small>
              </p>
              <p class="card-text mb-2">
                <small class="fs-7 fw-bold">
                  <i class="fa-solid fa-location-dot me-1" style="color: green;"></i> {{ event.lokasi }}
                </small>
              </p>
              <div class="d-flex justify-content-between align-items-center">
                <p class="card-text mb-0">Rp. {{ event.harga }}</p>
                <a @click.stop="goToEventDetail(event.uuid)" class="btn btn-primary btn-sm">
                  Tickets <i class="fa-solid fa-ticket ms-1"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="x mt-20 mb-10">
        <h1 class=" fw-bold" style="font-family: 'Lobster', cursive;">
           Semua Konser 
        </h1>
        <p class="text-muted">Jelajahi berbagai pilihan konser menarik di berbagai kota!</p>
      </div>

      <div class="row mt-15 justify-content-center">
        <div v-if="filteredKonser.length === 0" class="text-center">
          <p>No concerts available at the moment.</p>
        </div>
        <div v-for="event in filteredKonser" :key="event.id" class="col-lg-3 col-md-4 col-sm-6 mb-4">
          <div class="card concert-card" @click="goToEventDetail(event.uuid)">
            <div class="card-img-wrapper">
              <img :src="`${event.image}`" class="card-img-top" alt="Event Image">
            </div>
            <div class="card-body">
              <h5 class="card-title">{{ event.title }}</h5>
              <p class="card-text mb-1">
                <small class="fs-7 fw-bold">
                  <i class="fa-solid fa-calendar-days me-1" style="color: #ffcc00;"></i> {{ event.tanggal }}
                </small>
              </p>
              <p class="card-text mb-2">
                <small class="fs-7 fw-bold">
                  <i class="fa-solid fa-location-dot me-1" style="color: green;"></i> {{ event.lokasi }}
                </small>
              </p>
              <div class="d-flex justify-content-between align-items-center">
                <p class="card-text mb-0">Rp. {{ event.harga }}</p>
                <a @click.stop="goToEventDetail(event.uuid)" class="btn btn-primary btn-sm">
                  Tickets <i class="fa-solid fa-ticket ms-1"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-20 text-center mb-20">
      <h2 class="display-6 fw-bold" style="font-family: 'Lobster', cursive;">Eksplorasi Kota dan Musik Bersama Kami!</h2>
    </div>
  </main>
  <Footer />
</template>

<style scoped>
.fade-in {
  animation: fadeIn 1s ease-in forwards;
  opacity: 0;
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
  font-family: 'Lobster', cursive;
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
</style>