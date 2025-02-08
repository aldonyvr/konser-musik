<script setup lang="ts">
import { h, ref, watch, onMounted, computed } from "vue";
import { useDelete } from "@/libs/hooks";
import { createColumnHelper } from "@tanstack/vue-table";
import type { Konser } from "@/types";
import Form from "./Form.vue";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { useVueTable } from "@tanstack/vue-table";
import DataTableFacetedFilter from '@/components/DataTableFacetedFilter.vue';

interface KonserData {
    id: number;
    title: string;
    lokasi: string;
    tanggal: string;
    jam: string;
    deskripsi: string;
    tiket_tersedia: number;
}

const column = createColumnHelper<Konser>();
const paginateRef = ref<any>(null);
const table = ref<any>(null);
const selectedKonser = ref('');
const selectedKonserIds = ref(new Set());

const selected = ref<string>("");
const openForm = ref<boolean>(false);
const konserList = ref<KonserData[]>([]);
const isLoading = ref<boolean>(false);

const columnVisibility = ref({
    'uuid': false,
    'konser_id': false
});

const toggleColumnVisibility = (columnId: string) => {
    columnVisibility.value = {
        ...columnVisibility.value,
        [columnId]: !columnVisibility.value[columnId]
    };
};

const konserFilterOptions = computed(() => 
  konserList.value.map(konser => ({
    label: `${konser.title} - ${konser.lokasi}`,
    value: konser.id
  }))
);

const dataPemesanan = ref([]);

const filteredDataPemesanan = computed(() => {
    if (!selectedKonserIds.value.size) return dataPemesanan.value;
    
    return dataPemesanan.value.filter(item => 
        selectedKonserIds.value.has(item.tiket.konsers_id)
    );
});

const fetchData = async () => {
    try {
        const response = await axios.post('/datapemesan', {
            search: '',
            page: 1,
            per: 10
        });
        dataPemesanan.value = response.data;
    } catch (error) {
        console.error('Error fetching data:', error);
        toast.error("Gagal memuat data");
    }
};

// Table columns configuration
const columns = [
    column.accessor("no", {
        header: "No",
        cell: (info) => info.row.index + 1
    }),
    column.accessor("nama_pemesan", {
        header: "Nama Pemesan",
    }),
    column.accessor("tiket.konsers.title", {
        header: "Konser",
    }),
    column.accessor("email_pemesan", {
        header: "Email",
    }),
    column.accessor("telepon_pemesan", {
        header: "Telepon",
    }),
    column.accessor("alamat_pemesan", {
        header: "Alamat",
    }),
    column.accessor("status_pembayaran", {
        header: "Status Pembayaran",
    }),
    column.accessor("tanggal_pemesan", {
        header: "Tanggal Pemesanan",
        cell: ({ getValue }) => {
            const date = new Date(getValue());
            return date.toLocaleDateString('id-ID');
        }
    }),
    
    column.accessor("tiket.konsers_id", {
        id: 'konsers_id',
        header: "Konser",
        enableColumnFilter: true,
        filterFn: (row: any) => {
            if (!selectedKonserIds.value.length) return true;
            return selectedKonserIds.value.includes(row.original.tiket.konsers_id);
        },
        cell: ({ row }) => {
            const konser = konserList.value.find(k => k.id === row.original.tiket.konsers_id);
            return konser ? `${konser.title} - ${konser.lokasi}` : '';
        }
    }),
    column.accessor("uuid", {
        header: "Aksi",
        cell: (info) => h("div", { class: "d-flex gap-2" }, [
            h("button", {
                class: "btn btn-sm btn-icon btn-info",
                onClick: () => handleEdit(info.getValue())
            }, [
                h("i", { class: "la la-pencil fs-2" })
            ]),
            h("button", {
                class: "btn btn-sm btn-icon btn-danger",
                onClick: () => handleDelete(info.getValue())
            }, [
                h("i", { class: "la la-trash fs-2" })
            ])
        ])
    })
];

// Delete handler with confirmation
const { delete: deleteKonser } = useDelete({
    onSuccess: () => {
        toast.success("Data berhasil dihapus");
        refreshData();
    },
    onError: (error) => {
        toast.error("Gagal menghapus data");
        console.error("Delete error:", error);
    }
});

// Fetch konser data
const fetchKonserList = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get('http://localhost:8000/api/getKonser');
        konserList.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error fetching konser:', error);
        toast.error("Gagal memuat data konser");
    } finally {
        isLoading.value = false;
    }
};

// Handlers
const handleEdit = (uuid: string) => {
    selected.value = uuid;
    openForm.value = true;
};

const handleDelete = async (uuid: string) => {
    if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
        await deleteKonser(`konser/konser/${uuid}`);
    }
};

const refreshData = () => {
    if (paginateRef.value) {
        paginateRef.value.refetch();
    }
    fetchKonserList();
};

// Watchers
watch(() => selectedKonser.value, () => {
    if (paginateRef.value) {
        paginateRef.value.refetch();
    }
}, { deep: true });

watch(openForm, (val) => {
    if (!val) {
        selected.value = "";
    }
    window.scrollTo(0, 0);
});

// Add a watch for filter changes
watch(selectedKonser, (newFilters) => {
    if (paginateRef.value) {
        paginateRef.value.refetch();
    }
}, { deep: true });

// Add handler for filter change
const handleFilterChange = (values: number[]) => {
    selectedKonserIds.value = values;
    if (paginateRef.value) {
        paginateRef.value.refetch();
    }
};

const handleKonserFilter = (value: number) => {
    const newSelected = new Set(selectedKonserIds.value);
    if (newSelected.has(value)) {
        newSelected.delete(value);
    } else {
        newSelected.add(value);
    }
    selectedKonserIds.value = newSelected;
};

const resetKonserFilter = () => {
    selectedKonserIds.value = new Set();
};

const searchKonser = ref('');
const filteredKonserList = computed(() => {
    if (!searchKonser.value) return konserList.value;
    const search = searchKonser.value.toLowerCase();
    return konserList.value.filter(konser => 
        konser.title.toLowerCase().includes(search) || 
        konser.lokasi.toLowerCase().includes(search)
    );
});

// Add a method to get count for each konser
const getKonserCount = (konserId: number) => {
    return dataPemesanan.value.filter(item => 
        item.tiket.konsers_id === konserId
    ).length;
};

// Add pagination state
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Update filtered data with pagination
const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredDataPemesanan.value.slice(start, end);
});

// Add pagination helpers
const totalPages = computed(() => 
    Math.ceil(filteredDataPemesanan.value.length / itemsPerPage.value)
);

const pageNumbers = computed(() => {
    const pages = [];
    const maxVisiblePages = 5;
    const halfVisible = Math.floor(maxVisiblePages / 2);
    
    let startPage = Math.max(1, currentPage.value - halfVisible);
    let endPage = Math.min(totalPages.value, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
    }
    
    return pages;
});

const goToPage = (page: number) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

// Reset to first page when filter changes
watch([selectedKonserIds, searchKonser], () => {
    currentPage.value = 1;
});

// Lifecycle
onMounted(() => {
    fetchData();
    fetchKonserList();
});
</script>

<template>
    <div class="card">
        <!-- Header -->
        <div class="card-header border-bottom d-flex align-items-center py-3">
            <h3 class="card-title mb-0">Manajemen Konser</h3>
            <!-- <button
                v-if="!openForm"
                type="button"
                class="btn btn-primary btn-sm ms-auto"
                @click="openForm = true"
            >
                <i class="la la-plus me-2"></i>
                Tambah Konser
            </button> -->

            <!-- Add Column Visibility Dropdown -->
            <!-- <div class="dropdown ms-2">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Columns
                </button>
                <ul class="dropdown-menu">
                    <li v-for="column in columns" :key="column.id">
                        <label class="dropdown-item">
                            <input 
                                type="checkbox" 
                                :checked="!columnVisibility[column.id]"
                                @change="toggleColumnVisibility(column.id)"
                                class="form-check-input me-2"
                            />
                            {{ column.header }}
                        </label>
                    </li>
                </ul>
            </div> -->
        </div>

        <!-- Body -->
        <div class="card-body">
            <!-- Filter -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Filter Konser
                            <span v-if="selectedKonserIds.size" class="badge bg-secondary ms-2">
                                {{ selectedKonserIds.size }}
                            </span>
                        </button>
                        <div class="dropdown-menu p-3" style="width: 300px">
                            <input 
                                type="search" 
                                class="form-control form-control-sm mb-2" 
                                placeholder="Cari konser..."
                                v-model="searchKonser"
                            >
                            <div class="dropdown-divider"></div>
                            <div class="dropdown-items-wrapper" style="max-height: 300px; overflow-y: auto;">
                                <div v-for="konser in filteredKonserList" 
                                     :key="konser.id" 
                                     class="dropdown-item py-2 px-3"
                                >
                                    <label class="d-flex align-items-center gap-2 mb-0">
                                        <input 
                                            type="checkbox"
                                            :checked="selectedKonserIds.has(konser.id)"
                                            @change="handleKonserFilter(konser.id)"
                                            class="form-check-input"
                                        >
                                        <span>{{ konser.title }} - {{ konser.lokasi }}</span>
                                        <span class="badge bg-secondary ms-auto">{{ getKonserCount(konser.id) }}</span>
                                    </label>
                                </div>
                            </div>
                            <div v-if="selectedKonserIds.size" class="dropdown-divider mt-2"></div>
                            <button 
                                v-if="selectedKonserIds.size"
                                class="btn btn-link btn-sm w-100 text-decoration-none"
                                @click="resetKonserFilter"
                            >
                                Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-rounded table-hover table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pemesan</th>
                            <th>Konser</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th>Tanggal Pemesanan</th>
                            <th>Status Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!filteredDataPemesanan.length">
                            <td colspan="8" class="text-center">Data tidak ditemukan</td>
                        </tr>
                        <tr v-else v-for="(item, index) in paginatedData" :key="item.uuid">
                            <td>{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
                            <td>{{ item.nama_pemesan }}</td>
                            <td>{{ item.tiket.konsers.title }}</td>
                            <td>{{ item.email_pemesan }}</td>
                            <td>{{ item.telepon_pemesan }}</td>
                            <td>{{ item.alamat_pemesan }}</td>
                            <td>{{ new Date(item.tanggal_pemesan).toLocaleDateString('id-ID') }}</td>
                            <td class="text-success">{{ item.status_pembayaran }}</td>

                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-icon btn-danger" @click="handleDelete(item.uuid)">
                                        <i class="la la-trash fs-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to 
                    {{ Math.min(currentPage * itemsPerPage, filteredDataPemesanan.length) }}
                    of {{ filteredDataPemesanan.length }} entries
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination mb-0">
                        <li class="page-item" :class="{ disabled: currentPage === 1 }">
                            <button class="page-link" @click="goToPage(currentPage - 1)">
                                Previous
                            </button>
                        </li>
                        <li v-for="page in pageNumbers" 
                            :key="page" 
                            class="page-item"
                            :class="{ active: page === currentPage }"
                        >
                            <button class="page-link" @click="goToPage(page)">
                                {{ page }}
                            </button>
                        </li>
                        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                            <button class="page-link" @click="goToPage(currentPage + 1)">
                                Next
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Form Modal -->
    <Form
        v-if="openForm"
        :uuid="selected"
        @close="openForm = false"
        @success="refreshData"
    />
</template>

<style scoped>
.card {
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
    border-radius: 8px;
}

.card-header {
    background-color: transparent;
}

.table-responsive {
    min-height: 400px;
}

.dropdown-item {
    cursor: pointer;
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-items-wrapper::-webkit-scrollbar {
    width: 6px;
}

.dropdown-items-wrapper::-webkit-scrollbar-thumb {
    background-color: #ddd;
    border-radius: 3px;
}
</style>