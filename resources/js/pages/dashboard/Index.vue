<script setup lang="ts">
import VueApexCharts from "vue3-apexcharts";
import { ref, onMounted, computed } from 'vue';
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { useAuthStore } from "@/stores/auth";

// Dashboard stats
const dashboardData = ref({
  totalKonser: 0,
  totalPembelian: 0,
  totalPendapatan: 0,
  totalUsers: 0,
  monthlyStats: [],
  ticketTypeStats: {
    Regular: 0,
    VIP: 0,
    VVIP: 0,
    Others: 0
  }
});

const isLoading = ref(true);
const authStore = useAuthStore();
const isMitra = computed(() => authStore.user?.role_id == '3');

// Format currency
const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value);
};

// Fetch dashboard data
const fetchDashboardData = async () => {
  try {
    isLoading.value = true;
    const params = {};
    
    // Add konser_id filter for mitra
    if (isMitra.value && authStore.user?.konser_id) {
      params.konser_id = authStore.user.konser_id;
    }

    const pembelianRes = await axios.post('/datapemesan', { 
      search: '',
      page: 1,
      per: 999999,
      ...params
    });

    const pembelianData = pembelianRes.data.data || [];

    // Calculate totals
    dashboardData.value = {
      ...dashboardData.value,
      totalPembelian: pembelianData.filter(item => 
        item.status_pembayaran === 'Successfully'
      ).length,
      totalPendapatan: pembelianData.reduce((total, item) => {
        if (item.status_pembayaran === 'Successfully') {
          return total + (parseFloat(item.total_harga) || 0);
        }
        return total;
      }, 0)
    };

    // Only fetch additional data for non-mitra users
    if (!isMitra.value) {
      const [konserRes, userRes] = await Promise.all([
        axios.get('/getKonser'),
        axios.get('master/users')
      ]);

      dashboardData.value.totalKonser = konserRes.data.data?.length || 0;
      dashboardData.value.totalUsers = userRes.data.data?.length || 0;
    }

    // Update monthly stats
    const monthlyStats = Array(12).fill().map(() => ({ tickets: 0, revenue: 0 }));
    pembelianData.forEach(item => {
      if (item.status_pembayaran === 'Successfully') {
        const date = new Date(item.tanggal_pemesan);
        const month = date.getMonth();
        monthlyStats[month].tickets++;
        monthlyStats[month].revenue += parseFloat(item.total_harga) || 0;
      }
    });
    dashboardData.value.monthlyStats = monthlyStats;

    updateCharts();
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
    toast.error('Gagal memuat data dashboard');
  } finally {
    isLoading.value = false;
  }
};

// Chart base configuration
const chartBase = {
  sales: {
    chart: {
      type: 'line',
      height: 350,
      toolbar: { show: true },
      zoom: { enabled: true }
    },
    colors: ['#4e73df', '#1cc88a'],
    xaxis: {
      categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    }
  },
  revenue: {
    chart: {
      type: 'donut',
      height: 350
    },
    colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
    labels: ['Regular', 'VIP', 'VVIP', 'Others']
  }
};

// Chart state
const chartState = ref({
  sales: {
    ...chartBase.sales,
    series: [{
      name: 'Jumlah Tiket',
      data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
    }, {
      name: 'Pendapatan (Juta)',
      data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
    }]
  },
  revenue: {
    ...chartBase.revenue,
    series: [0, 0, 0, 0]
  }
});

// Update charts with real data
const updateCharts = () => {
  if (!dashboardData.value.monthlyStats) return;

  chartState.value.sales.series = [
    {
      name: 'Jumlah Tiket',
      data: dashboardData.value.monthlyStats.map(m => m.tickets)
    },
    {
      name: 'Pendapatan (Juta)',
      data: dashboardData.value.monthlyStats.map(m => Math.round(m.revenue / 1000000))
    }
  ];

  chartState.value.revenue.series = [
    dashboardData.value.ticketTypeStats.Regular || 0,
    dashboardData.value.ticketTypeStats.VIP || 0,
    dashboardData.value.ticketTypeStats.VVIP || 0,
    dashboardData.value.ticketTypeStats.Others || 0
  ];
};

// Computed properties for formatted values
const formattedStats = computed(() => ({
  pendapatan: formatCurrency(dashboardData.value.totalPendapatan),
  konser: dashboardData.value.totalKonser,
  pembelian: dashboardData.value.totalPembelian,
  users: dashboardData.value.totalUsers
}));

onMounted(() => {
  fetchDashboardData();
});
</script>

<template>
  <main>
    <div class="card mb-10">
      <!--begin::Heading-->
      <div
        class="card-header rounded bgi-no-repeat bgi-size-cover bgi-position-y-top bgi-position-x-center align-items-start h-200px bg-warning">
        <!--begin::Title-->
        <h3 class="card-title align-items-start flex-column text-white pt-10">
          <span class="fw-bold fs-2x">D A S H B O A R D</span>
        </h3>
        <div class="d-flex justify-content-end mt-10">
          <!-- <select2 placeholder="Pilih Tahun" class="form-select-solid mw-200px mw-md-100" name="tahun" :options="tahuns"
            v-model="tahun">
          </select2> -->
        </div>
        <!--end::Title-->
      </div>
      <!--end::Heading-->

      <!--begin::Body-->
      <div class="card-body mt-n20">
        <!--begin::Stats-->
        <div class="mt-n20 position-relative">
          <!--begin::Row-->
          <div class="row g-3 g-lg-6">
            <!-- Show these cards only for non-mitra users -->
            <template v-if="!isMitra">
              <div class="col-md-3">
                <!-- Total Konser Card -->
                <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 border-info border-left-5 border-start">
                  <!--begin::Symbol-->
                  <div class="symbol symbol-30px me-5 mb-8">
                    <span class="symbol-label">
                      <i class="ki-duotone ki-profile-user fs-2x text-info">
                        <i class="path1"></i>
                        <i class="path2"></i>
                        <i class="path3"></i>
                        <i class="path4"></i>
                      </i>
                    </span>
                  </div>
                  <!--end::Symbol-->

                  <!--begin::Stats-->
                  <div class="m-0">
                    <span v-if="isLoading" class="placeholder-glow">
                      <span class="placeholder col-6"></span>
                    </span>
                    <span v-else class="text-info fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1">
                      {{ dashboardData.totalKonser }}
                    </span>
                    <!--begin::Desc-->
                    <span class="text-gray-500 fw-semibold fs-6">Total Konser</span>
                    <!--end::Desc-->
                  </div>
                  <!--end::Stats-->
                </div>
              </div>
            </template>

            <!-- Show these cards for all users -->
            <div :class="isMitra ? 'col-md-6' : 'col-md-3'">
              <!-- Total Pembelian Card -->
              <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 border-success border-left-5 border-start">
                <!--begin::Symbol-->
                <div class="symbol symbol-30px me-5 mb-8">
                  <span class="symbol-label">
                    <i class="ki-duotone ki-abstract-46 fs-2x text-success">
                      <i class="path1"></i>
                      <i class="path2"></i>
                    </i>
                  </span>
                </div>
                <!--end::Symbol-->

                <!--begin::Stats-->
                <div class="m-0">
                  <!--begin::Number-->
                  <span class="text-success fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1">{{ formattedStats.pembelian
                    }}</span>
                  <!--end::Number-->

                  <!--begin::Desc-->
                  <span class="text-gray-500 fw-semibold fs-6">Total Pembelian Tiket</span>
                  <!--end::Desc-->
                </div>
                <!--end::Stats-->
              </div>
            </div>

            <div :class="isMitra ? 'col-md-6' : 'col-md-3'">
              <!-- Total Pendapatan Card -->
              <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 border-success border-left-5 border-start">
                <!--begin::Symbol-->
                <div class="symbol symbol-30px me-5 mb-8">
                  <span class="symbol-label">
                    <i class="ki-duotone ki-dollar fs-2x text-success">
                      <i class="path1"></i>
                      <i class="path2"></i>
                      <i class="path3"></i>
                    </i>
                  </span>
                </div>

                <!--begin::Stats-->
                <div class="m-0">
                  <!--begin::Number-->
                  <span class="text-success fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1">{{ formattedStats.pendapatan }}</span>
                  <!--end::Number-->

                  <!--begin::Desc-->
                  <span class="text-gray-500 fw-semibold fs-6">Jumlah Saldo Pembelian Tiket</span>
                  <!--end::Desc-->
                </div>
                <!--end::Stats-->
              </div>
            </div>

            <!-- Show user card only for non-mitra users -->
            <template v-if="!isMitra">
              <div class="col-md-3">
                <!-- Total Users Card -->
                <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 border-info border-left-5 border-start">
                  <!--begin::Symbol-->
                  <div class="symbol symbol-30px me-5 mb-8">
                    <span class="symbol-label">
                      <i class="ki-duotone ki-profile-user fs-2x text-info">
                        <i class="path1"></i>
                        <i class="path2"></i>
                        <i class="path3"></i>
                        <i class="path4"></i>
                      </i>
                    </span>
                  </div>
                  <!--end::Symbol-->

                  <!--begin::Stats-->
                  <div class="m-0">
                    <span v-if="isLoading" class="placeholder-glow">
                      <span class="placeholder col-6"></span>
                    </span>
                    <span v-else class="text-info fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1">
                      {{ dashboardData.totalUsers }}
                    </span>
                    <!--end::Number-->

                    <!--begin::Desc-->
                    <span class="text-gray-500 fw-semibold fs-6">Data Pengguna</span>
                    <!--end::Desc-->
                  </div>
                  <!--end::Stats-->
                </div>
              </div>
            </template>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Sales Chart -->
              <div class="card">
                <div class="card-body">
                  <h3 class="card-title">{{ isMitra ? 'Statistik Konser Anda' : 'Statistik Semua Konser' }}</h3>
                  <apexchart
                    v-if="!isLoading"
                    type="line"
                    height="350"
                    :options="chartState.sales"
                    :series="chartState.sales.series"
                  ></apexchart>
                  <div v-else class="chart-placeholder"></div>
                </div>
              </div>

              <!-- Revenue Chart -->
              <!-- <div class="card">
                <div class="card-body">
                  <apexchart
                    v-if="!isLoading"
                    type="donut"
                    height="350"
                    :options="chartState.revenue"
                    :series="chartState.revenue.series"
                  ></apexchart>
                  <div v-else class="chart-placeholder"></div>
                </div>
              </div> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
.stat-card {
  padding: 1.5rem;
  border-radius: 0.5rem;
  background: white;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  border-left: 4px solid;
}

.chart-placeholder {
  height: 350px;
  background: #f8f9fa;
  border-radius: 0.5rem;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: bold;
  margin-top: 0.5rem;
}

.placeholder {
  background-color: #e9ecef;
  border-radius: 0.25rem;
}

.placeholder-glow {
  display: block;
  min-height: 1.5rem;
}
</style>
