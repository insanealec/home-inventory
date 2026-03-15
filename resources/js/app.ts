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
import InventoryUpdate from "./components/pages/inventory-items/Update.vue";
import StockLocationIndex from "./components/pages/stock-locations/Index.vue";
import StockLocationCreate from "./components/pages/stock-locations/Create.vue";
import StockLocationShow from "./components/pages/stock-locations/Show.vue";
import StockLocationUpdate from "./components/pages/stock-locations/Update.vue";
import ShoppingListIndex from "./components/pages/shopping-lists/Index.vue";
import ShoppingListCreate from "./components/pages/shopping-lists/Create.vue";
import ShoppingListShow from "./components/pages/shopping-lists/Show.vue";
import ShoppingListUpdate from "./components/pages/shopping-lists/Update.vue";

// Create router
const routes = [
    { path: "/", component: Welcome },
    { path: "/register", component: Register },
    { path: "/login", component: Login },
    { path: "/dashboard", component: Dashboard },
    { path: "/inventory", component: InventoryIndex },
    { path: "/inventory/create", component: InventoryCreate },
    { path: "/inventory/:id", component: InventoryShow },
    { path: "/inventory/:id/edit", component: InventoryUpdate },
    { path: "/stock-locations", component: StockLocationIndex },
    { path: "/stock-locations/create", component: StockLocationCreate },
    { path: "/stock-locations/:id", component: StockLocationShow },
    { path: "/stock-locations/:id/edit", component: StockLocationUpdate },
    { path: "/shopping-lists", component: ShoppingListIndex },
    { path: "/shopping-lists/create", component: ShoppingListCreate },
    { path: "/shopping-lists/:id", component: ShoppingListShow },
    { path: "/shopping-lists/:id/edit", component: ShoppingListUpdate },
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
