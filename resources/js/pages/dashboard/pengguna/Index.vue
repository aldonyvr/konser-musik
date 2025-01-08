<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { User } from "@/types";

const column = createColumnHelper<User>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);

const { delete: deleteUser } = useDelete({
    onSuccess: () => {
        paginateRef.value.refresh();
    },
});



const columns = [
    // column.accessor("no", {
    //     header: "No",
    // }),
    column.accessor("nama", {
        header: "Nama",
        cell: (info) => info.getValue(),
    }),
    column.accessor("email", {
        header: "Email",
        cell: (info) => info.getValue(),
    }),
    column.accessor("phone", {
        header: "No. Telp",
        cell: (info) => info.getValue(),
    }),
    column.accessor("created_at", {
        header: "Created At",
        cell: (info) => info.getValue(),
    }),
    column.accessor("uuid", {

    header: "Aksi",
    cell: (cell) =>
        h("div", { class: "d-flex gap-2" }, [
            h(
                "button",
                {
                    class: "btn btn-sm btn-icon btn-info",
                    onClick: () => {
                        selected.value = cell.getValue();
                        openForm.value = true;
                    },
                },
                h("i", { class: "la la-pencil fs-2" })
            ),
            h(
                "button",
                {
                    class: "btn btn-sm btn-icon btn-danger",
                    onClick: () =>
                        deleteUser(`konser/konser/${cell.getValue()}`),
                },
                h("i", { class: "la la-trash fs-2" })
            ),
        ]),
    }),
];

const refresh = () => paginateRef.value.refetch();

watch(openForm, (val) => {
    if (!val) {
        selected.value = "";
    }
    window.scrollTo(0, 0);
});

</script>

<template>
    <Form
        :selected="selected"
        @close="openForm = false"
        v-if="openForm"
        @refresh="refresh"
    />    
    <div class="card">
        <div class="card-header align-items-center">
            <h2 class="mb-0">List Pengguna</h2>
            <button
                type="button"
                class="btn btn-sm btn-primary ms-auto"
                v-if="!openForm"
                @click="openForm = true"
            >
                Tambah Pengguna
                <i class="la la-plus"></i>
            </button>
        </div>
        <div class="card-body">
            <paginate
                ref="paginateRef"
                id="table-users"
                url="/register/get"
                :columns="columns"
            ></paginate>
        </div>
    </div>
</template>