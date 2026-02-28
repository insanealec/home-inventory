<script setup lang="ts">
defineProps<{
    isOpen: boolean;
    title?: string;
}>();

const emit = defineEmits<{
    (e: "close"): void;
}>();
</script>

<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        @click="$emit('close')"
    >
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            @click.stop
        >
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        <slot name="title">{{ title }}</slot>
                    </h2>
                    <button
                        type="button"
                        @click.stop="$emit('close')"
                        class="text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <div class="mt-4">
                    <slot></slot>
                </div>
            </div>
        </div>
    </div>
</template>
