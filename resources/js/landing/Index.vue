<script setup lang="ts">
import KTHeader from "@/layouts/default-layout/components/header/NavbarLanding.vue";
import Footer from "@/Layouts/default-layout/components/footer/Footer.vue";
import { useRoute, useRouter } from "vue-router";
import { ref, onMounted } from "vue";
import axios from "@/libs/axios";
import Splide from "@splidejs/splide";
import "@splidejs/splide/dist/css/splide.min.css";

const route = useRoute();
const router = useRouter();
const konser = ref([]);
const cities = ref<string[]>([]);
const city = ref<string[]>([]);
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

onMounted(() => {
  new Splide("#splide", {
    type: "loop",
    autoplay: true,
    interval: 2500,
    arrows: true,
    pagination: false,
    drag: true,
  }).mount();
  getKonser();
  fetchCities();
});
</script>

<template>
    <nav>
        <KTHeader />
    </nav>

    <main class="">
      <div class="">
        <div id="splide" class="splide">
          <div class="splide__track">
            <ul class="splide__list">
              <li class="splide__slide">
                <img src="/media/musik/rockmusic.jpg" alt="Rock Music" style="object-fit: fill; height: 580px; width: 100%;" />
              </li>
              <li class="splide__slide">
                <img src="/media/musik/eventjazz.jpg" alt="Festival" style="object-fit: fill; height: 580px; width: 100%;" />
              </li>
              <li class="splide__slide">
                <img src="/media/musik/festval.jpg" alt="Festival" style="object-fit: fill; height: 580px; width: 100%;" />
              </li>
              <li class="splide__slide">
                <img src="/media/musik/event.jpg" alt="Event" style="object-fit: fill; height: 580px; width: 100%;" />
              </li>
            </ul>
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

      <div class="d-flex  justify-content-center gap-5">
      <form class="d-flex justify-content-center mt-10">
        <div class="input-group mb-3" style="width: 650px;">
          <span class="input-group-text bg-secondary border-0"
          style="background-color: #ced4da !important;" >
            <i class="fa fa-music"></i>
          </span>
          <input type="text" class="form-control bg-secondary border-0" 
                style="background-color: #ced4da !important;" 
                placeholder="Cari Konser . . ." aria-label="Search" aria-describedby="button-addon2">
          <button class="btn btn-secondary" type="button" id="button-addon2">
            <i class="fa fa-search"></i>
          </button>
        </div>
      </form>
      <div class="mt-10">
        <select
          v-model="selectedCity"
          @change="filterKonserByCity"
          class="form-select"
          placeholder="Pilih Kota"
        >
          <option value="">Semua Kota</option>
          <option v-for="city in cities" :key="city" :value="city">
            {{ city.lokasi }}
          </option> 
        </select>
      </div>
    </div>  

      <div>


    <!-- <div class="mt-5">
      <h4>Konser di {{ selectedCity || 'Semua Kota' }}</h4>
      <div v-if="cities.length > 0" class="row g-3">
        <div v-for="concert in cities" :key="concert.id" class="col-md-3">
          <div class="card bg-gray-100">
            <div class="card-body">
              <h5>{{ concert.name }}</h5>
              <p>{{ concert.city }} - {{ concert.date }}</p>
            </div>
          </div>
        </div>
      </div>
      <p v-else>Tidak ada konser tersedia di kota ini.</p>
    </div> -->
  </div>
      

      <div class="container">
        

        <div class="row mt-15 justify-content-center">
          <div v-if="filteredKonser.length === 0" class="text-center">
            <p>No concerts available at the moment.</p>
          </div>
          <div v-for="event in filteredKonser" :key="event.id" class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card concert-card" @click="goToEventDetail(event.id)">
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
                  <a @click.stop="goToEventDetail(event.id)" class="btn btn-primary btn-sm">
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
        <!-- <h2 class="lead display-7 fw-bold" style="color: #ffcc00; font-family: 'Lobster', cursive;">Selain konser, temukan juga tempat-tempat menarik di kota Anda.</h2> -->
        <!-- <div class="row mt-4 justify-content-center card-container">
          <div v-for="(location, index) in locations" :key="index" class="col-md-2 mb-4 card card-kota">
            <div class=" icon">
              <img :src="location.image" class="card-img-top" alt="Icon">
              <div class="card-body">
                <h5 class="card-title" style="font-family: 'Lobster', cursive;">{{ location.name }}</h5>
              </div>
            </div>
          </div>
        </div> -->
      </div>
    </main> 
    <Footer/>
</template>

<style scoped>

.fade-in {
  animation: fadeIn 1s ease-in forwards; /* Fade-in effect */
  opacity: 0; /* Mulai dengan opacity 0 */
}

.container {
  max-width: 1200px;
  margin: 0 auto;
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
  transform: scale(1.04) rotate(1.5deg); /* Putar sedikit saat hover */
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.card-img-wrapper {
  height: 150px; /* Tinggi gambar dalam card lebih kecil */
  overflow: hidden;
}

.card-img-top {
  width: 100%;
  height: 100%;
  object-fit: fill;
  transition: transform 0.3s;
}

.concert-card:hover .card-img-top {
  transform: scale(1.05);
}

.card-body {
  padding: 0.75rem; /* Padding lebih kecil */
}

.card-title {
  font-size: 1.4rem; /* Ukuran font lebih kecil */
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
  transform: scale(1.1); /* Membesarkan tombol saat hover */
  background-color: #0069d9; /* Warna latar belakang saat hover */
} 
</style>