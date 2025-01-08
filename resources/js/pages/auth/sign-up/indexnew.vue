<template>
    <div class="w-100">
        <div class="text-center mb-10">
            <router-link to="/">
                <img
                    :src="setting?.logo"
                    :alt="setting?.app"
                    class="w-200px mb-4"
                />
            </router-link>
            <h2 class="">
                Daftar Akun <span class="text-primary">{{ setting?.app }}</span>
            </h2>
        </div>

        <div
            class="stepper stepper-links d-flex flex-column"
            id="kt_create_account_stepper"
            ref="horizontalWizardRef"
        >
            <form
                class="w-100"
                novalidate
                id="kt_create_account_form"
                @submit="handleStep"
            >
                <div class="current" data-kt-stepper-element="content">
                    <Credential :formData="formData"></Credential>
                </div>

                <div data-kt-stepper-element="content">
                    <VerifyEmail
                        :formData="formData"
                        @on-complete="handleOtpEmail"
                        @send-otp="sendOtpEmail"
                    ></VerifyEmail>
                </div>
                <!--end::Step 2-->

                <!--begin::Step 3-->
                <div data-kt-stepper-element="content">
                    <VerifyPhone
                        :formData="formData"
                        @on-complete="handleOtpPhone"
                        @send-otp="sendOtpPhone"
                    ></VerifyPhone>
                </div>
                <!--end::Step 3-->

                <!--begin::Step 4-->
                <div data-kt-stepper-element="content">
                    <Password :formData="formData"></Password>
                </div>
                <!--end::Step 4-->

                <!--begin::Actions-->
                <div class="d-flex flex-column w-100">
                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                    >
                        <span v-if="currentStepIndex === totalSteps - 1">
                            Daftar
                        </span>
                        <span v-else>
                            Selanjutnya
                        </span>
                    </button>
                </div>
                <!--end::Actions-->
            </form>
            <!--end::Form-->
        </div>

        <div class="border-gray-300 w-100 mt-5 mb-10"></div>

        <!--begin::Link-->
        <div class="text-gray-500 fw-semobold fs-4 text-center">
            Sudah memiliki akun?

            <router-link to="/auth/sign-in" class="link-primary fw-bold">
                MASUK
            </router-link>
        </div>
        <!--end::Link-->
    </div>
</template>

<script lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent, ref, onMounted, computed } from "vue";
import * as Yup from "yup";
import create from "vue-zustand";

import { StepperComponent } from "@/assets/ts/components";
import { useForm } from "vee-validate";
import Credential from "./steps/Credential.vue";
import VerifyEmail from "./steps/VerifyEmail.vue";
import VerifyPhone from "./steps/VerifyPhone.vue";
import Password from "./steps/Password.vue";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { blockBtn, unblockBtn } from "@/libs/utils";
import router from "@/router";
import { useSetting } from "@/services";

interface ICredential {
    nama?: string;
    email?: string;
    phone?: string;
}

interface IVerifyEmail {
    otp_email?: string;
}

interface IVerifyPhone {
    otp_phone?: string;
}

interface IPassword {
    password?: string;
    password_confirmation?: string;
}

interface CreateAccount
    extends ICredential,
        IVerifyEmail,
        IVerifyPhone,
        IPassword {}

interface IOtpInterval {
    otpInterval: number;
    setOtpInterval: (otpInterval: number) => void;
}

export const useOtpIntervalStore = create<IOtpInterval>((set) => ({
    otpInterval: 0,
    setOtpInterval: (otpInterval: number) => set({ otpInterval }),
}));

export default defineComponent({
    name: "sign-up",
    components: {
        Credential,
        VerifyEmail,
        VerifyPhone,
        Password,
    },
    setup() {
        const { data: setting = {} } = useSetting();

        const _stepperObj = ref<StepperComponent | null>(null);
        const horizontalWizardRef = ref<HTMLElement | null>(null);
        const currentStepIndex = ref(0);

        const formData = ref<CreateAccount>({
            nama: "",
            email: "",
            phone: "",
            otp_email: "",
            otp_phone: "",
            password: "",
            password_confirmation: "",
        });

        onMounted(() => {
            _stepperObj.value = StepperComponent.createInsance(
                horizontalWizardRef.value as HTMLElement
            );
        });

        const createAccountSchema = [
            Yup.object({
                nama: Yup.string()
                    .required("Nama tidak boleh kosong")
                    .label("Nama"),
                email: Yup.string()
                    .email()
                    .required("Email tidak boleh kosong")
                    .label("Email"),
                phone: Yup.string()
                    .matches(/^08[0-9]\d{8,11}$/, "No. Telepon tidak valid")
                    .required("No. Telepon tidak boleh kosong")
                    .label("No. Telepon"),
            }),
            Yup.object({}),
            Yup.object({}),
            Yup.object({
                password: Yup.string()
                    .min(8, "Password minimal terdiri dari 8 karakter")
                    .required("Password tidak boleh kosong")
                    .label("Password"),
                password_confirmation: Yup.string()
                    .oneOf(
                        [Yup.ref("password")],
                        "Konfirmasi Password tidak sesuai"
                    )
                    .required("Konfirmasi Password tidak boleh kosong")
                    .label("Konfirmasi Password"),
            }),
        ];

        const currentSchema = computed(() => {
            return createAccountSchema[currentStepIndex.value];
        });

        const { resetForm, handleSubmit } = useForm<
            ICredential | IVerifyEmail | IVerifyPhone | IPassword
        >({
            validationSchema: currentSchema,
        });

        const totalSteps = computed(() => {
            if (_stepperObj.value) {
                return _stepperObj.value.totalStepsNumber;
            } else {
                return 1;
            }
        });

        const { otpInterval, setOtpInterval } = useOtpIntervalStore();

        const sendOtpEmail = (callback: any) => {
            blockBtn("#next-form");

            axios
                .post("/auth/register/get/email/otp", {
                    email: formData.value.email,
                    nama: formData.value.nama,
                })
                .then((res) => {
                    toast.success("Kode OTP berhasil dikirim ke Email Anda");
                    unblockBtn("#next-form");
                    callback && callback();

                    setOtpInterval.value(30);
                    handleOtpInterval();
                })
                .catch((err) => {
                    toast.error(err.response.data.message);
                    unblockBtn("#next-form");
                });
        };

        const checkOtpEmail = (callback: any) => {
            blockBtn("#next-form");

            axios
                .post("/auth/register/check/email/otp", {
                    email: formData.value.email,
                    otp: formData.value.otp_email,
                })
                .then((res) => {
                    toast.success("Email berhasil diverifikasi");
                    unblockBtn("#next-form");
                    callback && callback();
                })
                .catch((err) => {
                    toast.error(err.response.data.message);
                    unblockBtn("#next-form");
                });
        };

        const sendOtpPhone = (callback: any) => {
            blockBtn("#next-form");

            axios
                .post("/auth/register/get/phone/otp", {
                    phone: formData.value.phone,
                })
                .then((res) => {
                    toast.success(
                        "Kode OTP berhasil dikirim ke No. Telepon Anda"
                    );
                    unblockBtn("#next-form");
                    callback && callback();

                    setOtpInterval.value(30);
                    handleOtpInterval();
                })
                .catch((err) => {
                    toast.error(err.response.data.message);
                    unblockBtn("#next-form");
                });
        };

        const checkOtpPhone = (callback: any) => {
            blockBtn("#next-form");

            axios
                .post("/auth/register/check/phone/otp", {
                    phone: formData.value.phone,
                    otp: formData.value.otp_phone,
                })
                .then((res) => {
                    toast.success("No. Telepon berhasil diverifikasi");
                    unblockBtn("#next-form");
                    callback && callback();
                })
                .catch((err) => {
                    toast.error(err.response.data.message);
                    unblockBtn("#next-form");
                });
        };

        const handleStep = handleSubmit((values) => {
            resetForm({
                values: {
                    ...formData.value,
                },
            });

            if (currentStepIndex.value === 0) {
                sendOtpEmail(() => {
                    formData.value = { ...values };

                    currentStepIndex.value++;

                    if (!_stepperObj.value) {
                        return;
                    }

                    _stepperObj.value.goNext();
                });
            } else if (currentStepIndex.value === 1) {
                checkOtpEmail(() =>
                    sendOtpPhone(() => {
                        formData.value = { ...values };

                        currentStepIndex.value++;

                        if (!_stepperObj.value) {
                            return;
                        }

                        _stepperObj.value.goNext();
                    })
                );
            } else if (currentStepIndex.value === 2) {
                checkOtpPhone(() => {
                    formData.value = { ...values };

                    currentStepIndex.value++;

                    if (!_stepperObj.value) {
                        return;
                    }

                    _stepperObj.value.goNext();
                });
            } else if (currentStepIndex.value === 3) {
                formData.value = { ...values };

                formSubmit(values);
            } else {
                formData.value = { ...values };

                currentStepIndex.value++;

                if (!_stepperObj.value) {
                    return;
                }

                _stepperObj.value.goNext();
            }
        });

        const previousStep = () => {
            if (!_stepperObj.value) {
                return;
            }

            currentStepIndex.value--;

            _stepperObj.value.goPrev();
        };

        const formSubmit = (values: CreateAccount) => {
            blockBtn("#submit-form");

            axios
                .post("/auth/register", values)
                .then((res) => {
                    toast.success("Akun berhasil dibuat");
                    router.push({ name: "sign-in" });
                })
                .catch((err) => {
                    toast.error(err.response.data.message);
                    unblockBtn("#submit-form");
                });
        };

        const timeIntv = ref<any>(null);
        const handleOtpInterval = () => {
            clearInterval(timeIntv.value);

            timeIntv.value = setInterval(() => {
                setOtpInterval.value(otpInterval.value - 1);

                if (otpInterval.value === 0) {
                    clearInterval(timeIntv.value);
                }
            }, 1000);
        };

        return {
            horizontalWizardRef,
            previousStep,
            handleStep,
            formSubmit,
            totalSteps,
            currentStepIndex,
            getAssetPath,
            formData,
            sendOtpEmail,
            sendOtpPhone,
            resetForm,
            setting,
        };
    },
    methods: {
        handleOtpEmail(value: string) {
            this.resetForm({
                values: {
                    ...this.formData,
                    otp_email: value,
                },
            });
            this.formData.otp_email = value;
        },
        handleOtpPhone(value: string) {
            this.resetForm({
                values: {
                    ...this.formData,
                    otp_phone: value,
                },
            });
            this.formData.otp_phone = value;
        },
    },
});
</script>
