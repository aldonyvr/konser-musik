<!-- ResetPassword.vue -->
<template>
    <div class=""></div>
    <div class="w-100 mb-20 mt-5">
        <div class="text-center">
            <router-link to="/">
                <img :src="setting?.logo" :alt="setting?.app" class="w-200px mb-4" />
            </router-link>
            <h2>Reset Password</h2>
        </div>

        <!-- Step 1: Email Input -->
        <div v-if="step === 1">
            <VForm class="form w-100" @submit="sendOTP" :validation-schema="emailSchema">
                <div class="fv-row mb-10">
                    <label class="form-label fs-6 fw-bold">Email</label>
                    <Field
                        class="form-control form-control-lg form-control-solid"
                        type="email"
                        name="email"
                        v-model="formData.email"
                    />
                </div>

                <div class="text-center mt-15">
                    <button type="submit" ref="submitButton" class="btn btn-lg btn-primary w-100 mb-5">
                        <span class="indicator-label">Kirim Kode OTP</span>
                        <span class="indicator-progress">
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </VForm>
        </div>

        <!-- Step 2: OTP Verification -->
        <div v-if="step === 2">
            <VForm class="form w-100" @submit="verifyOTP" :validation-schema="otpSchema">
                <div class="fv-row mb-10">
                    <label class="form-label fs-6 fw-bold">Kode OTP</label>
                    <Field
                        class="form-control form-control-lg form-control-solid"
                        type="text"
                        name="otp"
                        maxlength="6"
                        v-model="formData.otp"
                    />
                    <div class="text-center mt-2">
                        <span class="text-muted">Tidak menerima kode? </span>
                        <a href="#" @click.prevent="resendOTP" class="link-primary">Kirim ulang</a>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" ref="verifyButton" class="btn btn-lg btn-primary w-100 mb-5">
                        <span class="indicator-label">Verifikasi OTP</span>
                        <span class="indicator-progress">
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </VForm>
        </div>

        <!-- Step 3: New Password -->
        <div v-if="step === 3">
            <VForm class="form w-100" @submit="resetPassword" :validation-schema="passwordSchema">
                <div class="fv-row mb-10">
                    <label class="form-label fs-6 fw-bold">Password Baru</label>
                    <div class="position-relative">
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            v-model="formData.password"
                        />
                        <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" @click="togglePassword">
                            <i :class="showPassword ? 'bi-eye' : 'bi-eye-slash'" class="bi fs-2"></i>
                        </span>
                    </div>
                </div>

                <div class="fv-row mb-10">
                    <label class="form-label fs-6 fw-bold">Konfirmasi Password</label>
                    <Field
                        class="form-control form-control-lg form-control-solid"
                        :type="showPassword ? 'text' : 'password'"
                        name="password_confirmation"
                        v-model="formData.password_confirmation"
                    />
                </div>

                <div class="text-center">
                    <button type="submit" ref="resetButton" class="btn btn-lg btn-primary w-100 mb-5">
                        <span class="indicator-label">Reset Password</span>
                        <span class="indicator-progress">
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </VForm>
        </div>


        <div class="text-center mt-3">
            <router-link to="/sign-in" class="link-primary fw-bold">
                Kembali ke Login
            </router-link>
        </div>
    </div>
    <div class="mt-20"></div>
</template>

<script>
import { defineComponent, ref } from "vue";
import { useRouter } from "vue-router";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { blockBtn, unblockBtn } from "@/libs/utils";
import { useSetting } from "@/services";

export default defineComponent({
    name: "reset-password",
    setup() {
        const router = useRouter();
        const { data: setting = {} } = useSetting();
        const submitButton = ref(null);
        const verifyButton = ref(null);
        const resetButton = ref(null);

        const emailSchema = Yup.object().shape({
            email: Yup.string()
                .email("Email tidak valid")
                .required("Email wajib diisi")
        });

        const otpSchema = Yup.object().shape({
            otp: Yup.string()
                .length(6, "Kode OTP harus 6 digit")
                .required("Kode OTP wajib diisi")
        });

        const passwordSchema = Yup.object().shape({
            password: Yup.string()
                .min(8, "Password minimal 8 karakter")
                .required("Password wajib diisi"),
            password_confirmation: Yup.string()
                .oneOf([Yup.ref('password'), null], "Password tidak cocok")
                .required("Konfirmasi password wajib diisi")
        });

        return {
            router,
            setting,
            submitButton,
            verifyButton,
            resetButton,
            emailSchema,
            otpSchema,
            passwordSchema
        };
    },
    data() {
        return {
            step: 1,
            showPassword: false,
            formData: {
                email: "",
                otp: "",
                password: "",
                password_confirmation: ""
            }
        };
    },
    methods: {
        async sendOTP() {
            blockBtn(this.submitButton);
            try {
                const response = await axios.post("/auth/send-otp", {
                    email: this.formData.email
                });
                toast.success(response.data.message);
                this.step = 2;
            } catch (error) {
                toast.error(error.response?.data?.message || "Gagal mengirim OTP");
            } finally {
                unblockBtn(this.submitButton);
            }
        },

        async verifyOTP() {
            blockBtn(this.verifyButton);
            try {
                const response = await axios.post("/auth/verify-otp", {
                    email: this.formData.email,
                    otp: this.formData.otp
                });
                toast.success(response.data.message);
                this.step = 3;
            } catch (error) {
                toast.error(error.response?.data?.message || "Kode OTP tidak valid");
            } finally {
                unblockBtn(this.verifyButton);
            }
        },

        async resetPassword() {
            blockBtn(this.resetButton);
            try {
                const response = await axios.post("/auth/reset-user-password", {
                    email: this.formData.email,
                    password: this.formData.password,
                    password_confirmation: this.formData.password_confirmation
                });
                toast.success(response.data.message);
                this.router.push("/sign-in");
            } catch (error) {
                toast.error(error.response?.data?.message || "Gagal reset password");
            } finally {
                unblockBtn(this.resetButton);
            }
        },

        resendOTP() {
            this.sendOTP();
        },

        togglePassword() {
            this.showPassword = !this.showPassword;
        }
    }
});
</script>