<script setup lang="ts">
import { h, ref, onMounted, computed, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import { createColumnHelper } from "@tanstack/vue-table";
import type { Konser } from "@/types";
import axios from "@/libs/axios";
import { useAuthStore } from "@/stores/auth";
import { toast } from "vue3-toastify";

const column = createColumnHelper<Konser>();
const paginateRef = ref<any>(null);
const konserList = ref([]);
const selectedKonserId = ref('0'); // Change to string type and default to '0'
const isLoading = ref(false);

// Update columns to include konser information
const columns = [
    column.accessor("no", {
        header: "#",
    }),
    column.accessor("nama_pemesan", {
        header: "Nama",
    }),
    column.accessor("tiket.konser.title", {
        header: "Nama Konser",
    }),
    column.accessor("email_pemesan", {
        header: "Email",
    }),
    column.accessor("telepon_pemesan", {
        header: "No. Telp",
    }),
    column.accessor("tanggal_pemesan", {
        header: "Tgl Pemesanan",
    }),
    column.accessor("status_pembayaran", {
        header: "Status Pembayaran",
        cell: (info) => {
            const status = info.getValue();
            let badgeClass = '';
            switch(status) {
                case 'Successfully':
                    badgeClass = 'badge bg-success';
                    break;
                case 'Pending':
                    badgeClass = 'badge bg-warning';
                    break;
                default:
                    badgeClass = 'badge bg-secondary';
            }
            return h('span', { class: badgeClass }, status);
        }
    }),
];

const fetchKonserList = async () => {
    try {
        const response = await axios.get('/getKonser');
        if (response.data.data) {
            konserList.value = response.data.data;
        }
    } catch (error) {
        console.error('Error fetching konser:', error);
        toast.error('Gagal memuat daftar konser');
    }
};

const konsers = computed(() => {
    const item = konserList.value?.map((konser) => ({
        id: konser.id,
        text: konser.title,
    }))

    item.unshift({
        id: 0,
        text: 'Semua Konser',
    })

    return item
})

const paginateUrl = computed(() => {
    const baseUrl = '/datapemesan';
    const params = new URLSearchParams();
    
    if (selectedKonserId.value !== '0') {
        params.append('konser_id', selectedKonserId.value);
    }
    
    const queryString = params.toString();
    return queryString ? `${baseUrl}?${queryString}` : baseUrl;
});

watch(selectedKonserId, async (newValue) => {
    console.log('Selected Konser ID changed:', newValue);
    await refresh();
});

// Modifikasi handler untuk select event
const handleSelect = (event: any) => {
    selectedKonserId.value = event.id;
    refresh();
};

// Watch for konser selection changes
onMounted(() => {
    fetchKonserList();
});

const refresh = async () => {
    try {
        isLoading.value = true;
        if (paginateRef.value) {
            await paginateRef.value.refetch();
        }
    } catch (error) {
        console.error('Error refreshing data:', error);
        toast.error('Gagal memperbarui data');
    } finally {
        isLoading.value = false;
    }
};

</script>

<template>
    <div class="card">
        <div class="card-header align-items-center">
                <h2 class="mb-0">Riwayat Transaksi</h2>
                
                <!-- Update Konser Filter -->
                <div class="ms-auto" style="width: 250px;">
                    <select2
                        v-model="selectedKonserId"
                        :options="konsers"
                        class="form-select-solid"
                        placeholder="Pilih Konser"
                    />
                </div>
        </div>

        <div class="card-body">
            <div v-if="isLoading" class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <paginate
                v-else
                ref="paginateRef"
                id="table-pemesanan"
                :url="paginateUrl"
                :columns="columns"
            />
        </div>
    </div>
</template>

<style scoped>
.form-select {
    min-width: 200px;
}
</style>
