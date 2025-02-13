<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import KTHeader from "@/layouts/default-layout/components/header/NavbarLanding.vue";
import Footer from '@/Layouts/default-layout/components/footer/Footer.vue';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const tickets = ref([]);
const isLoading = ref(true);
const paymentStatus = ref('processing');

// Function to fetch ticket details
const fetchTickets = async (orderId: string) => {
    try {
        const response = await axios.get(`/datapemesanan/show/${orderId}`);
        tickets.value = response.data;
        isLoading.value = false;
    } catch (error) {
        console.error('Error fetching tickets:', error);
        isLoading.value = false;
    }
};

const handlePaymentUpdate = async (result) => {
    try {
        // Send payment result to backend
        await axios.post('datapemesan/payment-callback', {
            order_id: route.query.order_id,
            transaction_status: result.transaction_status,
            fraud_status: result.fraud_status
        });

        console.log('Payment status updated:', result);
    } catch (error) {
        console.error('Error updating payment status:', error);
    }
};

onMounted(() => {
    const token = route.query.token as string;
    const orderId = route.query.order_id as string;

    if (!token) {
        router.push('/');
        return;
    }

    // Initialize Midtrans payment
    window.snap.pay(token, {
        onSuccess: async function (result) {
            paymentStatus.value = 'success';
            try {
                await handlePaymentUpdate(result);
                // Add delay before redirect to ensure data is processed
                setTimeout(() => {
                    router.push({ 
                        name: 'riwayat-konser',  // Make sure this matches your route name
                        params: { tab: 'tickets' }
                    });
                }, 1500);
            } catch (error) {
                console.error('Error processing payment:', error);
            }
        },
        onPending: async function (result) {
            paymentStatus.value = 'pending';
            await handlePaymentUpdate(result);
            alert('Pembayaran pending. Silahkan selesaikan pembayaran Anda.');
            router.push('/invoice/' + orderId);
        },
        onError: async function (result) {
            paymentStatus.value = 'error';
            await handlePaymentUpdate(result);
            alert('Pembayaran gagal.');
            console.error('Payment Error:', result);
            router.push('/payment');
        },
        onClose: function () {
            if (paymentStatus.value !== 'success') {
                alert('Anda menutup jendela pembayaran sebelum menyelesaikan pembayaran.');
                router.push('/payment');
            }
        }
    });
});
</script>

<template>
    <div>
        <nav>
            <KTHeader />
        </nav>
        <div class="container mt-20">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <!-- Loading State -->
                    <div v-if="isLoading" class="payment-loading text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h3 class="mt-3">Memuat halaman pembayaran...</h3>
                        <p>Mohon tunggu sebentar</p>
                    </div>

                    <!-- Success State with Tickets -->
                    <div v-else-if="paymentStatus === 'success'" class="payment-success">
                        <div class="success-header text-center mb-4">
                            <div class="success-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h2>Pembayaran Berhasil!</h2>
                        </div>


                    </div>
                </div>
            </div>
        </div>
        <Footer />
    </div>
</template>

<style scoped>
.payment-loading {
    margin-top: 100px;
    margin-bottom: 100px;
    padding: 40px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

.payment-success {
    margin: 2rem 0;
}

.success-icon {
    font-size: 4rem;
    color: #28a745;
    margin-bottom: 1rem;
}

.ticket-container {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.ticket-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.2s;
}

.ticket-card:hover {
    transform: translateY(-2px);
}

.ticket-header {
    background: #f8f9fa;
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
}

.ticket-header h3 {
    margin: 0;
    color: #333;
}

.ticket-type {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: #e9ecef;
    border-radius: 20px;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.ticket-body {
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ticket-info p {
    margin-bottom: 0.5rem;
}

.ticket-qr {
    width: 120px;
    height: 120px;
    padding: 0.5rem;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
}

.ticket-qr img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.ticket-footer {
    padding: 1rem;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

@media print {
    .ticket-card {
        break-inside: avoid;
        margin-bottom: 2rem;
    }

    nav, .ticket-footer, Footer {
        display: none;
    }
}
</style>