<script setup></script>

<template>
    <div
        class="min-h-screen bg-gray-100 flex items-center justify-center dark:bg-gray-900"
    >
        <div
            class="bg-white p-8 rounded shadow-md text-center dark:bg-gray-800 max-w-md w-full"
        >
            <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">
                Welcome to Inventory Manager
            </h1>
            <p class="text-gray-700 mb-6 dark:text-gray-300">
                Manage your inventory, stock locations, and shopping lists with
                ease.
            </p>
            <div class="space-y-4">
                <router-link
                    to="/login"
                    class="block bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition dark:hover:bg-blue-800"
                >
                    Sign In
                </router-link>
                <router-link
                    to="/register"
                    class="block bg-gray-200 text-gray-800 px-6 py-3 rounded hover:bg-gray-300 transition dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
                >
                    Create Account
                </router-link>
            </div>
        </div>
    </div>
</template>
