<script setup lang="ts">
import { h, ref, watch, computed } from "vue";
import { useDelete } from "@/libs/hooks";
import { createColumnHelper } from "@tanstack/vue-table";
import type { Konser } from "@/types";
import { currency } from '@/libs/utils';
import axios from '@/libs/axios';
import { toast } from 'vue3-toastify';
import { useAuthStore } from "@/stores/auth";
import jsPDF from 'jspdf';
import 'jspdf-autotable';

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
const filterData = (data: any[]) => {
  if (userRole.value === '3' && authStore.user?.konser_id) {
    return data.filter(item => {
      const konserId = item.konser?.id || item.konsers_id;
      return konserId === authStore.user.konser_id;
    });
  }
  return data;
};

// Update paginate url based on user role
const paginateUrl = computed(() => {
  if (userRole.value === '3' && authStore.user?.konser_id) {
    // Filter by konser_id for mitra users
    return `/konser?filter[konser_id]=${authStore.user.konser_id}`;
  }
  return '/konser';
});

// Modified viewReport function to use konser uuid
const viewReport = async (info: any) => {
  try {
    isLoading.value = true;
    const konser = info.row.original.konser;
    
    // Check if mitra user has access to this konser
    if (userRole.value === '3' && authStore.user?.konser_id !== konser.id) {
      throw new Error('Unauthorized access');
    }

    const uuid = konser.uuid;
    if (!uuid) {
      throw new Error('Konser UUID not found');
    }

    const response = await axios.get(`/konser/report/${uuid}`);
    
    if (response.data.error) {
      throw new Error(response.data.error);
    }
    
    currentReportData.value = response.data;
    showReportModal.value = true;
  } catch (error: any) {
    console.error('Failed to fetch report:', error);
    toast.error(error?.response?.data?.message || 'Gagal memuat laporan');
  } finally {
    isLoading.value = false;
  }
};

const downloadPDF = () => {
  const doc = new jsPDF();
  const report = currentReportData.value;
  
  // Add header
  doc.setFontSize(20);
  doc.text('Laporan Penjualan Konser', 15, 20);
  doc.setFontSize(12);
  doc.text(`${report.konser_info.title}`, 15, 30);
  
  // Add concert info
  doc.text(`Tanggal: ${report.konser_info.tanggal}`, 15, 40);
  doc.text(`Lokasi: ${report.konser_info.lokasi}`, 15, 50);
  
  // Add sales summary
  doc.autoTable({
    startY: 60,
    head: [['Tipe Tiket', 'Jumlah Terjual', 'Total Pendapatan']],
    body: Object.entries(report.ticket_stats.by_type).map(([type, count]) => [
      type,
      count,
      `Rp ${report.ticket_stats.revenue_by_type[type].toLocaleString()}`
    ])
  });
  
  doc.save(`laporan-${report.konser_info.title}.pdf`);
};

// Modified downloadReport function to use konser uuid
const downloadReport = async (info: any, type: 'excel' | 'pdf') => {
  try {
    isLoading.value = true;
    const uuid = info.row.original.konser.uuid;
    if (!uuid) {
      throw new Error('Konser UUID not found');
    }

    const response = await axios.get(`/konser/download-report/${uuid}`, {
      params: { type }
    });

    if (response.data.file_url) {
      // Create download link
      const link = document.createElement('a');
      link.href = response.data.file_url;
      link.download = response.data.filename;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      
      toast.success(`Berhasil mengunduh ${type.toUpperCase()}`);
    }

  } catch (error) {
    console.error('Failed to download report:', error);
    toast.error(`Gagal mengunduh ${type.toUpperCase()}`);
  } finally {
    isLoading.value = false;
  }
};


const templateTitle = computed(() => {
  return userRole.value == '3' ? 'Laporan Konser Anda' : 'Dashboard Analytics Konser';
});
// Update columns definition to pass full row info
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
  column.accessor("uuid", {
    header: "Aksi",
    cell: (info) => h("div", { class: "d-flex gap-2" }, [
      // View Report button
      h("button", {
        class: "btn btn-sm btn-primary",
        onClick: () => viewReport(info)
      }, [
        h("i", { class: "fas fa-chart-bar me-1" }),
        "Laporan"
      ]),
      
      // Download buttons
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
            onClick: () => downloadReport(info, 'pdf')
          }, "PDF")),
          h("li", h("a", {
            class: "dropdown-item",
            onClick: () => downloadReport(info, 'excel')
          }, "Excel"))
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
  <div class="card ">
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

  <!-- Enhanced Report Modal -->
  <div v-if="showReportModal" class="modal fade show" style="display: block">
    <div class="modal-dialog modal-xl">
      <div class="modal-content ">
        <div class="modal-header  text-white">
          <h5 class="modal-title">
            <i class="fas fa-chart-line me-2"></i>
            Laporan Penjualan Konser
          </h5>
          <button @click="showReportModal = false" class="btn-close"></button>
        </div>
        
        <div class="modal-body ">
          <div v-if="isLoading" class="text-center py-5">
            <div class="spinner-border text-primary"></div>
          </div>
          
          <div v-else-if="currentReportData" class="report-container">
            <!-- Concert Info Header -->
            <div class="concert-header mb-4 ">
              <div class="row align-items-center">
                <div class="col-md-8">
                  <h3>{{ currentReportData.konser_info.title }}</h3>
                  <div class="concert-meta">
                    <span class="me-3">
                      <i class="fas fa-calendar-alt text-primary me-1"></i>
                      {{ currentReportData.konser_info.tanggal }}
                    </span>
                    <span>
                      <i class="fas fa-map-marker-alt text-danger me-1"></i>
                      {{ currentReportData.konser_info.lokasi }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Sales Summary Cards -->
            <div class="row mb-4">
              <div class="col-md-4">
                <div class="card bg-gradient-primary text-white">
                  <div class="card-body">
                    <h6>Total Pendapatan</h6>
                    <h3>Rp {{ currentReportData.ticket_stats.total_revenue.toLocaleString() }}</h3>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card bg-gradient-success text-white">
                  <div class="card-body">
                    <h6>Total Tiket Terjual</h6>
                    <h3>{{ currentReportData.ticket_stats.total_tickets }}</h3>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card bg-gradient-info text-white">
                  <div class="card-body">
                    <h6>Rata-rata Penjualan</h6>
                    <h3>Rp {{ Math.round(currentReportData.ticket_stats.total_revenue / 
                      currentReportData.ticket_stats.total_tickets).toLocaleString() }}</h3>
                  </div>
                </div>
              </div>
            </div>

            <!-- Ticket Sales by Type -->
            <div class="card  mb-4">
              <div class="card-header text-white">
                <h5 class="mb-0">Penjualan per Tipe Tiket</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover bg-transparent">
                    <thead class="">
                      <tr>
                        <th>Tipe Tiket</th>
                        <th class="text-center">Jumlah Terjual</th>
                        <th class="text-end">Total Pendapatan</th>
                        <th class="text-end">Persentase</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(count, type) in currentReportData.ticket_stats.by_type" :key="type">
                        <td>
                          <span :class="['badge', type == 'VIP' ? 'bg-warning' : 'bg-primary']">
                            {{ type }}
                          </span>
                        </td>
                        <td class="text-center">{{ count }}</td>
                        <td class="text-end">
                          Rp {{ currentReportData.ticket_stats.revenue_by_type[type].toLocaleString() }}
                        </td>
                        <td class="text-end">
                          {{ Math.round((count / currentReportData.ticket_stats.total_tickets) * 100) }}%
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-footer ">
          <button class="btn btn-secondary" @click="showReportModal = false">
            <i class="fas fa-times me-1"></i> Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <div v-if="showReportModal" class="modal-backdrop fade show"></div>
</template>

<style scoped>

.card {
  border: none;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.2s;
}

.bg-gradient-primary {
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-success {
  background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.bg-gradient-info {
  background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
}

.table th {
  font-weight: 600;

}

.table-hover tbody tr:hover {
  background-color: rgba(0,0,0,0.03);
}

.badge {
  padding: 0.5rem 1rem;
  font-weight: 500;
}

.concert-header h3 {
  margin-bottom: 0.5rem;
}

.concert-meta {
  color: #6c757d;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

.report-container {
  animation: fadeIn 0.3s ease-out;
}

/* Override any remaining white backgrounds */
.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5);
}

.table {
  background-color: transparent;
}

/* Add subtle hover effects */
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.table tbody tr {
  transition: background-color 0.3s ease;
}
</style>