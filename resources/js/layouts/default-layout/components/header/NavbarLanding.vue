<script setup lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { computed, ref } from "vue";
import KTUserMenu from "@/layouts/default-layout/components/menus/UserAccountMenu.vue";
import KTThemeModeSwitcher from "@/layouts/default-layout/components/theme-mode/ThemeModeSwitcher.vue";
import { ThemeModeComponent } from "@/assets/ts/layout";
import { useThemeStore } from "@/stores/theme";
import { useAuthStore } from "@/stores/auth";

const store = useThemeStore();
const { user } = useAuthStore();

const themeMode = computed(() => {
  if (store.mode === "system") {
    return ThemeModeComponent.getSystemMode();
  }
  return store.mode;
});

const isAdmin = computed(() => user && user.role === "admin"); 
</script>

<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand ms-10" href="#">
        <img src="/media/musik/logo.png" alt="Logo" class="navbar-logo">
      </a>

      <!-- Navbar toggler for mobile -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
        aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse me-10 mt-2" id="navbarContent">
        <div class="me-auto"></div>

        <div class="d-flex flex-column align-items-end me-3">
          <div class="">
            <!-- Theme switcher -->
            <div class="me-3 d-inline-block">
              <a href="#"
                class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px"
                data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent"
                data-kt-menu-placement="bottom-end">
                <KTIcon v-if="themeMode === 'light'" icon-name="night-day" icon-class="fs-2" />
                <KTIcon v-else icon-name="moon" icon-class="fs-2" />
              </a>
              <KTThemeModeSwitcher />
            </div>
          </div>

          <!-- Navigation items -->
          <ul class="navbar-nav">
            <li class="nav-item">
              <router-link to="/" class="nav-link" active-class="active">Home</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/riwayat-konser" class="nav-link" active-class="active">Riwayat Konser</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/kontak" class="nav-link" active-class="active">Kontak</router-link>
            </li>
            <!-- Tampilkan Dashboard jika user adalah admin -->
            <li class="nav-item">
              <router-link to="/dashboard" class="nav-link" active-class="active">Dashboard</router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
</template>

<style scoped>

.navbar {
  position: relative; /* Untuk memungkinkan penggunaan pseudo-element */
  z-index: 1; /* Pastikan navbar berada di atas elemen lainnya */
}

.navbar::after {
  content: "";
  position: absolute;
  bottom: -1px; /* Menempatkan sedikit di bawah navbar */
  left: 0;
  width: 100%;
  height: 1.2px; /* Sedikit lebih kecil dari navbar */
   /* Warna putih di bawah navbar */
  border-radius: 0 0 100px 100px; /* Rounded mengikuti navbar */
  z-index: -1; /* Membuatnya berada di bawah navbar */
}

.navbar-logo {
  height: 60px; /* Adjust this value to make the logo larger */
}

.nav-link {
  color: #ffffff;
  font-family: Lobster, cursive;
  font-size: 14px;
  transition: color 0.3s ease, text-shadow 0.3s ease;
  position: relative;
}

.nav-link::before {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 2px;
  background-color: #d4bb00;
  transform: scaleX(0);
  transform-origin: bottom right;
  transition: transform 0.3s ease-out;
}

.nav-link.active {
  color: #d4bb00; /* Warna saat active */
  text-shadow: 2px 2px 8px rgba(255, 255, 255, 0.3);
}

.nav-link:hover::before {
  transform: scaleX(1);
  transform-origin: bottom left;
}

.nav-link:hover {
  color: #d4bb00;
  text-shadow: 2px 2px 8px rgba(255, 255, 255, 0.3);
}
.nav-item {
  margin-left: 20px;
}

.navbar-nav {
  display: flex;
  align-items: center;
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
  .navbar-nav {
    flex-direction: row;
    justify-content: flex-end;
  }

  .nav-item {
    padding-left: 1rem;
  }
}
</style>