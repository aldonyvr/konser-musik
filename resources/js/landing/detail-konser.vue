<script setup lang="ts">
import KTHeader from "@/layouts/default-layout/components/header/NavbarLanding.vue";
import { useRoute, useRouter } from "vue-router";
import Footer from '@/Layouts/default-layout/components/footer/Footer.vue';
import { ref, onMounted, computed, watch } from "vue";
import axios from "@/libs/axios";
import { currency } from "@/libs/utils";

const route = useRoute();
const router = useRouter();
const konserId = route.params.uuid;
const DetailKonser = ref<any>([]);
const ticketCount = ref(1);
const isLoggedIn = ref(false);
const showLoginModal = ref(false);
const showBookingForm = ref(false);

// Add user state
const currentUser = ref(null);

const ticketForms = ref([
    {
        name: '',
        email: '',
        phone: '',
        idNumber: ''
    }
]);

const totalPrice = computed(() => {
    return DetailKonser.value.harga * ticketCount.value;
});

const updateTicketForms = () => {
    const currentLength = ticketForms.value.length;
    const targetLength = ticketCount.value;

    if (currentLength < targetLength) {
        for (let i = currentLength; i < targetLength; i++) {
            ticketForms.value.push({
                name: '',
                email: '',
                phone: '',
                idNumber: ''
            });
        }
    } else if (currentLength > targetLength) {
        ticketForms.value = ticketForms.value.slice(0, targetLength);
    }
};

// Check authentication status
const checkAuthStatus = async () => {
    try {
        const response = await axios.get('/auth/me');
        currentUser.value = response.data;
        isLoggedIn.value = true;
    } catch (error) {
        isLoggedIn.value = false;
        currentUser.value = null;
    }
};

const handleBooking = async () => {
    if (!isLoggedIn.value) {
        showLoginModal.value = true;
        return;
    }

    // Show booking form immediately if logged in
    showBookingForm.value = true;
};

const submitBooking = async () => {
    const isValid = ticketForms.value.every(form =>
        form.name && form.email && form.phone && form.idNumber
    );

    if (!isValid) {
        alert('Please fill in all fields for each ticket');
        return;
    }

    try {
        const bookingData = {
            konserId: konserId,
            ticketCount: ticketCount.value,
            totalPrice: totalPrice.value,
            tickets: ticketForms.value
        };

        const response = await axios.post('/bookings/create', bookingData);
        alert('Booking successful!');
        router.push('/bookings');
    } catch (error) {
        console.error('Booking failed:', error);
        alert('Booking failed. Please try again.');
    }
};

const getDetailKonser = async () => {
    try {
        const response = await axios.get(`/konser/edit/${konserId}`);
        DetailKonser.value = response.data.data;
    } catch (error) {
        console.error("Error fetching concerts:", error);
    }
};

onMounted(() => {
    getDetailKonser();
    checkAuthStatus(); // Check authentication status when component mounts
});

watch(ticketCount, () => {
    updateTicketForms();
});
</script>

<template>
    <nav>
        <KTHeader />
    </nav>
    <div class="mt-6"></div>
    <div class="mt-20"></div>
    <div class="container mt-20 mb-20">
        <div class="row justify-content-center">
            <!-- Concert details card - same as before -->
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <img :src="DetailKonser.image" 
                                class="img-fluid rounded shadow-sm concert-image"
                                alt="Concert Image">
                            <div class="description-container mb-10">
                                <div class="description-title">Deskripsi :</div>
                                <span class="description-text">{{ DetailKonser.deskripsi }}</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card-body">
                                <h2 class="concert-title">
                                    {{ DetailKonser.title }}    
                                </h2>

                                <div class="info-item">
                                    <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                                    <span>{{ DetailKonser.lokasi }}</span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-calendar-event-fill text-success me-2"></i>
                                    <span>{{ DetailKonser.tanggal }}</span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-currency-dollar text-warning me-2"></i>
                                    <span>{{ currency(DetailKonser.harga) }}</span>
                                </div>

                                <div class="ticket-selection d-flex flex-row ">
                                    <label class="ticket-label mt-3">Jumlah pemesanan tiket:</label>
                                    <div class="input-group ticket-input ms-3">
                                        <button class="btn btn-outline-secondary"
                                            @click="ticketCount > 1 && ticketCount--">-</button>
                                        <input type="number" class="form-control text-center" v-model="ticketCount"
                                            min="1" max="5">
                                        <button class="btn btn-outline-secondary"
                                            @click="ticketCount < 5 && ticketCount++">+</button>
                                    </div>
                                </div>

                                <div class="total-price">
                                    <h4>Total: {{ currency(totalPrice) }}</h4>
                                </div>

                                <button class="booking-button" @click="handleBooking">
                                    Pesan Tiket <i class="fa-solid fa-cart-shopping ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Modal - only shown when not logged in -->
        <div class="modal fade" :class="{ 'show d-block': showLoginModal && !isLoggedIn }" tabindex="-1"
            :style="{ background: showLoginModal && !isLoggedIn ? 'rgba(0,0,0,0.5)' : '' }">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Login Required</h5>
                        <button type="button" class="btn-close" @click="showLoginModal = false"></button>
                    </div>
                    <div class="modal-body">
                        <div class="login-message">
                            <i class="bi bi-exclamation-circle text-warning"></i>
                            <p>Please login to your account or create a new one to book tickets.</p>
                        </div>
                        <div class="login-buttons">
                            <button class="btn btn-primary" @click="router.push('/sign-in')">
                                Sign In
                            </button>
                            <button class="btn btn-outline-primary" @click="router.push('/sign-up')">
                                Create Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Forms Modal - shown when logged in -->
        <div class="modal fade" :class="{ 'show d-block': showBookingForm && isLoggedIn }" tabindex="-1"
            :style="{ background: showBookingForm && isLoggedIn ? 'rgba(0,0,0,0.5)' : '' }">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ticket Information</h5>
                        <button type="button" class="btn-close" @click="showBookingForm = false"></button>
                    </div>
                    <div class="modal-body">
                        <div v-for="(form, index) in ticketForms" :key="index" class="ticket-form">
                            <h6>Ticket #{{ index + 1 }}</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" v-model="form.name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" v-model="form.email" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" v-model="form.phone" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ID Number</label>
                                    <input type="text" class="form-control" v-model="form.idNumber" required>
                                </div>
                            </div>
                            <hr v-if="index < ticketForms.length - 1">
                        </div>
                        <div class="booking-confirmation">
                            <button class="btn btn-primary btn-lg" @click="submitBooking">
                                Confirm Booking
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
    margin-top: 2rem;
    padding: 0 1.5rem;
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
</style>