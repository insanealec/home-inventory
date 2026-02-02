<script setup>
import { onMounted } from "vue";
import { useAuthStore } from "../../../stores/auth";

const authStore = useAuthStore();

onMounted(() => {
    authStore.errors = {};
});
</script>
<template>
    <div
        class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900"
    >
        <div
            class="bg-white p-8 rounded shadow-md w-full max-w-md dark:bg-gray-800"
        >
            <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">
                Login
            </h2>
            <form @submit.prevent="authStore.login" class="space-y-4">
                <div>
                    <label
                        for="email"
                        class="block text-gray-700 dark:text-gray-300 mb-1"
                        >Email</label
                    >
                    <input
                        v-model="authStore.loginForm.email"
                        type="email"
                        id="email"
                        class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        required
                    />
                    <div v-if="authStore.errors.email" class="text-red-500 text-sm mt-1">
                        <div
                            v-for="(error, index) in authStore.errors.email"
                            :key="index"
                        >
                            {{ error }}
                        </div>
                    </div>
                </div>
                <div>
                    <label
                        for="password"
                        class="block text-gray-700 dark:text-gray-300 mb-1"
                        >Password</label
                    >
                    <input
                        v-model="authStore.loginForm.password"
                        type="password"
                        id="password"
                        class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        required
                    />
                    <div
                        v-if="authStore.errors.password"
                        class="text-red-500 text-sm mt-1"
                    >
                        <div
                            v-for="(error, index) in authStore.errors.password"
                            :key="index"
                        >
                            {{ error }}
                        </div>
                    </div>
                </div>
                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition dark:hover:bg-blue-800"
                >
                    Login
                </button>
            </form>
        </div>
    </div>
</template>
