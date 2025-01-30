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
const konserId = route.params.uuid;
const ticketDetails = ref<any>(null);
const showLoginModal = ref(false);
const showBookingForm = ref(false);

const reguler = ref(0);
const vip = ref(0);
const MAX_TOTAL_TICKETS = 10;
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

const totalPrice = computed(() => {
    return (reguler.value * ticketPrices.value.regular) +
        (vip.value * ticketPrices.value.vip);
});

const updateTicketForms = () => {
    const regularForms = [];
    for (let i = 0; i < reguler.value; i++) {
        regularForms.push({
            nama_pemesan: '',
            email_pemesan: '',
            telepon_pemesan: '',
            alamat_pemesan: '',
            type: 'regular'
        });
    }
    ticketForms.value.regular = regularForms;

    const vipForms = [];
    for (let i = 0; i < vip.value; i++) {
        vipForms.push({
            nama_pemesan: '',
            email_pemesan: '',
            telepon_pemesan: '',
            alamat_pemesan: '',
            type: 'vip'
        });
    }
    ticketForms.value.vip = vipForms;
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
    if (!authStore.isAuthenticated) {
        showLoginModal.value = true;
        showBookingForm.value = false;
        return;
    }

    const allForms = [...ticketForms.value.regular, ...ticketForms.value.vip];
    const isValid = allForms.every(form =>
        form.nama_pemesan && form.email_pemesan && form.telepon_pemesan && form.alamat_pemesan
    );

    if (!isValid) {
        alert('Please fill in all fields for each ticket');
        return;
    }

    try {
        const bookingData = {
            konserId: konserId,
            reguler: reguler.value,
            vip: vip.value,
            totalPrice: totalPrice.value,
            tickets: allForms
        };

        const response = await axios.post('/datapemesan/store', bookingData);
        
        if (response.data.success && response.data.snap_token) {
            window.snap.pay(response.data.snap_token, {
                onSuccess: function(result) {
                    alert('Payment success!');
                    router.push('/invoice/' + response.data.order_id);
                },
                onPending: function(result) {
                    alert('Payment pending. Please complete your payment.');
                    router.push('/invoice/' + response.data.order_id);
                },
                onError: function(result) {
                    alert('Payment failed.');
                    console.error('Payment Error:', result);
                },
                onClose: function() {
                    alert('You closed the payment window without completing the payment.');
                }
            });
        } else {
            throw new Error('Failed to get payment token');
        }
    } catch (error) {
        if (error.response?.status === 401) {
            showLoginModal.value = true;
            showBookingForm.value = false;
        } else {
            console.error('Booking failed:', error);
            alert('Booking failed. Please try again.');
        }
    }
};

const getTicketDetails = async () => {
    try {
        console.log("konserId:", konserId); 
        const response = await axios.get(`/tiket/show/${konserId}`);
        if (response.data.success) {
            ticketDetails.value = response.data.data;
            console.log(`response ${response}`);
        } else {
            console.log(`response ${response}`);
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

const handleRegularInput = (event: Event) => {
    const value = parseInt((event.target as HTMLInputElement).value) || 0;
    const maxAllowed = Math.min(
        availableTickets.value.regular,
        MAX_TOTAL_TICKETS - vip.value
    );
    reguler.value = Math.min(Math.max(0, value), maxAllowed);
};

const handleVipInput = (event: Event) => {
    const value = parseInt((event.target as HTMLInputElement).value) || 0;
    const maxAllowed = Math.min(
        availableTickets.value.vip,
        MAX_TOTAL_TICKETS - reguler.value
    );
    vip.value = Math.min(Math.max(0, value), maxAllowed);
};

const incrementRegular = () => {
    if (reguler.value < availableTickets.value.regular && totalTickets.value < MAX_TOTAL_TICKETS) {
        reguler.value++;
    }
};

const decrementRegular = () => {
    if (reguler.value > 0) {
        reguler.value--;
    }
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
    <div class="mt-13"></div>
    <div class="mt-20"></div>
    <div class="container mt-20 mb-20">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <img :src="ticketDetails?.konser?.image" class="img-fluid rounded shadow-sm concert-image"
                                alt="Concert Image">
                            <div class="description-container mb-10 mt-10">
                                <div class="description-title">Deskripsi :</div>
                                <span class="description-text">{{ ticketDetails?.konser?.deskripsi }}</span>
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
                                    <label class="ticket-label mb-3">Pilih Tiket Anda:</label>
                                    <div class="ticket-options">
                                        <!-- Regular Ticket Option -->
                                        <div class="ticket-option">
                                            <div class="ticket-type-header">
                                                <i class="bi bi-ticket-perforated-fill text-primary"></i>
                                                <span class="ticket-name text-white">Regular</span>
                                            </div>
                                            <div class="ticket-features">
                                                <p>✓ Regular seating</p>
                                                <p>✓ Standard amenities</p>
                                                <p>Tersedia: {{ availableTickets.regular }}</p>
                                            </div>
                                            <div class="ticket-price">{{ currency(ticketPrices.regular) }}</div>
                                            <div class="ticket-quantity mt-3">
                                                <label class="form-label">Jumlah Tiket:</label>
                                                <div class="input-group">
                                                    <button class="btn btn-outline-secondary" @click="decrementRegular"
                                                        :disabled="reguler <= 0">-</button>
                                                    <input type="number" class="form-control text-center"
                                                        :value="reguler" @input="handleRegularInput" min="0"
                                                        :max="Math.min(availableTickets.regular, MAX_TOTAL_TICKETS - vip)"
                                                        :disabled="totalTickets >= MAX_TOTAL_TICKETS && reguler === 0">
                                                    <button class="btn btn-outline-secondary" @click="incrementRegular"
                                                        :disabled="reguler >= availableTickets.regular || totalTickets >= MAX_TOTAL_TICKETS">+</button>
                                                </div>

                                            </div>

                                        </div>

                                        <!-- VIP Ticket Option -->
                                        <div class="ticket-option">
                                            <div class="ticket-type-header">
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <span class="ticket-name text-white">VIP</span>
                                            </div>
                                            <div class="ticket-features">
                                                <p>✓ Premium seating</p>
                                                <p>✓ Exclusive merchandise</p>
                                                <p>✓ Meet & Greet access</p>
                                                <p>Tersedia: {{ availableTickets.vip }}</p>
                                            </div>
                                            <div class="ticket-price">{{ currency(ticketPrices.vip) }}</div>
                                            <div class="ticket-quantity mt-3">
                                                <label class="form-label">Jumlah Tiket:</label>
                                                <div class="input-group">
                                                    <button class="btn btn-outline-secondary" @click="decrementVip"
                                                        :disabled="vip <= 0">-</button>
                                                    <input type="number" class="form-control text-center"
                                                        :value="vip" @input="handleVipInput" min="0"
                                                        :max="Math.min(availableTickets.vip, MAX_TOTAL_TICKETS - reguler)"
                                                        :disabled="totalTickets >= MAX_TOTAL_TICKETS && vip === 0">
                                                    <button class="btn btn-outline-secondary" @click="incrementVip"
                                                        :disabled="vip >= availableTickets.vip || totalTickets >= MAX_TOTAL_TICKETS">+</button>
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
                        <button 
                            class="btn btn-primary col-4"
                            @click="redirectToSignIn"
                        >
                            Sign In
                            <i class="bi bi-box-arrow-in-right ms-2"></i>
                        </button>
                        
                        <button 
                            class="btn btn-outline-warning"
                            @click="redirectToSignUp"
                        >
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
                        <div v-if="reguler > 0">
                            <h5 class="ticket-section-title">Regular Tickets</h5>
                            <div v-for="(form, index) in ticketForms.regular" :key="'regular-' + index"
                                class="ticket-form">
                                <h6> {{ index + 1 }}</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" v-model="form.name_pemesan" required>
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
                                <hr v-if="index < ticketForms.regular.length - 1">
                            </div>
                        </div>

                        <!-- VIP Tickets Section -->
                        <div v-if="vip > 0">
                            <h5 class="ticket-section-title mt-4">VIP Tickets</h5>
                            <div v-for="(form, index) in ticketForms.vip" :key="'vip-' + index" class="ticket-form">
                                <h6> {{ index + 1 }}</h6>
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
                            <button class="btn btn-primary btn-lg w-100" @click="submitBooking">
                                Konfirmasi Pemesanan
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
    /* Membuat deskripsi mengambil ruang sisa */
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
    word-wrap: break-word;
    /* Agar teks panjang dapat terbungkus */
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

.ticket-options {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.ticket-option {
    flex: 1;
    min-width: 100px;
    border: 1px solid #cbcaca;
    border-radius: 12px;
    padding: 1.5rem;
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
}

.ticket-option:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.ticket-option.selected {
    border-color: var(--bs-primary);
    box-shadow: 0 8px 20px rgba(13, 110, 253, 0.15);
}

.ticket-radio {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 1.2rem;
    height: 1.2rem;
    cursor: pointer;
}

.ticket-type-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.ticket-type-header i {
    font-size: 1.5rem;
}

.ticket-name {
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
}

.ticket-features {
    margin: 1rem 0;
}

.ticket-features p {
    margin: 0.5rem 0;
    color: #666;
    font-size: 0.9rem;
}

.ticket-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--bs-primary);
    margin-top: 1rem;
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
</style>