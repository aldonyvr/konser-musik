<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from '@/libs/axios';
import { toast } from 'vue3-toastify';

const mitras = ref([]);
const isLoading = ref(true);
const showForm = ref(false);

const formData = ref({
    name: '',
    email: '',
    password: '',
    phone: '',
    company_name: ''
});

const fetchMitras = async () => {
    try {
        const response = await axios.get('/api/mitra');
        mitras.value = response.data;
    } catch (error) {
        toast.error('Gagal memuat data mitra');
    } finally {
        isLoading.value = false;
    }
};

const createMitra = async () => {
    try {
        await axios.post('/api/mitra/store', formData.value);
        toast.success('Mitra berhasil ditambahkan');
        showForm.value = false;
        fetchMitras();
        formData.value = {
            name: '',
            email: '',
            password: '',
            phone: '',
            company_name: ''
        };
    } catch (error) {
        toast.error('Gagal menambahkan mitra');
    }
};

const deleteMitra = async (id) => {
    if (!confirm('Apakah Anda yakin ingin menghapus mitra ini?')) return;
    
    try {
        await axios.delete(`/api/mitra/${id}`);
        toast.success('Mitra berhasil dihapus');
        fetchMitras();
    } catch (error) {
        toast.error('Gagal menghapus mitra');
    }
};

onMounted(fetchMitras);
</script>

<template>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Manajemen Mitra</h3>
            <button class="btn btn-primary" @click="showForm = true">
                Tambah Mitra
            </button>
        </div>
        
        <div class="card-body">
            <!-- Form Modal -->
            <div v-if="showForm" class="modal d-block" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Mitra Baru</h5>
                            <button type="button" class="btn-close" @click="showForm = false"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="createMitra">
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input v-model="formData.name" type="text" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input v-model="formData.email" type="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input v-model="formData.password" type="password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">No. Telepon</label>
                                    <input v-model="formData.phone" type="text" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama Perusahaan</label>
                                    <input v-model="formData.company_name" type="text" class="form-control" required>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mitra List -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Perusahaan</th>
                            <th>Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="isLoading">
                            <td colspan="5" class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="!mitras.length">
                            <td colspan="5" class="text-center">Tidak ada data mitra</td>
                        </tr>
                        <tr v-else v-for="mitra in mitras" :key="mitra.id">
                            <td>{{ mitra.name }}</td>
                            <td>{{ mitra.email }}</td>
                            <td>{{ mitra.company_name }}</td>
                            <td>{{ mitra.phone }}</td>
                            <td>
                                <button class="btn btn-sm btn-danger" @click="deleteMitra(mitra.id)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
