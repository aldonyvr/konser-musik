
<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from "@/stores/auth";
import axios from '@/libs/axios';
import { toast } from 'vue3-toastify';
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import * as XLSX from 'xlsx';

const authStore = useAuthStore();
const isLoading = ref(true);
const salesData = ref([]);
const dateRange = ref({
  start: '',
  end: ''
});

const isMitra = computed(() => authStore.user?.role_id === '3');
const userKonserId = computed(() => authStore.user?.konser_id);

const stats = computed(() => {
  const total = salesData.value.reduce((acc, item) => {
    if (item.status_pembayaran === 'Successfully') {
      return acc + parseFloat(item.total_harga);
    }
    return acc;
  }, 0);

  const count = salesData.value.filter(item => 
    item.status_pembayaran === 'Successfully'
  ).length;

  return {
    totalSales: total,
    totalTransactions: count,
    averageTransaction: count ? total / count : 0
  };
});

const fetchSalesData = async () => {
  try {
    isLoading.value = true;
    const params = {
      start_date: dateRange.value.start,
      end_date: dateRange.value.end,
      ...(isMitra.value && { konser_id: userKonserId.value })
    };

    const response = await axios.get('/datapemesan/report', { params });
    salesData.value = response.data.data.map(item => ({
      ...item,
      formattedDate: new Date(item.tanggal_pemesan).toLocaleDateString(),
      statusClass: getStatusClass(item.status_pembayaran)
    }));
  } catch (error) {
    console.error('Error fetching sales data:', error);
    toast.error('Gagal memuat data penjualan');
  } finally {
    isLoading.value = false;
  }
};

const getStatusClass = (status) => {
  switch(status) {
    case 'Successfully': return 'bg-success';
    case 'Pending': return 'bg-warning';
    default: return 'bg-danger';
  }
};

const downloadPDF = () => {
  const doc = new jsPDF();
  
  // Add title
  doc.setFontSize(18);
  doc.text('Laporan Penjualan Tiket', 14, 20);
  
  // Add date range
  doc.setFontSize(12);
  doc.text(`Periode: ${dateRange.value.start || 'All'} - ${dateRange.value.end || 'All'}`, 14, 30);
  
  // Add stats
  doc.text(`Total Penjualan: Rp ${stats.value.totalSales.toLocaleString()}`, 14, 40);
  doc.text(`Total Transaksi: ${stats.value.totalTransactions}`, 14, 50);
  
  // Create table
  const tableData = salesData.value.map(item => [
    item.formattedDate,
    item.nama_pemesan,
    item.tiket?.konser?.title || '-',
    item.gate_type,
    `Rp ${parseFloat(item.total_harga).toLocaleString()}`,
    item.status_pembayaran
  ]);

  doc.autoTable({
    startY: 60,
    head: [['Tanggal', 'Pembeli', 'Konser', 'Tipe Tiket', 'Total', 'Status']],
    body: tableData,
    theme: 'grid'
  });

  doc.save('laporan-penjualan.pdf');
};

const downloadExcel = () => {
  const data = salesData.value.map(item => ({
    Tanggal: item.formattedDate,
    Pembeli: item.nama_pemesan,
    Konser: item.tiket?.konser?.title || '-',
    'Tipe Tiket': item.gate_type,
    Total: parseFloat(item.total_harga),
    Status: item.status_pembayaran
  }));

  const ws = XLSX.utils.json_to_sheet(data);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, 'Laporan Penjualan');
  XLSX.writeFile(wb, 'laporan-penjualan.xlsx');
};

onMounted(() => {
  fetchSalesData();
});
</script>

<template>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Laporan Penjualan Tiket</h3>
      
      <!-- Filter Controls -->
      <div class="d-flex gap-3 mt-3">
        <div class="date-range">
          <input type="date" v-model="dateRange.start" class="form-control" @change="fetchSalesData">
          <span class="mx-2">sampai</span>
          <input type="date" v-model="dateRange.end" class="form-control" @change="fetchSalesData">
        </div>
        
        <!-- Export Buttons -->
        <div class="ms-auto">
          <button class="btn btn-success me-2" @click="downloadExcel">
            <i class="fas fa-file-excel me-1"></i> Excel
          </button>
          <button class="btn btn-danger" @click="downloadPDF">
            <i class="fas fa-file-pdf me-1"></i> PDF
          </button>
        </div>
      </div>
    </div>

    <div class="card-body">
      <!-- Stats Cards -->
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="card bg-primary">
            <div class="card-body">
              <h6 class="card-subtitle">Total Penjualan</h6>
              <h3 class="card-title mt-2">
                Rp {{ stats.totalSales.toLocaleString() }}
              </h3>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-success">
            <div class="card-body">
              <h6 class="card-subtitle">Total Transaksi</h6>
              <h3 class="card-title mt-2">
                {{ stats.totalTransactions }}
              </h3>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-info">
            <div class="card-body">
              <h6 class="card-subtitle">Rata-rata Transaksi</h6>
              <h3 class="card-title mt-2">
                Rp {{ stats.averageTransaction.toLocaleString() }}
              </h3>
            </div>
          </div>
        </div>
      </div>

      <!-- Sales Table -->
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Pembeli</th>
              <th>Konser</th>
              <th>Tipe Tiket</th>
              <th>Total</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading">
              <td colspan="6" class="text-center">
                <div class="spinner-border text-primary"></div>
              </td>
            </tr>
            <tr v-else v-for="item in salesData" :key="item.id">
              <td>{{ item.formattedDate }}</td>
              <td>{{ item.nama_pemesan }}</td>
              <td>{{ item.tiket?.konser?.title || '-' }}</td>
              <td>{{ item.gate_type }}</td>
              <td>Rp {{ parseFloat(item.total_harga).toLocaleString() }}</td>
              <td>
                <span :class="['badge', item.statusClass]">
                  {{ item.status_pembayaran }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
.date-range {
  display: flex;
  align-items: center;
}

.date-range input {
  width: 150px;
}

.card-title {
  margin-bottom: 0;
}

.badge {
  padding: 0.5rem 0.75rem;
  font-weight: 500;
}

.table th {
  font-weight: 600;
}

.stats-card {
  border-radius: 10px;
  border: none;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
}

.stats-card:hover {
  transform: translateY(-5px);
}

@media (max-width: 768px) {
  .date-range {
    flex-direction: column;
    gap: 1rem;
  }
  
  .date-range input {
    width: 100%;
  }
}
</style>
