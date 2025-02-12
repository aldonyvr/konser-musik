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
        const response = await axios.get('/api/admin/mitra');
        mitras.value = response.data;
    } catch (error) {
        toast.error('Gagal memuat data mitra');
    } finally {
        isLoading.value = false;
    }
};

const createMitra = async () => {
    try {
        await axios.post('/api/admin/mitra/create', formData.value);
        toast.success('Mitra berhasil ditambahkan');
        showForm.value = false;
        fetchMitras();
        resetForm();
    } catch (error) {
        toast.error('Gagal menambahkan mitra');
    }
};

const resetForm = () => {
    formData.value = {
        name: '',
        email: '',
        password: '',
        phone: '',
        company_name: ''
    };
};

const deleteMitra = async (id) => {
    if (!confirm('Apakah Anda yakin ingin menghapus mitra ini?')) return;
    
    try {
        await axios.delete(`/api/admin/mitra/${id}`);
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
                Tambah Mitra Baru
            </button>
        </div>

        <div class="card-body">
            <!-- Add Mitra Modal -->
            <div v-if="showForm" class="modal fade show d-block" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Mitra Baru</h5>
                            <button type="button" class="btn-close" @click="showForm = false"></button>
                        </div>
                        <form @submit.prevent="createMitra">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label required">Nama Mitra</label>
                                    <input v-model="formData.name" type="text" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required">Email</label>
                                    <input v-model="formData.email" type="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required">Password</label>
                                    <input v-model="formData.password" type="password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required">No. Telepon</label>
                                    <input v-model="formData.phone" type="text" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required">Nama Perusahaan</label>
                                    <input v-model="formData.company_name" type="text" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="showForm = false">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mitra List -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Perusahaan</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="isLoading">
                            <td colspan="6" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="!mitras.length">
                            <td colspan="6" class="text-center py-4">Belum ada data mitra</td>
                        </tr>
                        <tr v-else v-for="(mitra, index) in mitras" :key="mitra.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ mitra.name }}</td>
                            <td>{{ mitra.company_name }}</td>
                            <td>{{ mitra.email }}</td>
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

<style scoped>
.modal {
    background-color: rgba(0, 0, 0, 0.5);
}
.required:after {
    content: " *";
    color: red;
}
</style>
