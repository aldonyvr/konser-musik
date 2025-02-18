<template>
    <div :id="id">
        <div class="d-flex justify-content-between gap-2 flex-wrap mb-4">
            <div class="d-flex gap-4 align-items-center">
                <label htmlFor="limit" class="form-label">
                    Tampilkan
                </label>
                <select2 class="w-75px form-select-solid" v-model="per" placeholder="Per" :options="[5, 10, 25, 50, 100]">
                </select2>
            </div>
            <form @submit.prevent="refetch" class="w-100 w-sm-auto">
                <input type="search" class="form-control form-control-solid" placeholder="Cari ..." v-model="search"
                    v-debounce="onSearch" />
            </form>
        </div>
        <div class="table-responsive" style="margin-top: -8rem">
            <table class="table table-rounded table-hover table-striped border gy-7 gs-7" style="margin: 8rem 0;">
                <thead class="bg-gray-200">
                    <tr class="fw-bolder fs-6 text-gray-800 border-bottom border-gray-200"
                        v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <th v-for="header in headerGroup.headers" :key="header.id" class="py-4">
                            <FlexRender :render="header.isPlaceholder ? null : header.column.columnDef.header"
                                :props="header.getContext()" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="!!data?.data?.length">
                        <tr v-for="row in table.getRowModel().rows" :key="`row.${row.original.uuid}`">
                            <td v-for="cell in row.getVisibleCells()" :key="`cell.${cell.id}.${cell.row.original.uuid}`"
                                class="py-4">
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </td>
                        </tr>
                    </template>
                    <tr v-else>
                        <td :colspan="columns.length" class="text-center py-4">
                            Data tidak ditemukan
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between mt-n20 flex-wrap gap-2">
            <div class="text-gray-700 fs-7">
                Menampilkan {{ data?.from }} sampai {{ data?.to }} dari {{ data?.total }} hasil
            </div>
            <ul class="pagination">
                <li class="page-item previous" :class="{ disabled: data?.current_page == 1 || !data }">
                    <span @click="page = data?.current_page - 1" class="page-link">
                        <i class="previous"></i>
                    </span>
                </li>
                <li v-for="item in pagination" :key="item" @click="page = item" class="page-item"
                    :class="{ active: item === page }">
                    <span class="page-link cursor-pointer">{{ item }}</span>
                </li>
                <li class="page-item next" :class="{ disabled: data?.current_page == data?.last_page || !data }">
                    <span @click="page = data?.current_page + 1" class="page-link cursor-pointer">
                        <i class="next"></i>
                    </span>
                </li>
            </ul>
        </div>
    </div>
</template>
  
<script>
import { useQuery } from "@tanstack/vue-query";
import { ref, defineComponent, watch, onMounted } from "vue";
import {
    useVueTable,
    FlexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getSortedRowModel,
    getFacetedRowModel,
    getFacetedUniqueValues,
    getPaginationRowModel
} from "@tanstack/vue-table";
import axios from "@/libs/axios";
import { block, unblock } from "@/libs/utils";
import { toast } from "vue3-toastify";

export default defineComponent({
    props: {
        id: {
            type: String,
            required: true
        },
        columns: {
            type: Object,
            required: true,
        },
        url: {
            type: String,
            required: true
        },
        payload: {
            type: Object,
            default: {}
        },
        queryKey: {
            type: String,
            default: null
        },
        enabled: {
            type: Boolean,
            default: true
        },
        selectFilter: {
            type: String,
            default: ""
        },
        columnVisibility: {
            type: Object,
            default: () => ({})
        },
        sorting: {
            type: Array,
            default: () => []
        },
        rowSelection: {
            type: Object,
            default: () => ({})
        },
        columnFilters: {
            type: Array,
            default: () => []
        },
        globalFilter: {
            type: String,
            default: ""
        },
        columnPinning: {
            type: Object,
            default: () => ({})
        },
        filters: {
            type: Object,
            default: () => ({})
        }
    },
    components: {
        FlexRender
    },
    emits: ['table-ready'],
    setup(props, { emit }) {
        const search = ref("");
        const debouncedSearch = ref("");

        const per = ref(10);
        const page = ref(1);

        const { data = {}, isFetching, refetch } = useQuery({
            queryKey: [props.queryKey || props.url, props.filters],
            queryFn: () => axios.post(props.url, {
                search: search.value,
                page: page.value,
                per: parseInt(per.value),
                konsers_id: props.filters.konsers_id,
            }).then(res => res.data),
            placeholderData: { data: [] },
            cacheTime: 0,
            enabled: props.enabled,
            onSuccess: (data) => {
                if (page.value > data.last_page) page.value = data.last_page;
            },
            onError: (err) => {
                toast.error(err.response.data.message);
            }
        });

        const table = useVueTable({
            get data() {
                return data.value.data
            },
            columns: props.columns,
            state: {
                sorting: props.sorting,
                columnVisibility: props.columnVisibility,
                rowSelection: props.rowSelection,
                columnFilters: props.columnFilters,
                globalFilter: props.globalFilter,
                columnPinning: props.columnPinning
            },
            enableRowSelection: true,
            enableFilters: true,
            onRowSelectionChange: (newRowSelection) => {
                props.rowSelection = newRowSelection;
            },
            onSortingChange: (newSorting) => {
                props.sorting = newSorting;
            },
            onColumnFiltersChange: (updater) => {
                const newFilters = typeof updater === 'function' 
                    ? updater(props.columnFilters)
                    : updater;
                props.columnFilters = newFilters;
            },
            onColumnVisibilityChange: (updater) => {
                const newVisibility = typeof updater === 'function'
                    ? updater(props.columnVisibility)
                    : updater;
                props.columnVisibility = newVisibility;
            },
            getCoreRowModel: getCoreRowModel(),
            getFilteredRowModel: getFilteredRowModel(),
            getSortedRowModel: getSortedRowModel(),
            getFacetedRowModel: getFacetedRowModel(),
            getFacetedUniqueValues: getFacetedUniqueValues(),
            getPaginationRowModel: getPaginationRowModel(),
            onGlobalFilterChange: (newGlobalFilter) => {
                props.globalFilter = newGlobalFilter;
            },
            globalFilterFn: "includesString"
        });

        // Emit table instance after creation
        onMounted(() => {
            emit('table-ready', table);
        });

        // Add watcher for columnVisibility changes
        watch(() => props.columnVisibility, () => {
            table.setColumnVisibility(props.columnVisibility);
        }, { deep: true });

        watch(() => props.columnFilters, () => {
            refetch();
        }, { deep: true });

        return {
            search, debouncedSearch,
            table,
            per, page,
            data,
            isFetching,
            refetch
        }
    },
    methods: {
        onSearch() {
            this.debouncedSearch = this.search;
        },
    },
    watch: {
        page() {
            this.refetch();
        },
        per() {
            this.refetch();
        },
        debouncedSearch() {
            this.refetch();
        },
        isFetching(val) {
            if (val && !document.querySelector(`#${this.id} table`).querySelector(".blockui-overlay")) block(`#${this.id} table`);
            else unblock(`#${this.id} table`);
        },
        payload(val, oldVal) {
            if (JSON.stringify(val) !== JSON.stringify(oldVal)) this.refetch();
        }
    },
    computed: {
        pagination() {
            let limit = this.data?.last_page <= this.page + 1 ? 5 : 2;
            return Array.from({ length: this.data?.last_page }, (_, i) => i + 1).filter(
                (i) =>
                    i >= (this.page < 3 ? 3 : this.page) - limit && i <= (this.page < 3 ? 3 : this.page) + limit
            )
        }
    },
    mounted() {
        block(`#${this.id} table`);
    }
})
</script>
  
<style></style>
