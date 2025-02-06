<template>
  <nav
    class="navbar navbar-expand-lg navbar-force-dark fixed-top"
    :class="{ 
      'navbar-dark bg-dark': true,
      'navbar-light bg-light': false
    }"
  >
    <div class="container-fluid">
      <a class="navbar-brand ms-4" href="#">
        <img src="/media/musik/logo.png" alt="Logo" class="navbar-logo">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
        aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse mx-4" id="navbarContent">
        <!-- Centered Navigation Links -->
        <ul class="navbar-nav mx-auto">
          <li class="nav-item">
            <router-link to="/" class="nav-link" active-class="active">Home</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/blog" class="nav-link" active-class="active">Blog</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/riwayat-konser" class="nav-link" active-class="active">Riwayat Konser</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/kontak" class="nav-link" active-class="active">Kontak Kami</router-link>
          </li>
          <li class="nav-item" v-if="isAdmin">
            <router-link to="/dashboard" class="nav-link" active-class="active">Dashboard</router-link>
          </li>
        </ul>

        <!-- Right-aligned Controls -->
        <div class="d-flex align-items-center">
          <!-- Theme Toggle -->
          <button @click="toggleTheme" 
            class="btn btn-icon btn-custom theme-toggle me-3">
            <KTIcon v-if="themeMode === 'light'" icon-name="night-day" icon-class="fs-2" />
            <KTIcon v-else icon-name="moon" icon-class="fs-2" />
          </button>

          <!-- Login Button -->
          <div v-if="!authStore.isAuthenticated">
            <router-link
              to="/sign-in"
              class="login-btn"
            >
              Login Account <i class="bi bi-arrow-return-right ms-1 text-white mt-1"></i>
            </router-link>
          </div>

          <!-- Profile Dropdown -->
          <div v-else class="dropdown">
            <button 
              class="btn btn-icon profile-btn dropdown-toggle"
              type="button"
              id="profileDropdown"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <div v-if="authStore.user?.photo" class="profile-image-container">
                <img 
                  :src="`${authStore.user.photo}`" 
                  alt="`${authStore.user.photo}`"
                  class="profile-image"
                >
              </div>
              <template v-else>
                <KTIcon icon-name="user" icon-class="fs-2" />
              </template>
            </button>
            
            <!-- Profile Dropdown Menu -->
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
              <li class="px-6 py-2">
                <div class="d-flex align-items-center">
                  <div class="symbol symbol-50px me-3">
                    <img 
                      v-if="authStore.user?.photo" 
                      :src="`${authStore.user.photo}`" 
                      alt="profile"
                      class="rounded-circle"
                    >
                    <span v-else class="symbol-label">
                      <KTIcon icon-name="user" icon-class="fs-2" />
                    </span>
                  </div>
                  <div class="d-flex flex-column">
                    <span class="fw-bolder fs-5">{{ authStore.user?.name }}</span>
                    <span class="text-muted fs-7">{{ authStore.user?.email }}</span>
                  </div>
                </div>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <router-link class="dropdown-item" to="/profile">
                  Profile Saya
                </router-link>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item" href="#" @click.prevent="handleSignOut">
                  Keluar
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import { useThemeStore } from "@/stores/theme";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";
import axios from "@/libs/axios";
import { ThemeModeComponent } from "@/assets/ts/layout";

const router = useRouter();
const themeStore = useThemeStore();
const authStore = useAuthStore();
const isLoggedIn = ref(false);
const currentUser = ref(null);

const checkAuthStatus = async () => {
  try {
    const response = await axios.get('/auth/me');
    authStore.user = response.data; // Pastikan data diperbarui di authStore
    console.log("User data:", response.data); // Debugging
    isLoggedIn.value = true;
  } catch (error) {
    console.error("Error fetching auth status:", error);
    isLoggedIn.value = false;
    authStore.user = null;
  }
};


const themeMode = computed(() => {
  return themeStore.mode === "system" 
    ? ThemeModeComponent.getSystemMode() 
    : themeStore.mode;
});

const isAdmin = computed(() => {
  return authStore.user?.role_id === 1; 
});


const toggleTheme = () => {
  const newMode = themeMode.value === 'light' ? 'dark' : 'light';
  themeStore.setThemeMode(newMode);
  document.body.classList.remove('light-mode', 'dark-mode');
  document.body.classList.add(`${newMode}-mode`);
};

const handleSignOut = async () => {
  await authStore.logout();
  router.push('/sign-in');
};
</script>

<style scoped>
.navbar {
  z-index: 20;
  transition: all 0.3s ease;
  padding: 0.5rem 1rem;
  backdrop-filter: blur(10px);
  border-bottom: 0.1px solid rgba(255, 254, 254, 0.852);
}

.navbar-logo {
  height: 60px;
  transition: transform 0.3s ease;
}

.navbar-logo:hover {
  transform: scale(1.05);
}

/* Navigation Links */
.nav-link {
  font-family: Lobster, cursive;
  font-size: 16.5px;
  padding: 0.5rem 1.2rem;
  margin: 0 1.3rem;
  border-radius: 20px;
  transition: all 0.3s ease;
}

.navbar-dark .nav-link {
  color: #ffffff;
}

.navbar-dark .nav-link.active {
  color: #d4bb00;
  background: none; /* Hilangkan background */
  border-bottom: 2.4px solid #d4bb00; /* Garis bawah */
  padding-bottom: 0.7rem; 
  margin-bottom: -0.7rem; 
}

.navbar-dark .nav-link:hover {
  color: #d4bb00;
  border-bottom: 2.4px solid #d4bb00; /* Efek hover dengan garis bawah */
  padding-bottom: 0.7rem;
  margin-bottom: -0.7rem;
}

/* Theme Toggle Button */
.theme-toggle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 0.1);
  background: transparent;
  transition: all 0.3s ease;
}

.theme-toggle:hover {
  background: rgba(255, 255, 255, 0.1);
  transform: rotate(45deg);
}

/* Login Button */
.login-btn {
  display: flex;
  align-items: center;
  padding: 0.6rem 1.5rem;
  border-radius: 25px;
  background: transparent;
  border: 2px solid #d4bb00;
  color: #d4bb00;
  font-weight: 600;
  transition: all 0.3s ease;
  text-decoration: none;
}

.login-btn:hover {
  background: #d4bb00;
  color: #000;
  transform: translateY(-2px);
}

/* Profile Button */
.profile-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 0.1);
  overflow: hidden;
  padding: 0;
  transition: all 0.3s ease;
}

.profile-btn:hover {
  border-color: #d4bb00;
  transform: scale(1.05);
}

.profile-image-container {
  width: 100%;
  height: 100%;
}

.profile-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Dropdown Menu */
.menu-dropdown {
  min-width: 250px;
  background: var(--bs-body-bg);
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.menu-link {
  padding: 0.8rem 1.5rem;
  color: var(--bs-body-color);
  text-decoration: none;
  transition: all 0.3s ease;
  border-radius: 8px;
  margin: 0 0.5rem;
}

.menu-link:hover {
  background: rgba(212, 187, 0, 0.1);
  color: #d4bb00;
}

@media (max-width: 991.98px) {
  .navbar-nav {
    margin: 1rem 0;
  }
  
  .nav-link {
    padding: 0.5rem 1rem;
    margin: 0.2rem 0;
  }
}
</style>