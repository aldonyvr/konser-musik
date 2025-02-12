<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Html5QrcodeScanner } from 'html5-qrcode';
import axios from '@/libs/axios';
import { toast } from 'vue3-toastify';

const result = ref('');
const lastScanned = ref(null);
const ticketInfo = ref(null);
const isLoading = ref(false);
let html5QrcodeScanner = null;

onMounted(() => {
    html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", 
        { 
            fps: 10,
            qrbox: { width: 250, height: 250 }
        },
        false
    );

    html5QrcodeScanner.render(onScanSuccess, onScanError);
});

onUnmounted(() => {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear();
    }
});

const onScanSuccess = async (decodedText) => {
    // Prevent duplicate scans
    if (lastScanned.value === decodedText) return;
    lastScanned.value = decodedText;

    try {
        isLoading.value = true;
        const response = await axios.post('/api/verify-ticket', {
            qr_code: decodedText
        });

        ticketInfo.value = response.data;
        
        if (response.data.isValid) {
            toast.success('Tiket Valid!');
            playSuccessSound();
        } else {
            toast.error('Tiket Tidak Valid!');
            playErrorSound();
        }
    } catch (error) {
        console.error('Error verifying ticket:', error);
        toast.error('Gagal memverifikasi tiket');
    } finally {
        isLoading.value = false;
    }
};

const onScanError = (error) => {
    console.warn(`QR Code scan error: ${error}`);
};

const playSuccessSound = () => {
    const audio = new Audio('/sounds/success.mp3');
    audio.play();
};

const playErrorSound = () => {
    const audio = new Audio('/sounds/error.mp3');
    audio.play();
};
</script>

<template>
    <div class="qr-scanner-container">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Scanner QR Code Tiket</h3>
            </div>
            <div class="card-body">
                <div id="qr-reader"></div>
                
                <div v-if="isLoading" class="text-center mt-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div v-if="ticketInfo" class="ticket-info mt-4">
                    <div class="alert" :class="ticketInfo.isValid ? 'alert-success' : 'alert-danger'">
                        <h4 class="alert-heading">
                            {{ ticketInfo.isValid ? 'Tiket Valid!' : 'Tiket Tidak Valid!' }}
                        </h4>
                        <div v-if="ticketInfo.isValid">
                            <p class="mb-0">Nama: {{ ticketInfo.name }}</p>
                            <p class="mb-0">Konser: {{ ticketInfo.concert }}</p>
                            <p class="mb-0">Gate: {{ ticketInfo.gate }}</p>
                            <p class="mb-0">Tanggal: {{ ticketInfo.date }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.qr-scanner-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

#qr-reader {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
}

#qr-reader img {
    margin: auto;
}

.ticket-info {
    max-width: 600px;
    margin: 20px auto;
}
</style>
