<script setup lang="ts">
import { ref, watch } from "vue";
import { useStockLocationStore } from "../../stores/stock-location";
import Modal from "./Modal.vue";
import StockLocationForm from "./StockLocationForm.vue";

const store = useStockLocationStore();

const isOpen = ref(false);

// Reset form when modal opens
watch(isOpen, (newVal) => {
    if (newVal) {
        store.initStockLocation();
    }
});

const openModal = () => {
    isOpen.value = true;
};

const closeModal = () => {
    isOpen.value = false;
};

const submitForm = async () => {
    if (await store.createStockLocationItem()) {
        // Close modal and reload stock locations
        closeModal();
        await store.loadStockLocations();
    }
};
</script>

<template>
    <div>
        <button
            type="button"
            @click="openModal"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
            Create New Location
        </button>

        <Modal
            :is-open="isOpen"
            title="Create New Stock Location"
            @close="closeModal"
        >
            <StockLocationForm @submit-form="submitForm" />
        </Modal>
    </div>
</template>
