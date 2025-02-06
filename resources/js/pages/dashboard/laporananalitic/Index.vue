<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import { createColumnHelper } from "@tanstack/vue-table";
import type { Konser } from "@/types";
import { currency } from '@/libs/utils';
import axios from '@/libs/axios';

const column = createColumnHelper<Konser>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);

const showReportModal = ref<boolean>(false);
const currentReportData = ref<any>(null);

const { delete: deleteUser } = useDelete({
  onSuccess: () => paginateRef.value.refetch(),
});

// Function to view detailed report
const viewReport = async (uuid: string) => {
  try {
    const response = await axios.get(`/konser/report/${uuid}`);
    currentReportData.value = response.data;
    showReportModal.value = true;
  } catch (error) {
    console.error('Failed to fetch report:', error);
    // Optionally show an error notification
  }
};

// Function to download report
const downloadReport = async (uuid: string, type: 'excel' | 'pdf') => {
  try {
    const response = await axios.get(`/konser/download-report/${uuid}`, {
      responseType: 'blob',
      params: { type }
    });

    // Create a link element to trigger download
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `konser-report-${uuid}.${type}`);
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (error) {
    console.error('Failed to download report:', error);
    // Optionally show an error notification
  }
};

const columns = [
  column.accessor("no", {
    header: "No",
  }),
  column.accessor("image", {
    header: "Image",
    cell: cell => h('img', { src: `${cell.getValue()}`, width: 150 })
  }),
  column.accessor("title", {
    header: "Nama Konser",
  }),
  column.accessor("lokasi", {
    header: "Lokasi",
  }),
  column.accessor("tanggal", {
    header: "Tanggal",
  }),
  column.accessor("tiket_tersedia", {
    header: "Total Tiket",
  }),
  column.accessor("uuid", {
    header: "Aksi",
    cell: (cell) =>
      h("div", { class: "d-flex gap-2 align-items-center" }, [
        // Tombol Laporan dengan Teks
        h(
          "button",
          {
            class: "btn btn-sm btn-primary d-flex align-items-center gap-1",
            onClick: () => viewReport(cell.getValue()),
          },
          [
            h("i", { class: "la la-file-alt fs-5" }),
            "Laporan"
          ]
        ),

        // Dropdown Download Button
        h("div", { class: "dropdown" }, [
          h(
            "button",
            {
              class: "btn btn-sm btn-success dropdown-toggle",
              type: "button",
              "data-bs-toggle": "dropdown",
              "aria-expanded": "false"
            },
            [
              h("i", { class: "la la-download fs-5" }),
              " Download"
            ]
          ),
          h(
            "ul",
            { class: "dropdown-menu" },
            [
              h(
                "li",
                h(
                  "a",
                  {
                    class: "dropdown-item",
                    onClick: () => downloadReport(cell.getValue(), 'excel')
                  },
                  "Download Excel"
                )
              ),
              h(
                "li",
                h(
                  "a",
                  {
                    class: "dropdown-item",
                    onClick: () => downloadReport(cell.getValue(), 'pdf')
                  },
                  "Download PDF"
                )
              )
            ]
          )
        ]),

        // Tombol Hapus
        h(
          "button",
          {
            class: "btn btn-sm btn-danger",
            onClick: () => deleteUser(`konser/konser/${cell.getValue()}`),
          },
          h("i", { class: "la la-trash fs-5" })
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
      <h1 class="text-center font-bold mb-2 text-center">Dashboard Analytics Konser</h1>
    </div>
    <div class="card-body">
      <paginate ref="paginateRef" id="table-users" url="/konser/index" :columns="columns"></paginate>
    </div>
  </div>

  <!-- Report Modal -->
  <div v-if="showReportModal" class="modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Laporan Konser Detail</h5>
          <button @click="showReportModal = false" class="close">&times;</button>
        </div>
        <div class="modal-body">
          <!-- Render report details here -->
          <pre v-if="currentReportData">
                        {{ JSON.stringify(currentReportData, null, 2) }}
                    </pre>
        </div>
      </div>
    </div>
  </div>
</template>