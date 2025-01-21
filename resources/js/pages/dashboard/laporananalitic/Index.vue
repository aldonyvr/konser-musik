<script setup lang="ts">
import VueApexCharts from "vue3-apexcharts";
import { ref, onMounted } from 'vue';

// Sample data - replace with actual data from your backend
const totalRevenue = ref("Rp 2.5M");
const totalTickets = ref("12,845");
const averageOccupancy = ref("78%");
const upcomingConcerts = ref("15");

// Monthly Revenue Chart
const revenueChartOptions = ref({
  chart: {
    type: 'area',
    height: 350,
    toolbar: {
      show: true
    }
  },
  colors: ['#4e73df'],
  series: [{
    name: 'Pendapatan',
    data: [42000, 55000, 58000, 61000, 85000, 73000, 69000]
  }],
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
    title: {
      text: 'Bulan'
    }
  },
  yaxis: {
    title: {
      text: 'Pendapatan (Ribu Rp)'
    }
  },
  title: {
    text: 'Trend Pendapatan Bulanan',
    align: 'center'
  },
  stroke: {
    curve: 'smooth'
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.7,
      opacityTo: 0.3
    }
  }
});

// Ticket Sales by Category
const ticketCategoryOptions = ref({
  chart: {
    type: 'donut',
    height: 350
  },
  colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
  series: [44, 55, 32, 14],
  labels: ['Regular', 'VIP', 'VVIP', 'Backstage Pass'],
  title: {
    text: 'Penjualan Tiket per Kategori',
    align: 'center'
  }
});

// Attendance Rate Chart
const attendanceChartOptions = ref({
  chart: {
    type: 'bar',
    height: 350
  },
  colors: ['#1cc88a'],
  series: [{
    name: 'Tingkat Kehadiran',
    data: [95, 87, 92, 88, 86, 89, 91]
  }],
  xaxis: {
    categories: ['Rock Fest', 'Jazz Night', 'Pop Gala', 'EDM Party', 'Classic Eve', 'Hip Hop Jam', 'Indie Fest'],
    title: {
      text: 'Nama Konser'
    }
  },
  yaxis: {
    title: {
      text: 'Persentase Kehadiran (%)'
    }
  },
  plotOptions: {
    bar: {
      borderRadius: 4,
      horizontal: false,
      columnWidth: '55%',
    }
  }
});

// Top Performing Concerts
const topConcerts = ref([
  { name: 'Rock Festival 2024', revenue: 'Rp 850,000,000', attendance: '12,500', rating: 4.8 },
  { name: 'Jazz Night Special', revenue: 'Rp 650,000,000', attendance: '8,900', rating: 4.7 },
  { name: 'Pop Music Gala', revenue: 'Rp 550,000,000', attendance: '7,500', rating: 4.6 },
  { name: 'EDM Night Party', revenue: 'Rp 450,000,000', attendance: '6,200', rating: 4.5 },
  { name: 'Classical Evening', revenue: 'Rp 350,000,000', attendance: '4,800', rating: 4.7 }
]);
</script>

<template>
  <div class="p-6">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold mb-2">Dashboard Analytics Konser</h1>
      <p class="text-gray-600">Overview performa konser dan penjualan tiket</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <!-- Total Revenue Card -->
      <div class="p-4 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Total Pendapatan</h3>
        <p class="text-2xl font-bold">{{ totalRevenue }}</p>
        <span class="text-green-500 text-sm">+15.3% dari bulan lalu</span>
      </div>

      <!-- Total Tickets Card -->
      <div class="p-4 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Total Tiket Terjual</h3>
        <p class="text-2xl font-bold">{{ totalTickets }}</p>
        <span class="text-green-500 text-sm">+8.2% dari bulan lalu</span>
      </div>

      <!-- Average Occupancy Card -->
      <div class="p-4 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Rata-rata Okupansi</h3>
        <p class="text-2xl font-bold">{{ averageOccupancy }}</p>
        <span class="text-green-500 text-sm">+5.7% dari bulan lalu</span>
      </div>

      <!-- Upcoming Concerts Card -->
      <div class="p-4 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Konser Mendatang</h3>
        <p class="text-2xl font-bold">{{ upcomingConcerts }}</p>
        <span class="text-blue-500 text-sm">Dalam 3 bulan kedepan</span>
      </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <!-- Revenue Trend Chart -->
      <div class="p-4 rounded-lg shadow">
        <apexchart
          type="area"
          height="350"
          :options="revenueChartOptions"
          :series="revenueChartOptions.series"
        ></apexchart>
      </div>

      <!-- Ticket Categories Chart -->
      <div class="p-4 rounded-lg shadow">
        <apexchart
          type="donut"
          height="350"
          :options="ticketCategoryOptions"
          :series="ticketCategoryOptions.series"
        ></apexchart>
      </div>

      <!-- Attendance Rate Chart -->
      <div class="p-4 rounded-lg shadow">
        <apexchart
          type="bar"
          height="350"
          :options="attendanceChartOptions"
          :series="attendanceChartOptions.series"
        ></apexchart>
      </div>
    </div>

    <!-- Top Performing Concerts Table -->
    <div class="p-4 rounded-lg shadow mb-6">
      <h2 class="text-xl font-bold mb-4">Konser Terbaik</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Konser</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pendapatan</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Pengunjung</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="concert in topConcerts" :key="concert.name">
              <td class="px-6 py-4 whitespace-nowrap">{{ concert.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ concert.revenue }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ concert.attendance }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-yellow-500">★</span> {{ concert.rating }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>