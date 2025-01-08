<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch, computed } from "vue";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});
const emit = defineEmits(["close", "refresh"]);
</script>

<template>
    <VForm class="form card mb-10">

    </VForm>
</template>