<script setup>
import { useAuthStore } from "../../stores/auth";

const authStore = useAuthStore();
</script>

<template>
    <form @submit.prevent="authStore.logout">
        <button type="submit">Logout</button>
    </form>
</template>
