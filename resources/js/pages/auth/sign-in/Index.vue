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
                Masuk ke <span class="text-primary">{{ setting?.app }}</span>
            </h2>
        </div>

        <ul class="nav nav-tabs nav-line-tabs mb-3 fs-6">
            <li class="nav-item">
                <a
                    class="nav-link active"
                    data-bs-toggle="tab"
                    href="#with-email"
                    >Email</a
                >
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#with-phone">{{ $t('login.telepon') }}</a>
            </li> -->
        </ul>

        <div class="tab-content" id="myTabContent">
            <div
                class="tab-pane fade show active"
                id="with-email"
                role="tabpanel"
            >
                <WithEmail />
            </div>
        </div>

        <div class=" border-gray-300 w-100 mt-5 mb-10"></div>

        <!--begin::Link-->
        <div class="text-gray-500 fw-semobold fs-4 text-center">
            Belum memiliki akun?

            <router-link to="/sign-up" class="link-primary fw-bold">
                REGISTER 
            </router-link>
        </div>
        <!--end::Link-->
    </div>
    <!--end::Form-->
</template>

<script>
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent, ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { blockBtn, unblockBtn } from "@/libs/utils";
import WithEmail from "./tabs/WithEmail.vue";
import WithPhone from "./tabs/WithPhone.vue";
import { useSetting } from "@/services";

export default defineComponent({
    name: "sign-in",
    components: {
        WithEmail,
        WithPhone,
    },
    setup() {
        const store = useAuthStore();
        const router = useRouter();
        const { data: setting = {} } = useSetting();

        const submitButton = ref(null);

        //Create form validation object
        const login = Yup.object().shape({
            identifier: Yup.string()
                .email("Email/No. Telepon tidak valid")
                .required("Harap masukkan Email/No. Telepon")
                .label("Email"),
            password: Yup.string()
                .min(8, "Password minimal terdiri dari 8 karakter")
                .required("Harap masukkan password")
                .label("Password"),
        });

        return {
            login,
            submitButton,
            getAssetPath,
            store,
            router,
            setting,
        };
    },
    data() {
        return {
            data: {
                identifier: null,
                password: null,
            },
            check: {
                type: "",
                error: "",
            },
        };
    },
    methods: {
        submit() {
            blockBtn(this.submitButton);

            axios
                .post("/auth/login", { ...this.data, type: this.check.type })
                .then((res) => {
                    this.store.setAuth(res.data.user, res.data.token);
                    this.router.push("/dashboard");
                })
                .catch((error) => {
                    toast.error(error.response.data.message);
                })
                .finally(() => {
                    unblockBtn(this.submitButton);
                });
        },
        checkInput(value) {
            this.check.type = "";
            if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value)) {
                this.check.type = "email";
            } else {
                this.check.type = "phone";
                if (isNaN(this.data.identifier)) {
                    this.check.type =
                        "Masukkan Email / No. Telepon Yang Valid!";
                }
            }
        },
    },
});
</script>
