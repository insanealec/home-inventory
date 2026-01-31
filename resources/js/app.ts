import { createPinia } from 'pinia';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';

// Import components
import App from './components/App.vue';
import Welcome from './components/pages/Welcome.vue';

// Import CSS
import '../css/app.css';

// Create router
const routes = [
    { path: '/', component: Welcome },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Create Pinia store
const pinia = createPinia();

// Create and mount the app
const app = createApp(App);

app.use(router);
app.use(pinia);
app.mount('#body');
