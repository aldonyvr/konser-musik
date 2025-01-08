<template>
  <nav
    class="navbar navbar-expand-lg navbar-force-dark fixed-top"
    :class="{ 
      'navbar-dark bg-dark': true,
      'navbar-light bg-light': false
    }">
    <div class="container-fluid">
      <a class="navbar-brand ms-10" href="#">
        <img src="/media/musik/logo.png" alt="Logo" class="navbar-logo">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
        aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse me-10 mt-2" id="navbarContent">
        <div class="me-auto"></div>

        <div class="d-flex flex-column align-items-end me-3">
          <div class="d-flex align-items-center">
            <!-- Theme Toggle Button -->
            <div class="me-3">
              <button @click="toggleTheme" 
                class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px">
                <KTIcon v-if="themeMode === 'light'" icon-name="night-day" icon-class="fs-2" />
                <KTIcon v-else icon-name="moon" icon-class="fs-2" />
              </button>
            </div>

            <!-- Profile Button -->
            <div class="position-relative">
              <button 
                class="btn btn-icon w-30px h-30px btn-custom btn-active-light" 
                data-kt-menu-trigger="click"
                data-kt-menu-attach="parent"
                data-kt-menu-placement="bottom-end"
              >
                <div v-if="authStore.user?.photo" class="profile-image-container">
                  <img 
                    :src="authStore.user.photo" 
                    :alt="authStore.user?.name"
                    class="profile-image rounded-circle"
                  >
                </div>
                <template v-else>
                  <KTIcon icon-name="user" icon-class="fs-2" />
                </template>
              </button>
              
              <!-- Profile Dropdown Menu -->
              <div 
                class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px"
                data-kt-menu="true"
              >
                <div class="menu-item px-3">
                  <div class="menu-content d-flex align-items-center px-3">
                    <div class="symbol symbol-50px me-5">
                      <img 
                        v-if="authStore.user?.photo" 
                        :src="authStore.user.photo" 
                        alt="profile"
                      >
                      <span v-else class="symbol-label bg-light-primary">
                        <KTIcon icon-name="user" icon-class="fs-2" />
                      </span>
                    </div>

                    <div class="d-flex flex-column">
                      <div class="fw-bolder d-flex align-items-center fs-5">
                        {{ authStore.user?.name }}
                      </div>
                      <a href="#" class="fw-bold text-muted text-hover-primary fs-7">
                        {{ authStore.user?.email }}
                      </a>
                    </div>
                  </div>
                </div>

                <div class="separator my-2"></div>

                <div class="menu-item px-5">
                  <router-link to="/profile" class="menu-link px-5">
                    Profile Saya
                  </router-link>
                </div>

                <div class="separator my-2"></div>

                <div class="menu-item px-5">
                  <a @click="handleSignOut" class="menu-link px-5">
                    Keluar
                  </a>
                </div>
              </div>
            </div>
          </div>

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
            <li class="nav-item" v-if="isAdmin">
              <router-link to="/dashboard" class="nav-link" active-class="active">Dashboard</router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { computed, ref } from "vue";
import KTUserMenu from "@/layouts/default-layout/components/menus/UserAccountMenu.vue";
import KTThemeModeSwitcher from "@/layouts/default-layout/components/theme-mode/ThemeModeSwitcher.vue";
import { ThemeModeComponent } from "@/assets/ts/layout";
import { useThemeStore } from "@/stores/theme";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";

const router = useRouter();
const themeStore = useThemeStore();
const authStore = useAuthStore();

const themeMode = computed(() => {
  return themeStore.mode === "system" 
    ? ThemeModeComponent.getSystemMode() 
    : themeStore.mode;
});

const isAdmin = computed(() => {
  return authStore.user?.role === "admin";
});

const toggleTheme = () => {
  const newMode = themeMode.value === 'light' ? 'dark' : 'light';
  themeStore.setThemeMode(newMode);
  document.body.classList.remove('light-mode', 'dark-mode');
  document.body.classList.add(`${newMode}-mode`);
};

const handleSignOut = async () => {
  await authStore.logout();
  router.push('/auth/sign-in');
};
</script>

<style scoped>
.navbar {
  z-index: 20;
  transition: background-color 0.3s ease;
}

.navbar::after {
  content: "";
  position: absolute;
  bottom: -1px;
  left: 0;
  width: 100%;
  height: 1.2px;
  border-radius: 0 0 100px 100px;
  z-index: -1;
}

.navbar-logo {
  height: 60px;
}

.profile-image-container {
  width: 30px;
  height: 30px;
  overflow: hidden;
  border-radius: 50%;
}

.profile-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.nav-link {
  font-family: Lobster, cursive;
  font-size: 14px;
  transition: color 0.3s ease, text-shadow 0.3s ease;
  position: relative;
}

/* Light theme styles */
.navbar-light .nav-link {
  color: #333;
}

.navbar-light .nav-link.active,
.navbar-light .nav-link:hover {
  color: #d4bb00;
}

/* Dark theme styles */
.navbar-dark .nav-link {
  color: #ffffff;
}

.navbar-dark .nav-link.active,
.navbar-dark .nav-link:hover {
  color: #d4bb00;
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

.nav-link:hover::before {
  transform: scaleX(1);
  transform-origin: bottom left;
}

.nav-item {
  margin-left: 20px;
}

.navbar-nav {
  display: flex;
  align-items: center;
}

@media (max-width: 991.98px) {
  .navbar-nav {
    flex-direction: row;
    justify-content: flex-end;
  }

  .nav-item {
    padding-left: 1rem;
  }
}

/* Profile menu styles */
.menu {
  display: none;
  position: absolute;
  top: 100%;
  right: 0;
  min-width: 200px;
  background: var(--bs-body-bg);
  border-radius: 0.475rem;
  box-shadow: 0 0 50px 0 rgba(82, 63, 105, 0.15);
  padding: 1rem 0;
}

.menu.show {
  display: block;
}

.menu-item {
  cursor: pointer;
}

.menu-link {
  display: flex;
  align-items: center;
  padding: 0.65rem 1rem;
  color: var(--bs-body-color);
  text-decoration: none;
}

.menu-link:hover {
  background-color: var(--bs-light);
  color: var(--bs-primary);
}
</style>