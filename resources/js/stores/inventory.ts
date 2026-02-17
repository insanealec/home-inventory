import { defineStore } from "pinia";
import { ref } from "vue";
import axios from "axios";
import { createInventoryItem, type InventoryItem } from "../types/inventory";
import { Pagination } from "../types/common";
import CreateItem from "../actions/App/Actions/InventoryItem/CreateItem";
import LoadItems from "../actions/App/Actions/InventoryItem/LoadItems";
import LoadItem from "../actions/App/Actions/InventoryItem/LoadItem";
import UpdateItem from "../actions/App/Actions/InventoryItem/UpdateItem";
import DeleteItem from "../actions/App/Actions/InventoryItem/DeleteItem";

export const useInventoryStore = defineStore("inventory", () => {
    const paginator = ref<Pagination<InventoryItem>>({
        data: [],
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
        links: [],
    } as Pagination<InventoryItem>);
    const item = ref<InventoryItem | null>(null);
    const errors = ref<Record<string, string>>({});
    const loading = ref(false);
    const creating = ref(false);
    const updating = ref(false);
    const deleting = ref(false);

    const loadItems = async (
        page: number = 1,
        sortField: string | null = null,
        sortDirection: "asc" | "desc" = "asc",
    ) => {
        try {
            loading.value = true;
            const params: any = { page };
            if (sortField) {
                params.sort = sortField;
                params.direction = sortDirection;
            }
            const response = await axios.get(LoadItems.url(), { params });
            paginator.value = response.data;
        } catch (error) {
            console.error("Error loading inventory items:", error);
        } finally {
            loading.value = false;
        }
    };

    const loadItem = async (id: number) => {
        try {
            loading.value = true;
            const response = await axios.get(LoadItem.url(id));
            item.value = response.data;
        } catch (error) {
            console.error("Error loading inventory item:", error);
        } finally {
            loading.value = false;
        }
    };

    const initItem = () => {
        item.value = createInventoryItem();
    };

    const createItem = async () => {
        try {
            creating.value = true;
            // Reset errors
            errors.value = {};
            const response = await axios.post(CreateItem.url(), item.value);
            return true;
        } catch (error: any) {
            if (error.response && error.response.data.errors) {
                // Handle validation errors
                errors.value = error.response.data.errors;
            }
            console.error("Error creating inventory item:", error);
            return false;
        } finally {
            creating.value = false;
        }
    };

    const updateItem = async () => {
        if (!item.value?.id) return false;
        try {
            updating.value = true;
            const response = await axios.put(UpdateItem.url(item.value.id), item.value);
            item.value = response.data;
            return true;
        } catch (error) {
            console.error("Error updating inventory item:", error);
            return false;
        } finally {
            updating.value = false;
        }
    };

    const deleteItem = async (id: number) => {
        try {
            deleting.value = true;
            await axios.delete(DeleteItem.url(id));
            return true;
        } catch (error) {
            console.error("Error deleting inventory item:", error);
            return false;
        } finally {
            deleting.value = false;
        }
    };

    return {
        paginator,
        item,
        errors,
        loading,
        creating,
        updating,
        deleting,
        loadItems,
        loadItem,
        initItem,
        createItem,
        updateItem,
        deleteItem,
    };
});
