<script setup lang="ts">
import { nextTick, onBeforeMount, onMounted } from "vue";
import { RouterView } from "vue-router";
import { useConfigStore } from "@/stores/config";
import { useThemeStore } from "@/stores/theme";
import { useBodyStore } from "@/stores/body";
import { themeConfigValue } from "@/layouts/default-layout/config/helper";
import { initializeComponents } from "@/core/plugins/keenthemes";

const configStore = useConfigStore();
const themeStore = useThemeStore();
const bodyStore = useBodyStore();

onBeforeMount(() => {
    /**
     * Overrides the layout config using saved data from localStorage
     * remove this to use static config (@/layouts/default-layout/config/DefaultLayoutConfig.ts)
     */
    configStore.overrideLayoutConfig();

    /**
     *  Sets a mode from configuration
     */
    themeStore.setThemeMode(themeConfigValue.value);
});

onMounted(() => {
    nextTick(() => {
        initializeComponents();

        bodyStore.removeBodyClassName("page-loading");
    });
});
</script>

<template>
    <RouterView />
</template>

<style lang="scss">
@import "bootstrap-icons/font/bootstrap-icons.css";
@import "apexcharts/dist/apexcharts.css";
@import "animate.css";
@import "sweetalert2/dist/sweetalert2.css";
@import "nouislider/dist/nouislider.css";
@import "@fortawesome/fontawesome-free/css/all.min.css";
@import "socicon/css/socicon.css";
@import "line-awesome/dist/line-awesome/css/line-awesome.css";
@import "@vueform/multiselect/themes/default.css";
@import "prism-themes/themes/prism-shades-of-purple.css";
@import "element-plus/dist/index.css";

// Main demo style scss
@import "assets/keenicons/duotone/style.css";
@import "assets/keenicons/outline/style.css";
@import "assets/keenicons/solid/style.css";
@import "assets/sass/element-ui.dark";
@import "assets/sass/plugins";
@import "assets/sass/style";

:root {
  --font-family-base: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

#app {
  font-family: var(--font-family-base);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Text weight utilities */
.text-light {
  font-weight: 300 !important;
}

.text-regular {
  font-weight: 400 !important;
}

.text-medium {
  font-weight: 500 !important;
}

.text-semibold {
  font-weight: 600 !important;
}

.text-bold {
  font-weight: 700 !important;
}

.text-extrabold {
  font-weight: 800 !important;
}

/* Heading styles */
.heading-1 {
  font-size: 2.5rem;
  font-weight: 800;
  letter-spacing: -0.02em;
}

.heading-2 {
  font-size: 2rem;
  font-weight: 700;
  letter-spacing: -0.01em;
}

.heading-3 {
  font-size: 1.75rem;
  font-weight: 600;
}

/* Components */
.nav-link {
  font-weight: 500;
}

.btn {
  font-weight: 600;
  letter-spacing: 0.02em;
}

.card-title {
  font-weight: 600;
  letter-spacing: -0.01em;
}
</style>
