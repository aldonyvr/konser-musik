<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { Konser } from "@/types";
import { currency } from '@/libs/utils';

const column = createColumnHelper<Konser>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);

const { delete: deleteUser } = useDelete({
    onSuccess: () => paginateRef.value.refetch(),
});

const columns = [
    column.accessor("no", {
        header: "No",
    }),
    column.accessor("title", {
        header: "Nama Konser",
    }),
    column.accessor("lokasi", {
        header: "Lokasi",
    }),
    column.accessor("tiket_tersedia", {
        header: "Total Tiket Tersedia",
    }),
    column.accessor("harga", {
        header: "Harga",
        cell: cell => currency(cell.getValue())
    }),
    column.accessor("tanggal", {
        header: "Tanggal",
    }),
    column.accessor("jam", {
        header: "Jam",
    }),
    column.accessor("image", {
        header: "Image",
        cell: cell => h('img', {src: `${cell.getValue()}`, width: 150 })
    }),
    column.accessor("deskripsi", {
        header: "Deskripsi",
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
            <h2 class="mb-0"></h2>
            <button
                type="button"
                class="btn btn-sm btn-primary ms-auto"
                v-if="!openForm"
                @click="openForm = true"
            >
                Tambah Konser
                <i class="la la-plus"></i>
            </button>
        </div>
        <div class="card-body">
            <paginate
                ref="paginateRef"
                id="table-users"
                url="/konser"
                :columns="columns"
            ></paginate>
        </div>
    </div>

</template>