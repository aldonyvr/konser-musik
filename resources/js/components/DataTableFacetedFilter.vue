<script setup lang="ts">
import { ref, computed } from 'vue';

const props = defineProps<{
  column: any;
  title: string;
  options: { label: string; value: string | number }[];
}>();

const selectedValues = ref(new Set());

const facets = computed(() => props.column?.getFacetedUniqueValues());

const getFacetCount = (value: string | number) => {
  return facets.value?.get(value) || facets.value?.get(value.toString()) || 0;
};

const toggleValue = (value: string | number) => {
  const newSelected = new Set(selectedValues.value);
  if (newSelected.has(value)) {
    newSelected.delete(value);
  } else {
    newSelected.add(value);
  }
  selectedValues.value = newSelected;
  props.column?.setFilterValue(Array.from(newSelected));
};

const resetFilter = () => {
  selectedValues.value = new Set();
  props.column?.setFilterValue(undefined);
};
</script>

<template>
  <div class="dropdown">
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
      {{ title }}
      <span v-if="selectedValues.size" class="badge bg-secondary ms-2">
        {{ selectedValues.size }}
      </span>
    </button>
    <div class="dropdown-menu p-2">
      <input type="text" class="form-control mb-2" :placeholder="'Search ' + title">
      <div class="dropdown-divider"></div>
      <div v-for="option in options" :key="option.value" class="dropdown-item">
        <label class="d-flex align-items-center gap-2">
          <input 
            type="checkbox"
            :checked="selectedValues.has(option.value)"
            @change="toggleValue(option.value)"
            class="form-check-input"
          >
          <span>{{ option.label }}</span>
          <span class="badge bg-secondary ms-auto">{{ getFacetCount(option.value) }}</span>
        </label>
      </div>
      <div v-if="selectedValues.size" class="dropdown-divider"></div>
      <button 
        v-if="selectedValues.size"
        class="btn btn-link btn-sm w-100"
        @click="resetFilter"
      >
        Reset
      </button>
    </div>
  </div>
</template>
