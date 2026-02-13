<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{
    id: string;
    label: string;
    type?: string;
    min?: number;
    step?: number;
    error?: string;
    field?: string;
    store?: any;
    modelValue?: any;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: any];
}>();

// Helper to get/set nested object values using dot notation (e.g., "item.name")
const getNestedValue = (obj: any, path: string): any => {
    return path.split('.').reduce((current, key) => current?.[key], obj);
};

const setNestedValue = (obj: any, path: string, value: any): void => {
    const keys = path.split('.');
    const lastKey = keys.pop();
    const target = keys.reduce((current, key) => current[key], obj);
    if (lastKey) {
        target[lastKey] = value;
    }
};

// Support two modes: store-based (field + store) or v-model based (modelValue)
const fieldValue = computed({
    get() {
        if (props.field && props.store) {
            return getNestedValue(props.store, props.field);
        }
        return props.modelValue;
    },
    set(value: any) {
        if (props.field && props.store) {
            setNestedValue(props.store, props.field, value);
        } else {
            emit('update:modelValue', value);
        }
    },
});
</script>

<template>
    <div>
        <label
            :for="id"
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
        >
            {{ label }}
        </label>
        <input
            :id="id"
            v-model="fieldValue"
            :type="type"
            :min="min || undefined"
            :step="step || undefined"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
        />
        <p
            v-if="error"
            class="mt-1 text-sm text-red-600"
        >
            {{ error }}
        </p>
    </div>
</template>