<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Html5Qrcode } from 'html5-qrcode'; // Change import to Html5Qrcode only
import axios from '@/libs/axios';

const scanner = ref(null);
const scanResult = ref({
    status: '',
    message: '',
    type: '',
    scanInfo: null
});
const scanStatus = ref('');
const isScanning = ref(true);
const isActive = ref(true);

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
        if (!isActive.value) return;
        
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

const stopScanner = async () => {
    try {
        if (scanner.value) {
            await scanner.value.stop();
            isActive.value = false;
        }
    } catch (err) {
        console.error('Error stopping scanner:', err);
    }
};

const restartScanner = async () => {
    isActive.value = true;
    await initScanner();
};

const handleScanSuccess = async (decodedText) => {
    if (!isScanning.value) return;
    
    isScanning.value = false;
    try {
        const response = await axios.post('/scan/scan-ticket', {
            ticket_uuid: decodedText
        });

        if (response.data.success) {
            scanResult.value = {
                status: 'success',
                message: 'Tiket Valid',
                type: 'valid',
                scanInfo: response.data.data
            };
            playSound('success');
        }
    } catch (error) {
        const errorData = error.response?.data;
        scanResult.value = {
            status: 'error',
            message: errorData?.message || 'QR Code tidak valid',
            type: errorData?.type || 'invalid',
            scanInfo: errorData?.scan_info || null
        };
        
        // Play different sounds based on error type
        if (errorData?.type === 'already_scanned') {
            playSound('warning');
        } else {
            playSound('error');
        }
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">QR Code Scanner (Camera Only)</h3>
            <div>
                <button v-if="isActive" 
                        @click="stopScanner" 
                        class="btn btn-danger btn-sm">
                    <i class="fas fa-stop me-1"></i> Stop Scanner
                </button>
                <button v-else 
                        @click="restartScanner" 
                        class="btn btn-success btn-sm">
                    <i class="fas fa-play me-1"></i> Start Scanner
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <!-- Camera Scanner -->
                    <div id="qr-reader" class="qr-container"></div>
                </div>
                <div class="col-md-6">
                    <!-- Updated Scan Result Display -->
                    <div v-if="scanResult.message" 
                         :class="['alert', {
                             'alert-success': scanResult.type === 'valid',
                             'alert-warning': scanResult.type === 'already_scanned',
                             'alert-danger': scanResult.type === 'invalid' || scanResult.type === 'error'
                         }, 'fade-in']">
                        
                        <!-- Valid Ticket -->
                        <div v-if="scanResult.type === 'valid'">
                            <h4 class="alert-heading">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Tiket Valid
                            </h4>
                            <hr>
                            <div class="ticket-info">
                                <p><strong>Nama:</strong> {{ scanResult.scanInfo.ticket_holder }}</p>
                                <p><strong>Event:</strong> {{ scanResult.scanInfo.event_name }}</p>
                                <p><strong>Gate:</strong> {{ scanResult.scanInfo.gate }}</p>
                                <p><strong>Tanggal:</strong> {{ scanResult.scanInfo.event_date }}</p>
                            </div>
                        </div>

                        <!-- Already Scanned Ticket -->
                        <div v-else-if="scanResult.type === 'already_scanned'">
                            <h4 class="alert-heading">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                Tiket Sudah Digunakan!
                            </h4>
                            <hr>
                            <div class="ticket-info">
                                <p><strong>Waktu Scan:</strong> {{ scanResult.scanInfo.scanned_at }}</p>
                                <p><strong>Nama:</strong> {{ scanResult.scanInfo.ticket_holder }}</p>
                                <p><strong>Event:</strong> {{ scanResult.scanInfo.event_name }}</p>
                            </div>
                        </div>

                        <!-- Invalid Ticket -->
                        <div v-else>
                            <h4 class="alert-heading">
                                <i class="fas fa-times-circle text-danger me-2"></i>
                                {{ scanResult.message }}
                            </h4>
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
    position: relative;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
    color: #856404;
}

.alert-heading {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.ticket-info {
    padding: 1rem;
    border-radius: 6px;
}

.fade-in {
    animation: fadeInAlert 0.3s ease-in;
}

@keyframes fadeInAlert {
    from { 
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.2rem;
}
</style>