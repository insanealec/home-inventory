<script setup></script>
<template>
    <div
        class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700"
    >
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            <slot name="title"></slot>
        </h1>

        <slot></slot>
    </div>
</template>
