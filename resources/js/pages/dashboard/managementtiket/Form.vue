<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch, computed } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { User, Role } from "@/types";
import ApiService from "@/core/services/ApiService";
import { useRole } from "@/services/useRole";

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);

const user = ref<User>({} as User);
const formRef = ref()


function getEdit() {
    block(document.getElementById("form-user"));
    ApiService.get("/konser/edit", props.selected)
        .then(({ data }) => {
            user.value = data.data;
        })
        .catch((err: any) => {
            toast.error(err.response.data.message);
        })
        .finally(() => {
            unblock(document.getElementById("form-user"));
        });
}



function submit() {
    const formData = new FormData();
    formData.append("no", user.value.no);
    formData.append("title", user.value.title);
    formData.append("harga", user.value.harga);
    formData.append("vip", user.value.vip);
    formData.append("reguler", user.value.reguler);
    formData.append("opengate", user.value.opengate);
    formData.append("closegate", user.value.vip);

    block(document.getElementById("form-user"));
    axios({
        method: "post",
        url: props.selected
            ? `/tiket/update/${props.selected}`
            : "/tiket/store",
        data: formData,
        headers: {
            "Content-Type": "multipart/form-data",
        },
    })
        .then(() => {
            emit("close");
            emit("refresh");
            toast.success("Data berhasil disimpan");
            formRef.value.resetForm();
        })
        .catch((err: any) => {
            formRef.value.setErrors(err.response.data.errors);
            toast.error(err.response.data.message);
        })
        .finally(() => {
            unblock(document.getElementById("form-user"));
        });
}

const role = useRole();
const roles = computed(() =>
    role.data.value?.map((item: Role) => ({
        uuid: item.uuid,
        text: item.full_name,
    }))
);

onMounted(async () => {
    if (props.selected) {
        getEdit();
    }
});

watch(
    () => props.selected,
    () => {
        if (props.selected) {
            getEdit();
        }
    }
);
</script>

<template>
    <VForm 
    class="form card mb-10" 
    @submit="submit" 
    :validation-schema="formSchema" 
    id="form-user" 
    ref="formRef">

        <div class="card-header align-items-center">
            <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Negara</h2>
            <button type="button" class="btn btn-sm btn-light-danger ms-auto" @click="emit('close')">
                Batal
                <i class="la la-times-circle p-0"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Nama Konser
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="nama_negara"
                            autocomplete="off" v-model="user.title" placeholder="Nama Negara" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="title" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Harga
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="harga"
                            autocomplete="off" v-model="user.harga" placeholder="Harga" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="harga" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-3">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Tiket VIP
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="vip"
                            autocomplete="off" v-model="user.vip" placeholder="Tiket VIP" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="vip" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-3">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Tiket REGULER
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="reguler"
                            autocomplete="off" v-model="user.reguler" placeholder="Tiket REGULER" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="reguler" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-3">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Open Gate
                        </label>
                        <Field name="opengate" class="form-control form-control-lg form-control-solid"
                            autocomplete="off">
                            <date-picker v-model="user.opengate" placeholder=" Open Gate" :config="{
                                enableTime: true,
                                noCalendar: true,
                                format: 'H:i'
                            }" />
                        </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="opengate" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-3">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Close Gate
                        </label>
                        <Field name="closegate" class="form-control form-control-lg form-control-solid"
                            autocomplete="off">
                            <date-picker v-model="user.closegate" placeholder=" Close Gate" :config="{
                                enableTime: true,
                                noCalendar: true,
                                format: 'H:i'
                            }" />
                        </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="closegate" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>

                <!--end::Input group-->

            </div>
        </div>
        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-primary btn-sm ms-auto">
                Simpan
            </button>
        </div>
    </VForm>
</template>
