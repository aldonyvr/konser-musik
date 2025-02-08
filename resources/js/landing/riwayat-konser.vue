<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import KTHeader from "@/layouts/default-layout/components/header/NavbarLanding.vue";
import Footer from '@/Layouts/default-layout/components/footer/Footer.vue';
import { computed } from 'vue';
import bwipjs from 'bwip-js';
import axios from "@/libs/axios";
import html2canvas from 'html2canvas';
import { currency } from "@/libs/utils"; // Add this import
import QRCode from 'qrcode';

const activeTickets = computed(() => tickets.value.filter(ticket => ticket.status === 'active'));
const completedHistory = computed(() => purchaseHistory.value.filter(history => history.status === 'Selesai'));
const route = useRoute();
const activeTab = ref('tickets');
const ticketRefs = ref<any>([]);

const tickets = ref([]);
const isLoading = ref(true);

const generateBarcodeImage = async (code: string) => {
    try {
        const canvas = await bwipjs.toCanvas('', {
            bcid: 'code128',       // Barcode type
            text: code,            // Text to encode
            scale: 3,              // Scale factor
            height: 10,            // Bar height, in millimeters
            includetext: true,     // Show human-readable text
            textxalign: 'center',  // Center the text
        });
        return canvas.toDataURL('image/png');
    } catch (error) {
        console.error('Error generating barcode:', error);
        return '';
    }
};

const generateQRCode = async (uuid: string) => {
    try {
        return await QRCode.toDataURL(uuid, {
            width: 300,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#ffffff'
            }
        });
    } catch (error) {
        console.error('Error generating QR code:', error);
        return '';
    }
};

const fetchPurchasedTickets = async () => {
    try {
        const response = await axios.get('datapemesan/purchased-tickets'); // Use your authenticated axios instance
        tickets.value = await Promise.all(response.data.map(async ticket => ({
            id: ticket.id,
            name: ticket.tiket?.konsers?.title || '',
            date: ticket.tiket?.konsers?.tanggal || '',
            time: ticket.tiket?.opengate || '',
            location: ticket.tiket?.konsers?.lokasi || '',
            seat: ticket.gate_type === 'VIP' ? 'VIP-' + ticket.id : null,
            row: ticket.gate_type === 'VIP' ? String.fromCharCode(65 + (ticket.id % 26)) : null,
            price: currency(ticket.total_harga),
            barcodeImage: await generateBarcodeImage(ticket.uuid), // Generate barcode image
            qrcode: await generateQRCode(ticket.uuid),
            status: ticket.status_pembayaran === 'Successfully' ? 'active' : 'pending',
            image: ticket.tiket.konsers?.image || '',
            gate: ticket.gate_type || 'VIP',
            organizer: 'Event Organizer'
        })));
    } catch (error) {
        console.error('Error fetching tickets:', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    // Set active tab from route params if available
    if (route.params.tab) {
        activeTab.value = route.params.tab as string;
    }
    fetchPurchasedTickets();
});

const downloadTicket = async (ticketId: number) => {
    const ticketElement = document.getElementById(`ticket-${ticketId}`);
    if (!ticketElement) {
        console.warn(`Elemen tiket dengan ID ${ticketId} tidak ditemukan.`);
        return;
    }

    try {
        const canvas = await html2canvas(ticketElement, { scale: 2 });
        const link = document.createElement('a');
        link.download = `ticket-${ticketId}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    } catch (error) {
        console.error('Gagal mengunduh tiket:', error);
    }
};

const generateBarcode = (data: string) => {
    return new Promise((resolve, reject) => {
        bwipjs.toBuffer(
            {
                bcid: 'code128',
                text: data,
                scale: 3,
                height: 10,
                includetext: true,
            },
            (err, png) => {
                if (err) reject(err);
                else resolve(URL.createObjectURL(new Blob([png], { type: 'image/png' })));
            }
        );
    });
};

</script>

<template>
    <nav>
        <KTHeader />
    </nav>

    <div class="mt-20"></div>
    <div class="container flex-grow-1 mt-15">
        <!-- Tab Buttons -->
        <div class="d-flex mb-10">
            <div class="btn-group" role="group" aria-label="Ticket Views">
                <button @click="activeTab = 'tickets'" class="btn btn-lg"
                    :class="activeTab === 'tickets' ? 'btn-primary' : 'btn-outline-primary'">
                    <i class="fas fa-ticket-alt me-2"></i>
                    Tiket Anda
                </button>
                <button @click="activeTab = 'history'" class="btn btn-lg"
                    :class="activeTab === 'history' ? 'btn-primary' : 'btn-outline-primary'">
                    <i class="fas fa-history me-2"></i>
                    Riwayat Konser
                </button>
            </div>
        </div>

        <!-- Tickets View -->
        <div v-if="activeTab === 'tickets'" class="tickets-container">
            <div class="text-center mb-10" style="font-family: 'Lobster', cursive; font-size: 25px;">
                Tiket Konser Anda
            </div>

            <div v-if="isLoading" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            
            <!-- Tickets List -->
            <div v-else-if="tickets.length > 0" class="tickets-wrapper">
                <div v-for="ticket in tickets" :key="ticket.id" class="mb-8">
                    <!-- Downloadable Ticket -->
                    <div :id="`ticket-${ticket.id}`" class="ticket-full">
                        <!-- Left Section -->
                        <div class="ticket-left">
                            <div class="event-details flex items-center gap-4">
                                <img :src="ticket.image" class="event-image w-40 h-40 object-cover rounded-lg"
                                    alt="Concert Image">
                                <div>
                                    <h3 class="event-name text-xl font-bold ">{{ ticket.name }}</h3>
                                    <div class="event-info mt-2 space-y-1 d-flex ">
                                        <p class="info-item flex items-center">
                                            <i class="fas fa-calendar"></i>
                                            <span>{{ ticket.date }}</span>
                                        </p>
                                        <p class="info-item flex items-center">
                                            <i class="fas fa-clock ms-5"></i>
                                            <span>{{ ticket.time }}</span>
                                        </p>
                                        <p class="info-item flex items-center">
                                            <i class="fas fa-map-marker-alt ms-5"></i>
                                            <span>{{ ticket.location }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="ticket-details">
                                <div class="detail-row">
                                    <span class="detail-label">Gate</span>
                                    <span class="detail-value">{{ ticket.gate || 'VIP' }}</span>
                                </div>
                                <!-- Only show seat for VIP tickets -->
                                <template v-if="ticket.gate === 'VIP'">
                                    <div class="detail-row">
                                        <span class="detail-label">Seat</span>
                                        <span class="detail-value">{{ ticket.gate }}</span>
                                    </div>
                                </template>
                                <div class="detail-row">
                                    <span class="detail-label">Price</span>
                                    <span class="detail-value">{{ ticket.price }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Section -->
                        <div class="ticket-right">
                            <div class="qrcode-section">
                                <img :src="ticket.qrcode" 
                                     alt="QR Code" 
                                     class="qrcode mt-7" />
                                <p class="mt-2">Scan for entry</p>
                            </div>
                            <div class="organizer-info">
                                <p>{{ ticket.organizer }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Download Button -->
                    <div class="text-center mt-4">
                        <button @click="downloadTicket(ticket.id)" class="download-btn">
                            <i class="fas fa-download me-2"></i>
                            Download Tiket
                        </button>
                    </div>
                </div>
            </div>

            <!-- No Tickets Message -->
            <div v-else class="text-center text-muted mt-5">
                <p class="lead">Anda belum memiliki tiket konser.</p>
            </div>
        </div>

        <!-- Purchase History View -->
        <div v-if="activeTab === 'history'">
            <div class="text-center mb-10" style="font-family: 'Lobster', cursive; font-size: 25px;">
                Riwayat Pembelian Tiket Konser
            </div>

            <!-- No History Message -->
            <div v-if="purchaseHistory.length === 0" class="text-center text-muted mt-5">
                <p class="lead">Tidak ada riwayat pembelian tiket konser ditemukan.</p>
            </div>

            <!-- History List -->
            <div v-else class="row mt-15 mb-20">
                <div v-for="purchase in purchaseHistory" :key="purchase.id" class="col-md-6 mb-7">
                    <div class="card shadow-lg hover-shadow">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <img :src="purchase.image" class="img-fluid rounded-start align-items-center"
                                    alt="Concert Image">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="card-title">Pembelian</h5>
                                        <span class="badge bg-success text-light align-self-center">{{ purchase.status
                                            }}</span>
                                    </div>
                                    <hr>
                                    <h5 class="text-primary">Tiket {{ purchase.name }}</h5>
                                    <p class="card-text">
                                        <i class="fa-solid fa-calendar-day text-danger me-2"></i>
                                        Masa berlaku {{ purchase.date }} sampai {{ purchase.date }}
                                    </p>
                                    <p class="card-text">
                                        <i class="fa-solid fa-location-dot text-success me-2"></i>
                                        {{ purchase.location }}
                                    </p>
                                    <p class="fw-bold">Total Pembayaran: {{ purchase.price }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="h-full flex bottom-0 justify-content-end items-end">
        <Footer />
    </div>
</template>

<style scoped>
.tickets-wrapper {
    max-width: 1500px;
}

.ticket-full {
    display: flex;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 10px;
    margin-bottom: 2rem;
    position: relative;

}

.ticket-left {
    flex: 2;
    padding: 0.8rem;
    display: flex;
    gap: 2rem;
    position: relative;
}

.ticket-right {
    flex: 1;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    position: relative;
    border-left: 2px dashed #dee2e6;
}

.event-details {
    flex: 1.5
}

.ticket-details {
    flex: 1;
    border-left: 1px solid #dee2e6;
    padding-left: 2rem;
}

.event-image {
    height: 150px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 1rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.event-name {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #2b2b2b;
}

.event-info {
    color: #666;
}

.info-item {
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #eee;
}

.detail-label {
    color: #666;
    font-weight: 500;
}

.detail-value {
    color: #2b2b2b;
    font-weight: 600;
}

.organizer-info {
    text-align: center;
    color: #666;
    font-size: 0.9rem;
}

.download-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.download-btn:hover {
    background-color: #45a049;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
}

.qrcode {
    width: 100px;
    height: 100px;
    margin: 0 auto;
}

.qrcode-section {
    text-align: center;
}

@media (max-width: 768px) {
    .ticket-full {
        flex-direction: column;
    }

    .ticket-left {
        flex-direction: column;
        gap: 1rem;
    }

    .ticket-details {
        border-left: none;
        border-top: 1px solid #dee2e6;
        padding-left: 0;
        padding-top: 1rem;
    }

    .ticket-right {
        border-left: none;
        border-top: 2px dashed #dee2e6;
    }
}
</style>