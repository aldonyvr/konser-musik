<script setup lang="ts">
import { h, ref, watch, computed } from "vue";
import { useDelete } from "@/libs/hooks";
import { createColumnHelper } from "@tanstack/vue-table";
import type { Konser } from "@/types";
import { currency } from '@/libs/utils';
import axios from '@/libs/axios';
import { toast } from 'vue3-toastify';
import { useAuthStore } from "@/stores/auth";

const column = createColumnHelper<Konser>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);

const showReportModal = ref<boolean>(false);
const currentReportData = ref<any>(null);
const isLoading = ref<boolean>(false);

const { delete: deleteUser } = useDelete({
  onSuccess: () => paginateRef.value.refetch(),
});

const authStore = useAuthStore();
const userRole = computed(() => authStore.user?.role_id);
const userKonserId = computed(() => authStore.user?.konser_id);

// Filter data based on user role and konser_id
const filterData = (data) => {
  if (userRole.value === '3' && userKonserId.value) {
    return data.filter(item => item.konser_id === userKonserId.value);
  }
  return data;
};

// Update paginate url based on user role
const paginateUrl = computed(() => {
  if (userRole.value === '3') {
    return `/konser/index?konser_id=${userKonserId.value}`;
  }
  return '/konser/index';
});

// Function to view detailed report
const viewReport = async (uuid: string) => {
  try {
    isLoading.value = true;
    if (userRole.value === '3' && userKonserId.value) {
      // Only allow if konser matches mitra's assigned konser
      const konserCheck = await axios.get(`/konser/${uuid}`);
      if (konserCheck.data.id !== userKonserId.value) {
        toast.error('Unauthorized access');
        return;
      }
    }
    const response = await axios.get(`konser/report/${uuid}`, {
      headers: {
        'Accept': 'application/json',
      }
    });
    
    if (response.data.error) {
      throw new Error(response.data.error);
    }
    
    currentReportData.value = response.data;
    showReportModal.value = true;
  } catch (error: any) {
    console.error('Failed to fetch report:', error);
    toast.error(error.response?.data?.error || 'Gagal memuat laporan');
  } finally {
    isLoading.value = false;
  }
};

// Function to download report
const downloadReport = async (uuid: string, type: 'excel' | 'pdf') => {
  try {
    isLoading.value = true;
    const response = await axios.get(`/konser/download-report/${uuid}`, {
      responseType: 'blob',
      params: { type },
      headers: {
        'Accept': 'application/json',
      }
    });

    const contentType = response.headers['content-type'];
    const blob = new Blob([response.data], { type: contentType });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `konser-report-${uuid}.${type}`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
  } catch (error: any) {
    console.error('Failed to download report:', error);
    toast.error('Gagal mengunduh laporan');
  } finally {
    isLoading.value = false;
  }
};

const columns = [
  column.accessor("no", {
    header: "No",
  }),
  column.accessor("konser.image", {
    header: "Image",
    cell: cell => h('img', { src: `${cell.getValue()}`, width: 150 })
  }),
  column.accessor("konser.title", {
    header: "Nama Konser",
  }),
  column.accessor("konser.lokasi", {
    header: "Lokasi",
  }),
  column.accessor("konser.tanggal", {
    header: "Tanggal",
  }),
  column.accessor("tiket_tersedia", {
    header: "Total Tiket",
  }),
  column.accessor("uuid", {
    header: "Aksi",
    cell: (cell) =>
      h("div", { class: "d-flex gap-2 align-items-center" }, [
        // Tombol Laporan dengan Teks
        h(
          "button",
          {
            class: "btn btn-sm btn-primary d-flex align-items-center gap-1",
            onClick: () => viewReport(cell.getValue()),
          },
          [
            h("i", { class: "la la-file-alt fs-5" }),
            "Laporan"
          ]
        ),

        // Dropdown Download Button
        h("div", { class: "dropdown" }, [
          h(
            "button",
            {
              class: "btn btn-sm btn-success dropdown-toggle",
              type: "button",
              "data-bs-toggle": "dropdown",
              "aria-expanded": "false"
            },
            [
              h("i", { class: "la la-download fs-5" }),
              " Download"
            ]
          ),
          h(
            "ul",
            { class: "dropdown-menu" },
            [
              h(
                "li",
                h(
                  "a",
                  {
                    class: "dropdown-item",
                    onClick: () => downloadReport(cell.row.original.konser.uuid, 'pdf')
                  },
                  "Download PDF"
                )
              ),
              h(
                "li",
                h(
                  "a",
                  {
                    class: "dropdown-item",
                    onClick: () => downloadReport(cell.row.original.konser.uuid, 'excel')
                  },
                  "Download Excel"
                )
              ),
            ]
          )
        ]),

        // Tombol Hapus
        h(
          "button",
          {
            class: "btn btn-sm btn-danger",
            onClick: () => deleteUser(`konser/konser/${cell.getValue()}`),
          },
          h("i", { class: "la la-trash fs-5" })
        ),
      ]),
  }),
];

const refresh = () => paginateRef.value.refetch();

const fetchData = async () => {
  try {
    const params = userRole.value === '3' ? { konser_id: userKonserId.value } : {};
    const response = await axios.get('/konser/report', { params });
    return filterData(response.data);
  } catch (error) {
    console.error('Error fetching data:', error);
    toast.error('Gagal memuat data');
    return [];
  }
};

watch(openForm, (val) => {
  if (!val) {
    selected.value = "";
  }
  window.scrollTo(0, 0);
});
</script>

<template>
  <div class="card">
    <div class="card-header align-items-center">
      <h1 class="text-center font-bold mb-2 text-center">
        {{ userRole === '3' ? 'Laporan Konser Anda' : 'Dashboard Analytics Konser' }}
      </h1>
    </div>
    <div class="card-body">
      <paginate 
        ref="paginateRef" 
        id="table-users" 
        :url="paginateUrl" 
        :columns="columns"
      ></paginate>
    </div>
  </div>

  <!-- Report Modal -->
  <div v-if="showReportModal" class="modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Laporan Konser Detail</h5>
          <button @click="showReportModal = false" class="close">&times;</button>
        </div>
        <div class="modal-body">
          <!-- Render report details here -->
          <pre v-if="currentReportData">
                        {{ JSON.stringify(currentReportData, null, 2) }}
                    </pre>
        </div>
      </div>
    </div>
  </div>
</template>