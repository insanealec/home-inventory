import './bootstrap';
import { createPinia } from 'pinia';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';

// Import components
import App from './components/App.vue';
import Welcome from './components/pages/Welcome.vue';

// Import CSS
import '../css/app.css';
import Login from './components/pages/auth/Login.vue';
import Register from './components/pages/auth/Register.vue';

// Create router
const routes = [
    { path: '/', component: Welcome },
    { path: '/register', component: Register },
    { path: '/login', component: Login },
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
