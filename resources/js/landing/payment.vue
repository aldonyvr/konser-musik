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
            await fetchTickets(orderId);
            router.push('/payment');
        },
        onPending: function (result) {
            paymentStatus.value = 'pending';
            alert('Pembayaran pending. Silahkan selesaikan pembayaran Anda.');
            router.push('/invoice/' + orderId);
        },
        onError: function (result) {
            paymentStatus.value = 'error';
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
                            <p>Berikut adalah tiket Anda:</p>
                        </div>

                        <!-- Ticket Cards -->
                        <div class="ticket-container">
                            <div v-for="ticket in tickets" :key="ticket.uuid" class="ticket-card">
                                <div class="ticket-header">
                                    <h3>{{ ticket.event_name }}</h3>
                                    <span class="ticket-type">{{ ticket.ticket_type }}</span>
                                </div>
                                <div class="ticket-body">
                                    <div class="ticket-info">
                                        <p><strong>Ticket ID:</strong> {{ ticket.uuid }}</p>
                                        <p><strong>Nama:</strong> {{ ticket.customer_name }}</p>
                                        <p><strong>Tanggal:</strong> {{ ticket.event_date }}</p>
                                        <p><strong>Lokasi:</strong> {{ ticket.venue }}</p>
                                    </div>
                                    <div class="ticket-qr">
                                        <!-- Add QR Code component here if needed -->
                                        <img :src="'/api/qr-code/' + ticket.uuid" alt="QR Code" />
                                    </div>
                                </div>
                                <div class="ticket-footer">
                                    <button class="btn btn-primary" @click="window.print()">
                                        <i class="fas fa-print"></i> Cetak Tiket
                                    </button>
                                    <button class="btn btn-secondary" @click="downloadTicket(ticket.uuid)">
                                        <i class="fas fa-download"></i> Unduh
                                    </button>
                                </div>
                            </div>
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