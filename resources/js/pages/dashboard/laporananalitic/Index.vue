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
  if (userRole.value == '3' && userKonserId.value) {
    return data.filter(item => item.konser_id == userKonserId.value);
  }
  return data;
};

// Update paginate url based on user role
const paginateUrl = computed(() => {
  if (userRole.value == '3') {
    return `/konser?konser_id=${userKonserId.value}`;
  }
  return '/konser';
});

// Modified viewReport function
const viewReport = async (uuid: string) => {
  try {
    isLoading.value = true;
    const response = await axios.get(`/konser/report/${uuid}`);
    
    if (response.data.error) {
      throw new Error(response.data.error);
    }
    
    // Log the data for debugging
    console.log('Report data:', response.data);
    
    currentReportData.value = response.data;
    showReportModal.value = true;
  } catch (error: any) {
    console.error('Failed to fetch report:', error);
    toast.error(error?.response?.data?.message || 'Gagal memuat laporan');
  } finally {
    isLoading.value = false;
  }
};

// Modified downloadReport function
const downloadReport = async (uuid: string, type: 'excel' | 'pdf') => {
  try {
    isLoading.value = true;
    const response = await axios.get(`/konser/download-report/${uuid}`, {
      responseType: 'blob',
      params: { type }
    });

    // Create download link
    const blob = new Blob([response.data], { 
      type: type === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `konser-report-${uuid}.${type}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
    
    toast.success(`Berhasil mengunduh ${type.toUpperCase()}`);
  } catch (error) {
    console.error('Failed to download report:', error);
    toast.error(`Gagal mengunduh ${type.toUpperCase()}`);
  } finally {
    isLoading.value = false;
  }
};

// Modified columns for report actions
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
    cell: (info) => h("div", { class: "d-flex gap-2" }, [
      h("button", {
        class: "btn btn-sm btn-primary",
        onClick: () => viewReport(info.getValue())
      }, [
        h("i", { class: "fas fa-chart-bar me-1" }),
        "Laporan"
      ]),
      h("div", { class: "dropdown" }, [
        h("button", {
          class: "btn btn-sm btn-success dropdown-toggle",
          "data-bs-toggle": "dropdown"
        }, [
          h("i", { class: "fas fa-download me-1" }),
          "Download"
        ]),
        h("ul", { class: "dropdown-menu" }, [
          h("li", h("a", {
            class: "dropdown-item",
            onClick: () => downloadReport(info.getValue(), 'pdf')
          }, [
            h("i", { class: "fas fa-file-pdf me-1" }),
            "PDF"
          ])),
          h("li", h("a", {
            class: "dropdown-item",
            onClick: () => downloadReport(info.getValue(), 'excel')
          }, [
            h("i", { class: "fas fa-file-excel me-1" }),
            "Excel"
          ]))
        ])
      ])
    ])
  })
];

const refresh = () => paginateRef.value.refetch();

const fetchData = async () => {
  try {
    const params = userRole.value == '3' ? { konser_id: userKonserId.value } : {};
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
      <h1 class="text-center font-bold mb-2">
        {{ userRole == '3' ? 'Laporan Konser Anda' : 'Dashboard Analytics Konser' }}
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

  <!-- Modified Report Modal -->
  <div v-if="showReportModal" class="modal fade show" style="display: block">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detail Laporan Konser</h5>
          <button @click="showReportModal = false" class="btn-close"></button>
        </div>
        <div class="modal-body">
          <div v-if="isLoading" class="text-center py-4">
            <div class="spinner-border text-primary"></div>
          </div>
          <div v-else-if="currentReportData" class="report-content">
            <!-- Report Content -->
            <div class="card mb-3">
              <div class="card-body">
                <h4 class="card-title">{{ currentReportData.konser_info?.title }}</h4>
                <div class="row mt-3">
                  <div class="col-md-6">
                    <p><strong>Tanggal:</strong> {{ currentReportData.konser_info?.tanggal }}</p>
                    <p><strong>Lokasi:</strong> {{ currentReportData.konser_info?.lokasi }}</p>
                  </div>
                  <div class="col-md-6">
                    <p><strong>Total Tiket Terjual:</strong> {{ currentReportData.ticket_stats?.total_tickets }}</p>
                    <p><strong>Total Pendapatan:</strong> Rp {{ currentReportData.ticket_stats?.total_revenue?.toLocaleString() }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Ticket Details Table -->
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Detail Penjualan per Tipe Tiket</h5>
                <div class="table-responsive">
                  <table class="table table-bordered mt-3">
                    <thead class="table-light">
                      <tr>
                        <th>Tipe Tiket</th>
                        <th>Jumlah Terjual</th>
                        <th>Total Pendapatan</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(count, type) in currentReportData.ticket_stats?.by_type" :key="type">
                        <td>{{ type }}</td>
                        <td>{{ count }}</td>
                        <td>Rp {{ currentReportData.ticket_stats?.revenue_by_type[type]?.toLocaleString() }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" @click="showReportModal = false">Tutup</button>
          <button 
            class="btn btn-success me-2" 
            @click="downloadReport(currentReportData?.konser_info?.uuid, 'excel')"
          >
            <i class="fas fa-file-excel me-1"></i> Excel
          </button>
          <button 
            class="btn btn-danger" 
            @click="downloadReport(currentReportData?.konser_info?.uuid, 'pdf')"
          >
            <i class="fas fa-file-pdf me-1"></i> PDF
          </button>
        </div>
      </div>
    </div>
  </div>
  <div v-if="showReportModal" class="modal-backdrop fade show"></div>
</template>

<style scoped>
.report-content {
  padding: 1rem;
}
.modal {
  background-color: rgba(0, 0, 0, 0.5);
}
.table th, .table td {
  padding: 0.75rem;
  vertical-align: middle;
}
</style>