<template>
  <nav>
    <KTHeader />
  </nav>

  <div class="contact-section py-5">
    <div class="container">
      <div class="row justify-content-center">
        <!-- Contact Information Card -->
        <div class="col-lg-4">
          <div class="card h-100 shadow border-0">
            <div class="card-body">
              <h4 class="card-title mb-4">Informasi Kontak</h4>

              <div class="d-flex align-items-center mb-4">
                <div class="bg-light rounded-circle p-3 me-3">
                  <KTIcon icon-name="phone" icon-class="fs-2 text-primary" />
                </div>
                <div>
                  <h6 class="mb-1">Telepon</h6>
                  <p class="mb-0 text-muted">+62 851-1234-5678</p>
                </div>
              </div>

              <div class="d-flex align-items-center mb-4">
                <div class="bg-light rounded-circle p-3 me-3">
                  <i class="bi bi-envelope-fill fs-4 text-primary"></i>
                </div>
                <div>
                  <h6 class="mb-1">Email</h6>
                  <p class="mb-0 text-muted">admkonser2025@gmail.com</p>
                </div>
              </div>

              <div class="d-flex align-items-center mb-4">
                <div class="bg-light rounded-circle p-3 me-3">
                  <KTIcon icon-name="map" icon-class="fs-2 text-primary" />
                </div>
                <div>
                  <h6 class="mb-1">Alamat</h6>
                  <p class="mb-0 text-muted">Jl. Musik No. 123, Jakarta Selatan</p>
                </div>
              </div>

              <h5 class="mt-5 mb-3">Ikuti Kami</h5>
              <div class="d-flex gap-3">
                <a href="#" class="btn btn-light rounded-circle">
                  <i class="bi bi-facebook"></i>
                </a>
                <a href="#" class="btn btn-light rounded-circle">
                  <i class="bi bi-instagram"></i>
                </a>
                <a href="#" class="btn btn-light rounded-circle">
                  <i class="bi bi-twitter"></i>
                </a>
              </div>

              <!-- Register for a Concert Section -->
              <h4 class="mt-5 mb-4">Ingin Daftarkan Konser Anda?</h4>
              <p class="text-muted">
                Hubungi kami melalui WhatsApp untuk mendaftarkan konser Anda dengan cepat dan mudah.
              </p>
              <a 
                href="https://wa.me/6281234567890?text=Halo,%20saya%20ingin%20mendaftarkan%20konser."
                target="_blank"
                class="btn btn-success w-100 d-flex align-items-center justify-content-center"
              >
                <i class="bi bi-whatsapp me-2"></i> Hubungi via WhatsApp
              </a>
            </div>
          </div>
        </div>

        <!-- Concert Description Section -->
        <div class="col-lg-6 ms-lg-4">
          <div class="concert-description bg-light p-4 rounded shadow">
            <h4 class="mb-4">Tentang Konser</h4>
            <p class="text-muted">
              Kami menghadirkan berbagai konser musik dari berbagai genre untuk memenuhi hasrat pecinta musik. Berikut adalah beberapa konser yang akan datang:
            </p>
            <div v-for="event in recommendedKonser" :key="event.id" class="mb-4">
            <ul class="list-unstyled">
              <li class="mb-3">
                <strong>{{ event.title }}</strong> - {{ event.tanggal }}<br />
                <span class="text-muted">Lokasi: {{ event.lokasi }}</span>
              </li>
            </ul>
          </div>
          </div>
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
  min-height: 100vh;
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
</style>
