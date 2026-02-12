import { defineStore } from "pinia";
import { ref } from "vue";
import axios from "axios";
import type { InventoryItem } from "../types/inventory";
import { Pagination } from "../types/common";
import CreateItem from "../actions/App/Actions/InventoryItem/CreateItem";
import LoadItems from "../actions/App/Actions/InventoryItem/LoadItems";
import LoadItem from "../actions/App/Actions/InventoryItem/LoadItem";
import DeleteItem from "../actions/App/Actions/InventoryItem/DeleteItem";

export const useInventoryStore = defineStore("inventory", () => {
    const items = ref<InventoryItem[]>([]);
    const item = ref<InventoryItem | null>(null);
    const paginator = ref<Pagination<InventoryItem>>({
        data: [],
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
        links: [],
    } as Pagination<InventoryItem>);
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
            items.value = response.data.data;
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

    const createItem = async () => {
        try {
            creating.value = true;
            const response = await axios.post(CreateItem.url(), {
                inventory_item: item.value,
            });
            return response.data;
        } catch (error) {
            console.error("Error creating inventory item:", error);
            throw error;
        } finally {
            creating.value = false;
        }
    };

    const updateItem = async () => {
        if (!item.value?.id) return false;
        try {
            updating.value = true;
            const response = await axios.put(LoadItem.url(item.value.id), {
                inventory_item: item.value,
            });
            return response.data;
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
        items,
        item,
        paginator,
        loading,
        creating,
        updating,
        deleting,
        loadItems,
        loadItem,
        createItem,
        updateItem,
        deleteItem,
    };
});
