<script setup lang="ts">
import VueApexCharts from "vue3-apexcharts";
import { ref, onMounted } from 'vue';

const ticketSalesOptions = ref({
  chart: {
    type: 'line',
    height: 350,
    toolbar: {
      show: true
    },
    zoom: {
      enabled: true
    }
  },
  colors: ['#4e73df', '#1cc88a'],
  series: [{
    name: 'Jumlah Tiket',
    data: [450, 520, 640, 720, 850, 780]
  }, {
    name: 'Pendapatan (Juta Rp)',
    data: [22.5, 26, 32, 36, 42.5, 39]
  }],
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    title: {
      text: 'Bulan'
    }
  },
  yaxis: [
    {
      title: {
        text: "Jumlah Tiket"
      }
    },
    {
      opposite: true,
      title: {
        text: "Pendapatan (Juta Rp)"
      }
    }
  ],
  title: {
    text: 'Trend Penjualan Tiket',
    align: 'center'
  },
  stroke: {
    curve: 'smooth',
    width: 2
  },
  legend: {
    position: 'bottom'
  }
});

// Chart options for Concert Categories
const concertCategoriesOptions = ref({
  chart: {
    type: 'bar',
    height: 350,
    toolbar: {
      show: true
    }
  },
  colors: ['#4e73df'],
  series: [{
    name: 'Jumlah Konser',
    data: [12, 8, 5, 4, 3]
  }],
  xaxis: {
    categories: ['Pop', 'Rock', 'Jazz', 'Classical', 'Others'],
    title: {
      text: 'Kategori'
    }
  },
  yaxis: {
    title: {
      text: 'Jumlah Konser'
    }
  },
  title: {
    text: 'Distribusi Kategori Konser',
    align: 'center'
  },
  plotOptions: {
    bar: {
      borderRadius: 4,
      horizontal: false,
      columnWidth: '55%',
    }
  },
  dataLabels: {
    enabled: false
  },
  legend: {
    position: 'bottom'
  }
});

// Chart options for Monthly Revenue Distribution
const revenueDistributionOptions = ref({
  chart: {
    type: 'donut',
    height: 350
  },
  colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
  series: [44, 55, 32, 14],
  labels: ['Regular', 'VIP', 'VVIP', 'Others'],
  title: {
    text: 'Distribusi Pendapatan per Kategori Tiket',
    align: 'center'
  },
  legend: {
    position: 'bottom'
  },
  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        width: 200
      },
      legend: {
        position: 'bottom'
      }
    }
  }]
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
            <!--begin::Col-->
            <div class="col-md-3">
              <!--begin::Items-->
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
                  <!--begin::Number-->
                  <span class="text-info fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1"></span>
                  <!--end::Number-->

                  <!--begin::Desc-->
                  <span class="text-gray-500 fw-semibold fs-6">Total Konser</span>
                  <!--end::Desc-->
                </div>
                <!--end::Stats-->
              </div>
              <!--end::Items-->
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-md-3">
              <!--begin::Items-->
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
                  <span class="text-success fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1"></span>
                  <!--end::Number-->

                  <!--begin::Desc-->
                  <span class="text-gray-500 fw-semibold fs-6">Total Pembelian Tiket</span>
                  <!--end::Desc-->
                </div>
                <!--end::Stats-->
              </div>
              <!--end::Items-->
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-md-3">
              <!--begin::Items-->
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
                <!--end::Symbol-->

                <!--begin::Stats-->
                <div class="m-0">
                  <!--begin::Number-->
                  <span class="text-success fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1"></span>
                  <!--end::Number-->

                  <!--begin::Desc-->
                  <span class="text-gray-500 fw-semibold fs-6">Jumlah Saldo Pembelian Tiket</span>
                  <!--end::Desc-->
                </div>
                <!--end::Stats-->
              </div>
              <!--end::Items-->
            </div>
            <div class="col-md-3">
              <!--begin::Items-->
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
                  <!--begin::Number-->
                  <span class="text-info fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1"></span>
                  <!--end::Number-->

                  <!--begin::Desc-->
                  <span class="text-gray-500 fw-semibold fs-6">Data Pengguna</span>
                  <!--end::Desc-->
                </div>
                <!--end::Stats-->
              </div>
              <!--end::Items-->
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Ticket Sales Trend Chart -->
      <div class="card">
        <div class="card-body">
          <apexchart
            type="line"
            height="350"
            :options="ticketSalesOptions"
            :series="ticketSalesOptions.series"
          ></apexchart>
        </div>
      </div>

      <!-- Concert Categories Chart -->
      <div class="card">
        <div class="card-body">
          <apexchart
            type="bar"
            height="350"
            :options="concertCategoriesOptions"
            :series="concertCategoriesOptions.series"
          ></apexchart>
        </div>
      </div>

      <!-- Revenue Distribution Chart -->
      <div class="card">
        <div class="card-body">
          <apexchart
            type="donut"
            height="350"
            :options="revenueDistributionOptions"
            :series="revenueDistributionOptions.series"
          ></apexchart>
        </div>
      </div>
    </div>
            </div>  
            </div>
            </div>  
          </div>
  </main>
</template>
