<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Html5Qrcode } from 'html5-qrcode'; // Change import to Html5Qrcode only
import axios from '@/libs/axios';

const scanner = ref(null);
const ticketInfo = ref(null);
const scanStatus = ref('');
const isScanning = ref(true);

onMounted(() => {
    initScanner();
});

onBeforeUnmount(() => {
    if (scanner.value) {
        scanner.value.stop();
    }
});

const initScanner = async () => {
    try {
        scanner.value = new Html5Qrcode("qr-reader");
        const cameras = await Html5Qrcode.getCameras();
        
        if (cameras && cameras.length) {
            const cameraId = cameras[0].id;
            await scanner.value.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                handleScanSuccess,
                handleScanError
            );
        }
    } catch (err) {
        console.error('Error starting scanner:', err);
    }
};

const handleScanSuccess = async (decodedText) => {
    if (!isScanning.value) return;
    
    isScanning.value = false;
    try {
        const response = await axios.post('/scan/scan-ticket', {
            ticket_uuid: decodedText
        });

        if (response.data.success) {
            scanStatus.value = 'success';
            ticketInfo.value = response.data.data;
            playSound('success');
        }
    } catch (error) {
        scanStatus.value = 'error';
        ticketInfo.value = {
            error: error.response?.data?.message || 'QR Code tidak valid',
            type: error.response?.data?.type || 'invalid'
        };
        playSound('error');
    }

    // Reset after 3 seconds
    setTimeout(() => {
        isScanning.value = true;
    }, 3000);
};

const handleScanError = (error) => {
    console.warn(error);
};

const playSound = (type) => {
    const audio = new Audio(`/sounds/${type}.mp3`);
    audio.play();
};
</script>

<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">QR Code Scanner (Camera Only)</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <!-- Camera Scanner -->
                    <div id="qr-reader" class="qr-container"></div>
                </div>
                <div class="col-md-6">
                    <!-- Scan Result -->
                    <div v-if="ticketInfo" 
                         :class="['alert', 
                                 scanStatus === 'success' ? 'alert-success' : 
                                 ticketInfo.type === 'already_used' ? 'alert-warning' : 'alert-danger', 
                                 'fade-in']">
                        <div v-if="scanStatus === 'success'">
                            <h4 class="mb-3">✅ Tiket Valid</h4>
                            <div class="ticket-info">
                                <p><strong>Nama:</strong> {{ ticketInfo.ticket_holder }}</p>
                                <p><strong>Event:</strong> {{ ticketInfo.event_name }}</p>
                                <p><strong>Tipe:</strong> {{ ticketInfo.ticket_type }}</p>
                                <p><strong>Gate:</strong> {{ ticketInfo.gate }}</p>
                                <p><strong>Tanggal:</strong> {{ ticketInfo.event_date }}</p>
                            </div>
                        </div>
                        <div v-else>
                            <h4 class="mb-3" :class="{'text-warning': ticketInfo.type === 'already_used'}">
                                {{ ticketInfo.type === 'already_used' ? '⚠️' : '❌' }} 
                                {{ ticketInfo.type === 'already_used' ? 'Tiket Sudah Digunakan' : 'Tiket Tidak Valid' }}
                            </h4>
                            <p>{{ ticketInfo.error }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.qr-container {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    border: 2px solid #eee;
    border-radius: 8px;
    overflow: hidden;
}

.ticket-info p {
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert {
    padding: 1.5rem;
    margin-bottom: 1rem;
    border-radius: 8px;
}

.alert-success {
    border-left: 5px solid #28a745;
}

.alert-danger {
    border-left: 5px solid #dc3545;
}

.alert-warning {
    background-color: #fff3cd;
    border-left: 5px solid #ffc107;
}

/* Remove file input related styles */
#qr-reader__fileinput,
#qr-reader__filescan_region,
.qr-file-selector {
    display: none !important;
}

/* Hide file selection button */
#html5-qrcode-button-file-selection {
    display: none !important;
}
</style>