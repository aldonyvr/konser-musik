<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import KTHeader from "@/layouts/default-layout/components/header/NavbarLanding.vue";
import Footer from '@/Layouts/default-layout/components/footer/Footer.vue';
import axios from 'axios';

// Router and state management
const route = useRoute();
const router = useRouter();
const tickets = ref([]);
const isLoading = ref(true);
const paymentStatus = ref('processing');
const paymentMessage = ref('');
const showPaymentFailed = ref(false);
const paymentTitle = ref('');

// Constants
const PAYMENT_TIMEOUT = 900000; // 15 minutes in milliseconds
const ERROR_MESSAGES = {
  SYSTEM_ERROR: 'Terjadi kesalahan sistem. Silakan coba beberapa saat lagi.',
  EXPIRED: 'Sesi pembayaran Anda telah berakhir. Silakan melakukan pemesanan ulang.',
  FAILED: 'Mohon maaf, pembayaran Anda gagal diproses. Silakan coba lagi.',
  NETWORK_ERROR: 'Koneksi terputus. Periksa koneksi internet Anda dan coba lagi.'
};

// Fetch ticket details
const fetchTickets = async (orderId: string) => {
  try {
    const response = await axios.get(`/datapemesanan/show/${orderId}`);
    tickets.value = response.data;
  } catch (error) {
    console.error('Error fetching tickets:', error);
    showPaymentError(
      'Gagal Memuat Data',
      'Tidak dapat memuat detail tiket. Silakan muat ulang halaman.'
    );
  } finally {
    isLoading.value = false;
  }
};

// Handle payment status updates
const handlePaymentUpdate = async (result: any, status: string) => {
  try {
    await axios.post('datapemesan/payment-callback', {
      order_id: route.query.order_id,
      transaction_status: status === 'success' ? 'settlement' : 
                         status === 'pending' ? 'pending' : 'expire',
      fraud_status: status === 'success' ? 'accept' : 'deny'
    });

    paymentStatus.value = status;

    if (status === 'success') {
      await fetchTickets(route.query.order_id as string);
    } else if (status === 'error' || status === 'failed') {
      showPaymentError(
        'Pembayaran Gagal',
        ERROR_MESSAGES.FAILED
      );
    }
  } catch (error) {
    console.error('Error updating payment:', error);
    showPaymentError(
      'Kesalahan Sistem',
      ERROR_MESSAGES.SYSTEM_ERROR
    );
  }
};

// Handle expired payments
const handlePaymentExpired = async (orderId: string) => {
  try {
    await Promise.all([
      axios.post('datapemesan/payment-callback', {
        order_id: orderId,
        transaction_status: 'expire',
        fraud_status: 'deny'
      }),
      axios.post('datapemesan/handle-expired-payment', {
        order_id: orderId
      })
    ]);

    showPaymentError(
      'Waktu Pembayaran Habis',
      ERROR_MESSAGES.EXPIRED
    );
  } catch (error) {
    console.error('Error handling expired payment:', error);
    showPaymentError(
      'Kesalahan Sistem',
      ERROR_MESSAGES.SYSTEM_ERROR
    );
  }
};

// Show payment error with animation
const showPaymentError = (title: string, message: string) => {
  paymentStatus.value = 'failed';
  showPaymentFailed.value = true;
  
  // Animate the error message appearance
  setTimeout(() => {
    paymentTitle.value = title;
    paymentMessage.value = message;
  }, 300);
};

// Navigate to retry payment
const retryPayment = () => {
  router.push('/');
};

// Component setup
onMounted(() => {
  const token = route.query.token as string;
  const orderId = route.query.order_id as string;

  if (!token || !orderId) {
    router.push('/');
    return;
  }

  // Payment timeout handler
  const paymentTimeout = setTimeout(() => {
    if (['processing', 'pending'].includes(paymentStatus.value)) {
      handlePaymentExpired(orderId);
    }
  }, PAYMENT_TIMEOUT);

  // Initialize Midtrans payment
  window.snap.pay(token, {
    onSuccess: async (result) => {
      clearTimeout(paymentTimeout);
      await handlePaymentUpdate(result, 'success');
      router.push({ name: 'riwayat-konser', params: { tab: 'tickets' }});
    },
    onPending: async (result) => {
      await handlePaymentUpdate(result, 'pending');
    },
    onError: async (result) => {
      clearTimeout(paymentTimeout);
      await handlePaymentUpdate(result, 'error');
    },
    onClose: async () => {
      clearTimeout(paymentTimeout);
      if (['processing', 'pending'].includes(paymentStatus.value)) {
        await handlePaymentExpired(orderId);
      }
    }
  });

  // Cleanup
  return () => {
    clearTimeout(paymentTimeout);
  };
});
</script>

<template>
  <div class="payment-page min-h-screen bg-gray-50">
    <nav>
      <KTHeader />
    </nav>

    <main class="container mx-auto px-4 py-12 mt-20">
      <div class="max-w-2xl mx-auto">
        <!-- Payment Failed State -->
        <div 
          v-if="showPaymentFailed"
          class="payment-failed  rounded-xl shadow-lg p-8 text-center transform transition-all duration-300"
        >
          <div class="error-icon mb-6 animate-bounce">
            <i class="fas fa-exclamation-circle text-red-500 text-6xl"></i>
          </div>

          <h2 class="text-2xl font-bold mb-4 text-gray-800">
            {{ paymentTitle }}
          </h2>

          <p class="text-gray-600 text-lg mb-8">
            {{ paymentMessage }}
          </p>

          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button 
              @click="retryPayment"
              class="btn btn-primary me-2 flex items-center justify-center gap-2"
            >
              <i class="fas fa-shopping-cart"></i>
              <span>Pesan Ulang</span>
            </button>

            <button 
              @click="() => router.push('/riwayat-konser')"
              class="btn btn-secondary flex items-center justify-center gap-2"
            >
              <i class="fas fa-history"></i>
              <span>Lihat Riwayat</span>
            </button>
          </div>

          <p class="mt-8 text-sm text-gray-500">
            Butuh bantuan? 
            <a href="/contact" class="text-blue-600 hover:text-blue-700">
              Hubungi kami
            </a>
          </p>
        </div>

        <!-- Loading State -->
        <div 
          v-else-if="isLoading || paymentStatus === 'processing'"
          class="loading-state bg-white rounded-xl shadow-lg p-8 text-center"
        >
          <div class="loading-spinner mb-6">
            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-blue-500 border-opacity-50 mx-auto"></div>
          </div>

          <h3 class="text-xl font-semibold mb-2">
            Memproses Pembayaran
          </h3>

          <p class="text-gray-600">
            Mohon tunggu sebentar, jangan tutup halaman ini
          </p>
        </div>

        <!-- Success State -->
        <div 
          v-else-if="paymentStatus === 'success'"
          class="success-state bg-white rounded-xl shadow-lg p-8"
        >
          <div class="text-center mb-8">
            <div class="success-icon mb-4">
              <i class="fas fa-check-circle text-green-500 text-6xl"></i>
            </div>

            <h2 class="text-2xl font-bold text-gray-800">
              Pembayaran Berhasil!
            </h2>
          </div>

          <!-- Ticket display would go here -->
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<style scoped>

.payment-failed {
  opacity: 0;
  animation: fadeInScale 0.5s ease-out forwards;
}


.loading-spinner {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes fadeInScale {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Print styles */
@media print {
  .payment-page {
    background: white;
  }

  nav, .btn-primary, .btn-secondary, Footer {
    display: none;
  }
}
</style>