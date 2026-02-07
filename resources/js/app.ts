import "./bootstrap";
import { createPinia } from "pinia";
import { createApp } from "vue";
import { createRouter, createWebHistory } from "vue-router";

// Import CSS
import "../css/app.css";

// Import components
import App from "./components/App.vue";
import Welcome from "./components/pages/Welcome.vue";
import Login from "./components/pages/auth/Login.vue";
import Register from "./components/pages/auth/Register.vue";
import Dashboard from "./components/pages/Dashboard.vue";
import InventoryIndex from "./components/pages/inventory-items/Index.vue";
import InventoryCreate from "./components/pages/inventory-items/Create.vue";
import InventoryShow from "./components/pages/inventory-items/Show.vue";

// Create router
const routes = [
    { path: "/", component: Welcome },
    { path: "/register", component: Register },
    { path: "/login", component: Login },
    { path: "/dashboard", component: Dashboard },
    { path: "/inventory", component: InventoryIndex },
    { path: "/inventory/create", component: InventoryCreate },
    { path: "/inventory/:id", component: InventoryShow },
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
app.mount("#body");
