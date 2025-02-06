<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import { createColumnHelper } from "@tanstack/vue-table";
import type { Konser } from "@/types";

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
    column.accessor("nama_pemesan", {
        header: "Nama Pemesan",
    }),
    column.accessor("email_pemesan", {
        header: "Email Pemesan",
    }),
    column.accessor("telepon_pemesan", {
        header: "Telepon Pemesan",
    }),
    column.accessor("status_pembayaran", {
        header: "Status Pembayaran",
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

    <div class="card">
        <div class="card-header align-items-center">
            <h2 class="mb-0"></h2>
            <!-- <button
                type="button"
                class="btn btn-sm btn-primary ms-auto"
                v-if="!openForm"
                @click="openForm = true"
            >
                Tambah Konser
                <i class="la la-plus"></i>
            </button> -->
        </div>
        <div class="card-body">
            <paginate
                ref="paginateRef"
                id="table-users"
                url="/datapemesan"
                :columns="columns"
            ></paginate>
        </div>
    </div>

</template>