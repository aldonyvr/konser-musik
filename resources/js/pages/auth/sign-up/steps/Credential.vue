<template>
    <section class="w-100">
        <!--begin::Input group-->
        <div class="fv-row mb-7">
            <label class="form-label fw-bold text-white fs-6">Nama</label>
            <Field class="form-control form-control-lg form-control-solid" type="text" name="name" autocomplete="off"
                v-model="formData.nama" />
            <div class="fv-plugins-message-container">
                <div class="fv-help-block">
                    <ErrorMessage name="name" />
                </div>
            </div>
        </div>
        <!--end::Input group-->

        <div class="fv-row mb-7">
            <label class="form-label fw-bold text-white fs-6">No. Telepon</label>
            <Field class="form-control form-control-lg form-control-solid" type="phone" name="phone" autocomplete="off"
                v-model="formData.phone" />
            <div class="fv-plugins-message-container">
                <div class="fv-help-block">
                    <ErrorMessage name="phone" />
                </div>
            </div>
        </div>
        <!--begin::Input group-->
        <div class="fv-row mb-7">
            <label class="form-label fw-bold text-white fs-6">Email</label>
            <Field class="form-control form-control-lg form-control-solid" type="email" name="email" autocomplete="off"
                v-model="formData.email" />
            <div class="fv-plugins-message-container">
                <div class="fv-help-block">
                    <ErrorMessage name="email" />
                </div>
            </div>
        </div>
        <div class="fv-row mb-7">
            <label class="form-label fw-bold text-white fs-6">Password</label>
            <Field class="form-control form-control-lg form-control-solid" type="password" name="password" autocomplete="off"
                v-model="formData.password" />
            <div class="fv-plugins-message-container">
                <div class="fv-help-block">
                    <ErrorMessage name="password" />
                </div>
            </div>
        </div>
        <!--end::Input group-->

        <!--begin::Input group-->
        <!--end::Input group-->
    </section>
</template>

<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
    name: 'Credential',
    props: {
        formData: {
            type: Object,
            required: true,
        },
    },
    setup(props) {
        return {
            formData: props.formData,
        }
    },
})
</script>
