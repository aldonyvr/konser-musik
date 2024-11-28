<script setup lang="ts">
import KTHeader from "@/layouts/default-layout/components/header/NavbarLanding.vue";
import { useRoute } from "vue-router";
import Footer from '@/Layouts/default-layout/components/footer/Footer.vue';


const route = useRoute();
</script>


<template>
     <nav>
        <KTHeader />
    </nav>
            
    <Footer />
</template>