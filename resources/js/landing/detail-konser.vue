<script setup lang="ts">
import KTHeader from "@/layouts/default-layout/components/header/NavbarLanding.vue";
import { useRoute, useRouter } from "vue-router";
import Footer from '@/Layouts/default-layout/components/footer/Footer.vue';
import { ref, onMounted, computed, watch } from "vue";
import axios from "@/libs/axios";
import { currency } from "@/libs/utils";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
const route = useRoute();
const router = useRouter();
const tiketId = route.params.uuid;
const ticketDetails = ref<any>(null);
const showLoginModal = ref(false);
const showBookingForm = ref(false);
const vip = ref(0);
const MAX_TOTAL_TICKETS = 5;
const isSubmitting = ref(false);
const totalTickets = computed(() => reguler.value + vip.value);

const ticketPrices = computed(() => ({
    regular: ticketDetails.value?.harga_regular || 0,
    vip: ticketDetails.value?.harga_vip || 0
}));

const availableTickets = computed(() => ({
    regular: ticketDetails.value?.reguler || 0,
    vip: ticketDetails.value?.vip || 0
}));

const ticketForms = ref({
    regular: [] as any[],
    vip: [] as any[]
});

// Add gate management
const selectedGate = ref({
    gate_a: 0,
    gate_b: 0,
    gate_c: 0,
    gate_d: 0,
    gate_e: 0
});

const gateCapacity = computed(() => ({
    gate_a: ticketDetails.value?.gate_a_capacity || 0,
    gate_b: ticketDetails.value?.gate_b_capacity || 0,
    gate_c: ticketDetails.value?.gate_c_capacity || 0
}));

const reguler = computed(() =>
    selectedGate.value.gate_a +
    selectedGate.value.gate_b +
    selectedGate.value.gate_c +
    selectedGate.value.gate_d +
    selectedGate.value.gate_e
);

const totalPrice = computed(() => {
    return (reguler.value * ticketPrices.value.regular) +
        (vip.value * ticketPrices.value.vip);
});

// Add computed property to group regular tickets by gate
const groupedRegularTickets = computed(() => {
    const groups = {
        gate_a: { name: 'Gate A', tickets: [] },
        gate_b: { name: 'Gate B', tickets: [] },
        gate_c: { name: 'Gate C', tickets: [] },
        gate_d: { name: 'Gate D', tickets: [] },
        gate_e: { name: 'Gate E', tickets: [] }
    };

    ticketForms.value.regular.forEach(ticket => {
        const gateKey = ticket.gate;
        if (groups[gateKey]) {
            groups[gateKey].tickets.push(ticket);
        }
    });

    // Only return gates that have tickets
    return Object.fromEntries(
        Object.entries(groups).filter(([_, value]) => value.tickets.length > 0)
    );
});

const updateTicketForms = () => {
    // Handle VIP tickets
    ticketForms.value.vip = Array(vip.value).fill('').map(() => ({
        nama_pemesan: '',
        email_pemesan: '',
        telepon_pemesan: '',
        alamat_pemesan: '',
        type: 'vip',
        total_harga: ticketPrices.value.vip
    }));

    // Handle regular tickets with gates
    ticketForms.value.regular = [];
    Object.entries(selectedGate.value).forEach(([gate, count]) => {
        for (let i = 0; i < count; i++) {
            ticketForms.value.regular.push({
                nama_pemesan: '',
                email_pemesan: '',
                telepon_pemesan: '',
                alamat_pemesan: '',
                type: 'regular',
                gate: gate,
                total_harga: ticketPrices.value.regular,
                gate_name: gates.value.find(g => g.id.includes(gate.split('_')[1]))?.name || gate
            });
        }
    });
};

const checkAuthStatus = async () => {
    try {
        await authStore.fetchUser();
    } catch (error) {
        console.log('Auth error:', error);
        if (error.response?.status === 401) {
            showLoginModal.value = true;
        }
    }
};

const handleBooking = async () => {
    if (!authStore.isAuthenticated) {
        showLoginModal.value = true;
        return;
    }

    if (reguler.value === 0 && vip.value === 0) {
        alert('Please select at least one ticket');
        return;
    }

    showBookingForm.value = true;
};

const submitBooking = async () => {
    try {
        isSubmitting.value = true;

        // Validate required fields
        const allForms = [...ticketForms.value.regular, ...ticketForms.value.vip];
        const isValid = allForms.every(form => {
            return form.nama_pemesan?.trim() &&
                form.email_pemesan?.trim() &&
                form.telepon_pemesan?.trim() &&
                form.alamat_pemesan?.trim();
        });

        if (!isValid) {
            throw new Error('Mohon lengkapi semua data pemesan');
        }

        if (!tiketId) {
            throw new Error('ID tiket tidak valid');
        }

        // Format data for API
        const bookingData = {
            tiketId: ticketDetails.value?.uuid,
            tickets: allForms.map(form => ({
                nama_pemesan: form.nama_pemesan,
                email_pemesan: form.email_pemesan,
                telepon_pemesan: form.telepon_pemesan,
                alamat_pemesan: form.alamat_pemesan,
                type: form.type,
                total_harga: form.type === 'vip' ? ticketPrices.value.vip : ticketPrices.value.regular,
                gate: form.type === 'regular' ? form.gate : null
            })),
            totalPrice: totalPrice.value
        };

        console.log('Sending booking data:', bookingData);

        const response = await axios.post('/datapemesan/store', bookingData);

        if (!response.data.success) {
            throw new Error(response.data.message || 'Gagal membuat pesanan');
        }

        if (!response.data.data?.snap_token) {
            throw new Error('Token pembayaran tidak ditemukan');
        }

        // Redirect to payment page
        router.push({
            path: '/payment',
            query: {
                token: response.data.data.snap_token,
                order_id: response.data.data.order_id
            }
        });

    } catch (error) {
        console.error('Booking error:', error);
        alert(error.message || 'Terjadi kesalahan saat pemesanan');
    } finally {
        isSubmitting.value = false;
    }
};

const getTicketDetails = async () => {
    try {
        console.log("tiketId:", tiketId);
        const response = await axios.get(`/tiket/show/${tiketId}`);
        if (response.data.success) {
            ticketDetails.value = response.data.data;
            console.log('Ticket details:', ticketDetails);
        } else {
            console.error('Failed to get ticket details:', response.data);
            router.push('/');
        }
    } catch (error) {
        console.error("Error fetching ticket details:", error);
        router.push('/');
    }
};

const emit = defineEmits(['close']);
const redirectToSignIn = () => {
    router.push('/sign-in');
    emit('close');
};

const redirectToSignUp = () => {
    router.push('/sign-up');
    emit('close');
};

const handleVipInput = (event: Event) => {
    const value = parseInt((event.target as HTMLInputElement).value) || 0;
    const maxAllowed = Math.min(
        availableTickets.value.vip,
        MAX_TOTAL_TICKETS - reguler.value
    );
    vip.value = Math.min(Math.max(0, value), maxAllowed);
};

const incrementVip = () => {
    if (vip.value < availableTickets.value.vip && totalTickets.value < MAX_TOTAL_TICKETS) {
        vip.value++;
    }
};

const decrementVip = () => {
    if (vip.value > 0) {
        vip.value--;
    }
};

const gates = computed(() => [
    {
        id: 'gate_a_capacity',
        name: 'Gate A',
        capacity: ticketDetails.value?.gate_a_capacity || 0,
        count: selectedGate.value.gate_a || 0
    },
    {
        id: 'gate_b_capacity',
        name: 'Gate B',
        capacity: ticketDetails.value?.gate_b_capacity || 0,
        count: selectedGate.value.gate_b || 0
    },
    {
        id: 'gate_c_capacity',
        name: 'Gate C',
        capacity: ticketDetails.value?.gate_c_capacity || 0,
        count: selectedGate.value.gate_c || 0
    },
    {
        id: 'gate_d_capacity',
        name: 'Gate D',
        capacity: ticketDetails.value?.gate_d_capacity || 0,
        count: selectedGate.value.gate_d || 0
    },
    {
        id: 'gate_e_capacity',
        name: 'Gate E',
        capacity: ticketDetails.value?.gate_e_capacity || 0,
        count: selectedGate.value.gate_e || 0
    }
].filter(gate => gate.capacity > 0));

const handleGateIncrement = (gateId: string) => {
    const gate = gateId.split('_')[1];
    const maxTotal = MAX_TOTAL_TICKETS - vip.value;
    const currentTotal = reguler.value;

    if (currentTotal < maxTotal && selectedGate.value[`gate_${gate}`] < ticketDetails.value[`${gateId}`]) {
        selectedGate.value[`gate_${gate}`]++;
    }
};

const handleGateDecrement = (gateId: string) => {
    const gate = gateId.split('_')[1];
    if (selectedGate.value[`gate_${gate}`] > 0) {
        selectedGate.value[`gate_${gate}`]--;
    }
};

watch([reguler, vip], () => {
    updateTicketForms();
});

onMounted(() => {
    getTicketDetails();
    checkAuthStatus();
});
</script>

<template>
    <nav>
        <KTHeader />
    </nav>
    <div class="container mb-20">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <img :src="ticketDetails?.konser?.image" class="img-fluid rounded shadow-sm concert-image"
                                alt="Concert Image">
                            <div class="description-container mb-10 mt-10">
                                <div class="description-title">Deskripsi :</div>
                                <div class="description-wrapper">
                                    <span class="description-text">{{ ticketDetails?.konser?.deskripsi }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card-body">
                                <h2 class="concert-title">
                                    {{ ticketDetails?.konser?.title }}
                                </h2>

                                <div class="info-item">
                                    <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                                    <span>{{ ticketDetails?.konser?.lokasi }}</span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-calendar-event-fill text-success me-2"></i>
                                    <span>{{ ticketDetails?.konser?.tanggal }}</span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-clock-fill text-primary me-2"></i>
                                    <span>{{ ticketDetails?.opengate }} - {{ ticketDetails?.closegate }}</span>
                                </div>

                                <div class="ticket-type-selection mb-4">
                                    <div class="me-20">
                                    <div class="me-20">
                                    <div class="me-20">
                                    
                                    <div class="ticket-options me-20">
                                        <template v-if="gates.length > 0">
                                            <div v-for="gate in gates" :key="gate.id" class="ticket-option">
                                                <div class="ticket-type-header">
                                                    <i class="bi bi-door-open text-primary"></i>
                                                    <span class="ticket-name"> Regular - {{ gate.name }}</span>
                                                </div>
                                                <div
                                                    class="ticket-features d-flex justify-content-between align-items-center">
                                                    <p class="mb-0 ms-5">{{ currency(ticketPrices.regular) }}</p>
                                                    <p class="mb-0">Tersedia: {{ gate.capacity }}</p>
                                                </div>

                                                <div class="ticket-quantity mt-3">
                                                    
                                                    <label class="form-label ms-5">Jumlah Tiket:</label>
                                                    <div class="input-group">
                                                        <button class="btn btn-outline-secondary"
                                                            @click="handleGateDecrement(gate.id)"
                                                            :disabled="selectedGate[`gate_${gate.id.split('_')[1]}`] <= 0">
                                                            -
                                                        </button>
                                                        <input type="number" class="form-control text-center"
                                                            v-model="selectedGate[`gate_${gate.id.split('_')[1]}`]"
                                                            :max="gate.capacity" min="0"
                                                            :disabled="totalTickets >= MAX_TOTAL_TICKETS">
                                                        <button class="btn btn-outline-secondary"
                                                            @click="handleGateIncrement(gate.id)" :disabled="selectedGate[`gate_${gate.id.split('_')[1]}`] >= gate.capacity ||
                                                                totalTickets >= MAX_TOTAL_TICKETS">
                                                            +
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- VIP Section -->
                                        <div class="ticket-option">
                                            <div class="ticket-type-header">
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <span class="ticket-name text-white"> VIP</span>
                                            </div>
                                            <div class="ticket-features  d-flex justify-content-between align-items-center">
                                                <p class="ms-5">✓ Premium seating</p>
                                                <p>Tersedia: {{ availableTickets.vip }}</p>
                                            </div>
                                            <p class="ms-5">✓ Exclusive merchandise</p>
                                            <div class="ticket-price ms-5">{{ currency(ticketPrices.vip) }}</div>
                                            <div class="ticket-quantity mt-3">
                                                <label class="form-label ms-5">Jumlah Tiket:</label>
                                                <div class="input-group">
                                                    <button class="btn btn-outline-secondary" @click="decrementVip"
                                                        :disabled="vip <= 0">-</button>
                                                    <input type="number" class="form-control text-center" :value="vip"
                                                        @input="handleVipInput" min="0"
                                                        :max="Math.min(availableTickets.vip, MAX_TOTAL_TICKETS - reguler)"
                                                        :disabled="totalTickets >= MAX_TOTAL_TICKETS && vip === 0">
                                                    <button class="btn btn-outline-secondary" @click="incrementVip"
                                                        :disabled="vip >= availableTickets.vip || totalTickets >= MAX_TOTAL_TICKETS">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                </div>
                                </div>

                                <div class="total-price">
                                    <h4>Total: {{ currency(totalPrice) }}</h4>
                                </div>

                                <button class="booking-button" @click="handleBooking">
                                    Pesan Tiket
                                    <i class="bi bi-cart2 text-white ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showLoginModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Authentication Required</h5>
                        <button type="button" class="btn-close" @click="showLoginModal = false"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-center mb-4">Please sign in or create an account to continue booking tickets.</p>

                        <div class="flex-row mt-15 d-flex justify-content-center gap-2">
                            <button class="btn btn-primary col-4" @click="redirectToSignIn">
                                Sign In
                                <i class="bi bi-box-arrow-in-right ms-2"></i>
                            </button>

                            <button class="btn btn-outline-warning" @click="redirectToSignUp">
                                Create Account
                                <i class="bi bi-person-plus ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Form Modal -->
        <div class="modal fade" :class="{ 'show d-block': showBookingForm && authStore.isAuthenticated }" tabindex="-1"
            :style="{ background: showBookingForm && authStore.isAuthenticated ? 'rgba(0,0,0,0.5)' : '' }">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Informasi Tiket</h5>
                        <button type="button" class="btn-close" @click="showBookingForm = false"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Regular Tickets Section -->
                        <template v-if="reguler > 0">
                            <!-- Group tickets by gate -->
                            <template v-for="(gate, gateName) in groupedRegularTickets" :key="gateName">
                                <div v-if="gate.tickets.length > 0" class="mb-4">
                                    <h5 class="ticket-section-title">{{ gate.name }} Tickets</h5>
                                    <div v-for="(form, index) in gate.tickets" 
                                        :key="'regular-' + gateName + index" 
                                        class="ticket-form p-3 mb-3 border rounded">
                                        <h6>Ticket {{ index + 1 }} - {{ gate.name }}</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Nama Lengkap</label>
                                                <input type="text" class="form-control" v-model="form.nama_pemesan" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" v-model="form.email_pemesan" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Nomor Telepon</label>
                                                <input type="tel" class="form-control" v-model="form.telepon_pemesan" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Alamat</label>
                                                <input type="text" class="form-control" v-model="form.alamat_pemesan" required>
                                            </div>
                                        </div>
                                        <hr v-if="index < gate.tickets.length - 1">
                                    </div>
                                </div>
                            </template>
                        </template>

                        <!-- VIP Tickets Section -->
                        <div v-if="vip > 0">
                            <h5 class="ticket-section-title mt-4">VIP Tickets</h5>
                            <div v-for="(form, index) in ticketForms.vip" :key="'vip-' + index" class="ticket-form">
                                <h6>Ticket VIP {{ index + 1 }}</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" v-model="form.nama_pemesan" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" v-model="form.email_pemesan" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor Telepon</label>
                                        <input type="tel" class="form-control" v-model="form.telepon_pemesan" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Alamat</label>
                                        <input type="text" class="form-control" v-model="form.alamat_pemesan" required>
                                    </div>
                                </div>
                                <hr v-if="index < ticketForms.vip.length - 1">
                            </div>
                        </div>

                        <div class="booking-confirmation">
                            <button class="btn btn-primary btn-lg w-100" @click="submitBooking"
                                :disabled="isSubmitting">
                                <span v-if="!isSubmitting">Konfirmasi Pemesanan</span>
                                <span v-else>
                                    <i class="bi bi-hourglass-split me-2"></i>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Footer />
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

* {
    font-family: 'Poppins', sans-serif;
}

.card-body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.modal {
    background-color: rgba(0, 0, 0, 0.5);
}

.modal.show {
    display: block;
}

.card {
    border: none;
    transition: all 0.3s ease;
    border-radius: 15px;
    margin-top: 10rem;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.concert-image {
    height: 300px;
    object-fit: cover;
    width: 100%;
    border-radius: 15px;
}

.description-container {
    padding: 0 1.5rem;
    flex: 1;
}

.description-wrapper {
    width: 100%;
    padding: 0.5rem;
}

.description-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.description-text {
    font-size: 0.95rem;
    line-height: 1.6;
    color: #666;
    text-align: justify;
    max-width: 100%; /* Memastikan tidak melebihi lebar parent */
    width: 100%; /* Menggunakan lebar penuh yang tersedia */
    display: block; /* Memastikan elemen mengambil ruang penuh */
}

.concert-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--bs-primary);
    margin-bottom: 1.5rem;
}

.info-item {
    margin-bottom: 1rem;
    font-size: 1rem;
    display: flex;
    align-items: center;
}

.info-item i {
    font-size: 1.2rem;
    margin-right: 0.5rem;
}

.info-item span {
    color: #666;
}

.ticket-label {
    font-size: 1.1rem;
    font-weight: 500;
}

.ticket-input {
    width: 150px;
}

.total-price {
    margin: 1.5rem 0;
}

.total-price h4 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--bs-primary);
}

.booking-button {
    background-color: var(--bs-primary);
    color: white;
    padding: 0.70rem 1.2rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.booking-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
}

.login-message {
    text-align: center;
    margin-bottom: 2rem;
}

.login-message i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.login-message p {
    font-size: 1.1rem;
    color: #666;
}

.login-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
}

.ticket-form {
    margin-bottom: 2rem;
}

.ticket-form h6 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.form-label {
    font-size: 0.9rem;
    font-weight: 500;
    color: #555;
}

.form-control {
    font-size: 0.95rem;
    padding: 0.6rem 1rem;
    border-radius: 8px;
}

.form-control:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

.booking-confirmation {
    text-align: right;
    margin-top: 2rem;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    opacity: 1;
}

.modal-content {
    border-radius: 15px;
}

.modal-header {
    border-bottom: 1px solid #eee;
}

.modal-header .modal-title {
    font-size: 1.25rem;
    font-weight: 600;
}

hr {
    margin: 2rem 0;
    opacity: 0.15;
}

.ticket-option {
    border: 1px solid #cbcaca;
    border-radius: 12px;
    width: 180%;
    margin-bottom: 5%;
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
}

.ticket-section-header {
    color: #333;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #eee;
}

/* Remove the media query since we always want vertical layout */
@media (max-width: 768px) {
    .ticket-options {
        gap: 1rem;
    }
}

.ticket-option:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Optional: Add some spacing between sections */
.ticket-type-selection {
    margin: 0 auto;
}

/* Add a subtle animation for selection */
.ticket-option {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .ticket-options {
        flex-direction: column;
    }

    .ticket-option {
        width: 100%;
    }
}

.section-title {
    width: 100%;
    border-bottom: 2px solid #eee;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}

.ticket-option {
    border: 1px solid #ddd;
    padding: 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.ticket-form {  
    transition: all 0.3s ease;
}

.ticket-form:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.ticket-section-title {
    border-bottom: 2px solid var(--bs-primary);
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
}
</style>