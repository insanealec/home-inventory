<script setup lang="ts">
import { Pagination } from "../../types/common";
const props = defineProps<{
    paginator: Pagination;
}>();
</script>

<template>
    <div class="mt-4">
        <nav
            class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 sm:px-6"
            aria-label="Pagination"
        >
            <div class="hidden sm:block">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    Showing
                    <span class="font-medium">{{
                        (paginator.current_page - 1) * paginator.per_page + 1
                    }}</span>
                    to
                    <span class="font-medium">{{
                        Math.min(
                            paginator.current_page * paginator.per_page,
                            paginator.total,
                        )
                    }}</span>
                    of
                    <span class="font-medium">{{ paginator.total }}</span>
                    results
                </p>
            </div>
            <div class="flex-1 flex justify-between sm:justify-end">
                <RouterLink
                    v-for="link in paginator.links"
                    :key="link.label"
                    :to="{
                        path: '/inventory',
                        query: {
                            page: link.url ? link.page : paginator.current_page,
                        },
                    }"
                    :class="{
                        'relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700':
                            link.url,
                        'relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800 cursor-not-allowed':
                            !link.url,
                        'bg-gray-100 dark:bg-gray-700': link.active,
                    }"
                    :disabled="!link.url"
                >
                    <span v-html="link.label"></span>
                </RouterLink>
            </div>
        </nav>
    </div>
</template>
