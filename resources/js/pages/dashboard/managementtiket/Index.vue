<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { User } from "@/types";
import { currency } from "@/libs/utils";

const column = createColumnHelper<User>();
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
    column.accessor("konsers.title", {
        header: "Title",
    }),
    // column.accessor("konsers.harga", {
    //     header: "Harga Tiket",
    //     cell: cell => currency(cell.getValue())
    // }),
    column.accessor("konsers.tiket_tersedia", {
        header: "Total Jumlah Tiket",
    }),
    column.accessor("vip", {
        header: "Tiket VIP Tersedia",
        cell: ({ row }) => {
            const vip =row.original?.vip ?? "-";
            const harga_vip = row.original?.harga_vip ?? "-";

            return h("div", [
                h("div", [
                    h("span", " Tiket VIP : "),
                    h("span", vip),
                ]),
                h("div", [
                    h("span", "Harga Tiket VIP :  "),
                    h("span", harga_vip),
                ]),
            ]);
        },
    }),
    column.accessor("reguler", {
        header: "Tiket REGULER Tersedia",
        cell: ({ row }) => {
            const reguler = row.original?.reguler ?? "-";
            const gate_a_capacity = row.original?.gate_a_capacity ?? "-";
            const gate_b_capacity = row.original?.gate_b_capacity ?? "-";
            const gate_c_capacity = row.original?.gate_c_capacity ?? "-";
            const gate_d_capacity = row.original?.gate_d_capacity ?? "-";
            const gate_e_capacity = row.original?.gate_e_capacity ?? "-";
            const harga_regular = row.original?.harga_regular ?? "-";

            return h("div", [
                h("div", [
                    h("span", " Tiket Regular  : "),
                    h("span", reguler),
                ]),
                h("div", [
                    h("span", "Harga Tiket Regular :  "),
                    h("span", harga_regular),
                ]),
                
                h("div", [
                    h("span", " Gate A  : "),
                    h("span", gate_a_capacity),
                ]),
                h("div", [
                    h("span", " Gate B  : "),
                    h("span", gate_b_capacity),
                ]),
                h("div", [
                    h("span", " Gate C  : "),
                    h("span", gate_c_capacity),
                ]),
                h("div", [
                    h("span", " Gate D  : "),
                    h("span", gate_d_capacity),
                ]),
                h("div", [
                    h("span", " Gate E  : "),
                    h("span", gate_e_capacity),
                ]),
                
            ]);
        },
    }),
    
    column.accessor("gateTimes", {
        header: "Detail Jam Buka & Tutup Gate",
        cell: ({ row }) => {
            const opengate = row.original?.opengate ?? "-";
            const closegate = row.original?.closegate ?? "-";

            return h("div", [
                h("div", [
                    h("span", "Open Gate: "),
                    h("span", opengate),
                ]),
                h("div", [
                    h("span", "Close Gate: "),
                    h("span", closegate),
                ]),
            ]);
        },
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
                            deleteUser(`/konser/destroy/${cell.getValue()}`),
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
        </div>
        <div class="card-body">
            <paginate
                ref="paginateRef"
                id="table-users"
                url="/tiket"
                :columns="columns"
            ></paginate>
        </div>
    </div>
</template>
