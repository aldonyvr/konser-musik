<template>

  <nav>
    <KTHeader />
  </nav>
  <div class="container-fluid py-5 px-20 mt-20 ">
    <!-- Profile Header Section -->
    <div class="row mb-5 mt-20 ">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex flex-column flex-md-row align-items-center">
              <!-- Profile Image -->
              <div class="symbol symbol-100px me-4 mb-3 mb-md-0">
                <img v-if="authStore.user?.photo" :src="`/storage/${authStore.user.photo}`" alt="Profile"
                  class="rounded-circle">
                <div v-else class="symbol-label bg-light-primary rounded-circle">
                  <KTIcon icon-name="user" icon-class="fs-1" />
                </div>
              </div>

              <!-- Profile Info -->
              <div class="flex-grow-1">
                <h3 class="mb-1">{{ authStore.user?.name }}</h3>
                <p class="text-muted mb-2">{{ authStore.user?.email }}</p>
                <p class="mb-0">{{ authStore.user?.role === 'admin' ? 'Administrator' : 'Member' }}</p>
              </div>

              <!-- Edit Profile Button -->
              <button class="btn btn-primary" @click="isEditing = true">
                Edit Profile
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Profile Content Section -->
    <div class="row mb-20">
      <!-- Left Column - Personal Information -->
      <div class="col-12 col-lg-8 mb-4 mb-lg-0">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Personal Information</h4>
          </div>
          <div class="card-body">
            <form v-if="isEditing" @submit.prevent="handleUpdateProfile">
              <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" v-model="formData.name">
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" v-model="formData.email" disabled>
              </div>
              <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="tel" class="form-control" v-model="formData.phone">
              </div>
              <div class="mb-3">
                <label class="form-label">Profile Photo</label>
                <input type="file" class="form-control" @change="handlePhotoUpload" accept="image/*">
              </div>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <button type="button" class="btn btn-secondary" @click="isEditing = false">
                  Cancel
                </button>
              </div>
            </form>

            <div v-else>
              <div class="row mb-3">
                <div class="col-4 text-muted">Full Name:</div>
                <div class="col-8">{{ authStore.user?.name }}</div>
              </div>
              <div class="row mb-3">
                <div class="col-4 text-muted">Email:</div>
                <div class="col-8">{{ authStore.user?.email }}</div>
              </div>
              <div class="row mb-3">
                <div class="col-4 text-muted">Phone:</div>
                <div class="col-8">{{ authStore.user?.phone || '-' }}</div>
              </div>
              <div class="row mb-3">
                <div class="col-4 text-muted">Member Since:</div>
                <div class="col-8">{{ formatDate(authStore.user?.created_at) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column - Account Settings -->
      <div class="col-12 col-lg-4">

        <div class="col-12 ">
          <div class="card mb-4">
            <div class="card-header">
              <h4 class="card-title">Account Settings</h4>
            </div>
            <div class="card-body">
              <div v-if="!isChangingPassword" class="d-grid gap-2 mb-7">
                <button class="btn btn-outline-primary" @click="handleShowChangePassword">
                  <i class="bi bi-lock"></i>
                  Change Password
                </button>
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                  <i class="bi bi-trash"></i>
                  Delete Account
                </button>
              </div>

              <!-- Form Change Password -->
              <form v-else @submit.prevent="handleChangePassword">
                <div class="mb-3">
                  <label class="form-label">New Password</label>
                  <input type="password" class="form-control" v-model="passwordForm.password" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Confirm Password</label>
                  <input type="password" class="form-control" v-model="passwordForm.password_confirmation" required>
                </div>
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary">Save Password</button>
                  <button type="button" class="btn btn-secondary" @click="handleCancelChangePassword">
                    Cancel
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Activity Summary Card -->
        <!-- <div class="card">
            <div class="card-header">
              <h4 class="card-title">Activity Summary</h4>
            </div>
            <div class="card-body">
              <div class="d-flex flex-column gap-3">
                <div class="d-flex justify-content-between">
                  <span>Concert Tickets</span>
                  <span class="badge bg-primary">{{ activitySummary.tickets || 0 }}</span>
                </div>
                <div class="d-flex justify-content-between">
                  <span>Reviews</span>
                  <span class="badge bg-info">{{ activitySummary.reviews || 0 }}</span>
                </div>
                <div class="d-flex justify-content-between">
                  <span>Last Login</span>
                  <span class="text-muted">{{ formatDate(authStore.user?.last_login) }}</span>
                </div>
              </div>
            </div>
          </div> -->
      </div>
    </div>
  </div>
  <Footer />

  <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-danger">Warning: This action cannot be undone. All your data will be permanently deleted.</p>
          <p>Please type "DELETE" to confirm:</p>
          <input type="text" class="form-control" v-model="deleteConfirmation" placeholder="Type DELETE to confirm">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancel
          </button>
          <button type="button" class="btn btn-danger" @click="handleDeleteAccount"
            :disabled="deleteConfirmation !== 'DELETE'">
            Delete Account
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Change Password Modal -->

</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { block, unblock } from "@/libs/utils";
import { useAuthStore } from '@/stores/auth';
import axios from '@/libs/axios';
import KTHeader from '@/layouts/default-layout/components/header/NavbarLanding.vue';
import Footer from '@/Layouts/default-layout/components/footer/Footer.vue';
import { Modal } from 'bootstrap';

const authStore = useAuthStore();
const isEditing = ref(false);
const showChangePasswordModal = ref(false);
const showDeleteAccountModal = ref(false);
const deleteConfirmation = ref('');
const formRef = ref();
const photo = ref<any>([]);
const isChangingPassword = ref(false);

const formData = ref({
  name: authStore.user?.name || '',
  email: authStore.user?.email || '',
  phone: authStore.user?.phone || '',
  photo: null as File | null,
  role_id: authStore.user?.role_id || '',
  password: '',
  passwordConfirmation: ''
});

const passwordForm = ref({
  password: '',
  password_confirmation: ''
});


const handleShowChangePassword = () => {
  isChangingPassword.value = true;
};

const handleCancelChangePassword = () => {
  isChangingPassword.value = false;
  passwordForm.value = {
    password: '',
    password_confirmation: ''
  };
};

const handleChangePassword = async () => {
  try {
    if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
      showAlert('Passwords do not match', "error");
      return;
    }

    await axios.put('master/change-password', {
      password: passwordForm.value.password,
      password_confirmation: passwordForm.value.password_confirmation
    });

    isChangingPassword.value = false;
    passwordForm.value = {
      password: '',
      password_confirmation: ''
    };
    showAlert('Password successfully changed', "success");
  } catch (error: any) {
    showAlert(error.response?.data?.message || 'Error changing password', "error");
  }
};

const activitySummary = ref({
  tickets: 0,
  reviews: 0
});

const showAlert = (message: string, type: 'success' | 'error') => {
  const alertEl = document.createElement('div');
  alertEl.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed top-0 end-0 m-4`;
  alertEl.style.zIndex = '9999';
  alertEl.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  `;
  document.body.appendChild(alertEl);

  setTimeout(() => {
    alertEl.remove();
  }, 3000);
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const handlePhotoUpload = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    photo.value = [{ file: target.files[0] }];
  }
};

const handleUpdateProfile = async () => {
  try {
    const submitFormData = new FormData();
    submitFormData.append("name", formData.value.name);
    submitFormData.append("email", formData.value.email);
    submitFormData.append("phone", formData.value.phone);
    submitFormData.append("role_id", formData.value.role_id);

    if (formData.value.password) {
      submitFormData.append("password", formData.value.password);
      submitFormData.append(
        "password_confirmation",
        formData.value.passwordConfirmation
      );
    }

    if (photo.value.length) {
      submitFormData.append("photo", photo.value[0].file);
    }

    const userId = authStore.user?.uuid;
    if (userId) {
      submitFormData.append("_method", "PUT");
    }

    block(document.getElementById("form-user"));

    await axios({
      method: "post",
      url: userId ? `/master/users/${userId}` : "/master/users/store",
      data: submitFormData,
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    isEditing.value = false;
    showAlert("Data berhasil disimpan", "success");
    if (formRef.value) {
      formRef.value.resetForm();
    }

  } catch (err: any) {
    if (formRef.value) {
      formRef.value.setErrors(err.response.data.errors);
    }
    showAlert(err.response.data.message, "error");
  } finally {
    unblock(document.getElementById("form-user"));
  }
};

const handleDeleteAccount = async () => {
  try {
    if (deleteConfirmation.value !== 'DELETE') {
      return;
    }

    const userId = authStore.user?.uuid;
    const response = await axios.get(`/master/destroy/${userId}`);

    if (response.data.success) {
      const modalElement = document.getElementById('deleteAccountModal');
      if (modalElement) {
        const modal = Modal.getInstance(modalElement);
        modal?.hide();
      }

      showAlert("Account successfully deleted", "success");
      await authStore.logout();
      window.location.href = '/';
    }
  } catch (error: any) {
    showAlert(error.response?.data?.message || 'Failed to delete account', "error");
  } finally {
    deleteConfirmation.value = '';
  }
};

onMounted(() => {
  const modalElement = document.getElementById('deleteAccountModal');
  if (modalElement) {
    new Modal(modalElement);
  }
});
</script>

<style scoped>
.symbol {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  overflow: hidden;
}

.symbol-100px {
  width: 100px;
  height: 100px;
}

.symbol img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.symbol-label {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>