<script setup>
import LogoutButton from './LogoutButton.vue';

</script>
<template>
    <nav class="bg-white shadow dark:bg-gray-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <div class="flex">
                    <div class="flex flex-shrink-0 items-center">
                        <h1
                            class="text-xl font-bold text-gray-900 dark:text-white"
                        >
                            Inventory Manager
                        </h1>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <router-link
                            to="/login"
                            class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300"
                        >
                            Login
                        </router-link>
                        <router-link
                            to="/register"
                            class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300"
                        >
                            Register
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>
