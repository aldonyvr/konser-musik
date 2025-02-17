
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
const user = ref({});
const formRef = ref()
const fileTypes = ref(["image/jpeg", "image/png", "image/jpg"]);
const image = ref<any>([]);
const previewImage = ref('');

function getEdit() {
    block(document.getElementById("form-user"));
    ApiService.get(`/banner/edit/${props.selected}`)
        .then(({ data }) => {
            if (data.success && data.data) {
                user.value = data.data;
                previewImage.value = data.data.image;
                console.log('Current banner:', data.data);
            }
        })
        .catch((err: any) => {
            console.error('Error fetching banner:', err);
            toast.error(err.response?.data?.message || 'Gagal mengambil data banner');
        })
        .finally(() => {
            unblock(document.getElementById("form-user"));
        });
}


onMounted(async () => {
    if (props.selected) {
        getEdit();
    }
});

watch(
    () => props.selected,
    () => {
        if (props.selected) {
            getEdit();
        }
    }
);
</script>

<template>
    <VForm class="form card mb-10" @submit="submit" :validation-schema="formSchema" id="form-user" ref="formRef">

    </VForm>
</template>