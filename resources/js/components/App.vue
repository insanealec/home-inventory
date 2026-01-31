<script setup>
import NavMain from './nav/NavMain.vue';

</script>

<template>
    <div id="app">
        <nav-main />
        <main class="py-6 bg-white dark:bg-gray-900">
            <router-view />
        </main>
    </div>
</template>

