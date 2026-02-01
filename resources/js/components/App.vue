<script setup>
import { useAuthStore } from '../stores/auth';
import NavMain from './nav/NavMain.vue';
import NavGuest from './nav/NavGuest.vue';

const authStore = useAuthStore();
authStore.setUser(window?.App?.user || null);

</script>

<template>
    <div id="app">
        <nav-main v-if="authStore.isAuthenticated" />
        <nav-guest v-else />
        <main class="py-6 bg-white dark:bg-gray-900">
            <router-view />
        </main>
    </div>
</template>

