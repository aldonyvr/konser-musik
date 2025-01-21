<template>
    <VForm class="form w-100" @submit="submit" :validation-schema="login">
        <!-- Email input group remains the same -->
        <div class="fv-row mb-10">
            <label class="form-label fs-6 fw-bold">Email</label>
            <Field tabindex="1" class="form-control form-control-lg form-control-solid" type="text" name="email"
                autocomplete="off" v-model="data.email" />
            <div class="fv-plugins-message-container"></div>
        </div>

        <!-- Password input group remains the same -->
        <div class="fv-row mb-5">
            <div class="d-flex flex-stack mb-2">
                <label class="form-label fw-bold fs-6 mb-0">Password</label>
                <router-link to="/reset-password" class="link-primary small">Lupa Password?</router-link>
            </div>
            <div class="position-relative mb-3">
                <Field tabindex="2" class="form-control form-control-lg form-control-solid" type="password" name="password"
                    v-model="data.password" autocomplete="off" />
                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2">
                    <i class="bi bi-eye-slash fs-2" @click="togglePassword"></i>
                </span>
            </div>
            <div class="fv-plugins-message-container"></div>
        </div>

        <!-- Submit button remains the same -->
        <div class="text-center">
            <button tabindex="3" type="submit" ref="submitButton" class="btn btn-lg btn-primary w-100 mb-5">
                <span class="indicator-label">Login</span>
                <span class="indicator-progress">
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>
    </VForm>
</template>


<script lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent, ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify"
import { blockBtn, unblockBtn } from "@/libs/utils"

export default defineComponent({
    setup() {
        const store = useAuthStore();
        const router = useRouter();
        const submitButton = ref(null);

        const login = Yup.object().shape({
            email: Yup.string().email('Email tidak valid').required("Harap masukkan Email").label("Email"),
            password: Yup.string().min(8, 'Password minimal terdiri dari 8 karakter').required('Harap masukkan password').label("Password"),
        });

        return {
            login,
            submitButton,
            getAssetPath,
            store,
            router
        };
    },
    data() {
        return {
            data: {
                email: '',
                password: '',
            },
        }
    },
    methods: {
        submit() {
            blockBtn(this.submitButton);

            axios.post("/auth/login", { ...this.data, type: "email" })
                .then(res => {
                    // Store user data and token
                    this.store.setAuth(res.data.user, res.data.token);
                    
                    // Check user role and redirect accordingly
                    if (res.data.user.role_id === 2) {
                        this.router.push("/");
                    } else {
                        this.router.push("/dashboard");
                    }
                    
                    // Show success message
                    toast.success("Login berhasil!");
                })
                .catch(error => {
                    toast.error(error.response?.data?.message || "Terjadi kesalahan saat login");
                })
                .finally(() => {
                    unblockBtn(this.submitButton);
                });
        },
        togglePassword(ev) {
            const passwordField = document.querySelector("[name=password]");
            const type = passwordField.type;

            if (type === 'password') {
                passwordField.type = 'text';
                ev.target.classList.add("bi-eye");
                ev.target.classList.remove("bi-eye-slash");
            } else {
                passwordField.type = 'password';
                ev.target.classList.remove("bi-eye");
                ev.target.classList.add("bi-eye-slash");
            }
        }
    }
})
</script>