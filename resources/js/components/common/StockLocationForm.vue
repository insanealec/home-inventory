<script setup lang="ts">
import { useStockLocationStore } from '../../stores/stock-location';
import FormInput from './FormInput.vue';

const store = useStockLocationStore();
</script>

<template>

    <form
        v-if="store.stockLocation"
        @submit.prevent="$emit('submit-form')"
        class="space-y-6"
    >
        <!-- Required Fields -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <FormInput
                id="name"
                label="Location Name *"
                field="stockLocation.name"
                :store="store"
                :error="store.errors.name ? store.errors.name[0] : ''"
            />

            <FormInput
                id="short_name"
                label="Short Name *"
                field="stockLocation.short_name"
                :store="store"
                :error="store.errors.short_name ? store.errors.short_name[0] : ''"
            />
        </div>

        <!-- Description -->
        <div>
            <label
                for="description"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
            >
                Description
            </label>
            <textarea
                id="description"
                v-model="store.stockLocation.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
            ></textarea>
            <p
                v-if="store.errors.description"
                class="mt-1 text-sm text-red-600"
            >
                {{ store.errors.description[0] }}
            </p>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button
                type="submit"
                :disabled="store.creating || store.updating"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ store.creating || store.updating ? "Saving..." : "Save" }}
            </button>
        </div>
    </form>
</template>
