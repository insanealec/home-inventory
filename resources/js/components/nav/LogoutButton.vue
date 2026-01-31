<script setup>
import axios from "axios";
import { ref } from 'vue';
import { useRouter } from "vue-router";

const router = useRouter();
const form = ref({});

const logout = async () => {
    try {
        await axios.post('/logout', form.value);
        await router.push("/");
    } catch (error) {
        console.error('Logout failed:', error);
    }
};
</script>

<template>
  <form @submit.prevent="logout">
  <button
    type="submit"
  >
    Logout
  </button>
  </form>
</template>