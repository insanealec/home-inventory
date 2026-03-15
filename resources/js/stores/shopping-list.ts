import { defineStore } from "pinia";
import { ref } from "vue";
import axios from "axios";
import { ShoppingList, ShoppingListItem } from "../types/shopping-list";
import { Pagination } from "../types/common";

export const useShoppingListStore = defineStore("shoppingList", () => {
    const paginator = ref<Pagination<ShoppingList>>({
        data: [],
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
        links: [],
    } as Pagination<ShoppingList>);
    const list = ref<ShoppingList | null>(null);
    const items = ref<ShoppingListItem[]>([]);
    const errors = ref<Record<string, string>>({});
    const loading = ref(false);
    const creating = ref(false);
    const updating = ref(false);
    const deleting = ref(false);

    const loadLists = async (page: number = 1) => {
        try {
            loading.value = true;
            const response = await axios.get(`/api/shopping-lists`, { params: { page } });
            paginator.value = response.data;
        } catch (error) {
            console.error("Error loading shopping lists:", error);
        } finally {
            loading.value = false;
        }
    };

    const loadList = async (id: number) => {
        try {
            loading.value = true;
            const response = await axios.get(`/api/shopping-lists/${id}`);
            list.value = response.data;
            if (response.data.items) {
                items.value = response.data.items;
            }
        } catch (error) {
            console.error("Error loading shopping list:", error);
        } finally {
            loading.value = false;
        }
    };

    const loadItems = async (listId: number) => {
        try {
            loading.value = true;
            const response = await axios.get(`/api/shopping-lists/${listId}/items`);
            items.value = response.data;
        } catch (error) {
            console.error("Error loading shopping list items:", error);
        } finally {
            loading.value = false;
        }
    };

    const initList = () => {
        list.value = {
            id: 0,
            name: "",
            notes: "",
            is_completed: false,
            shopping_date: null,
            user_id: 0,
            created_at: "",
            updated_at: "",
        };
    };

    const createList = async (data: Partial<ShoppingList>) => {
        try {
            creating.value = true;
            errors.value = {};
            const response = await axios.post("/api/shopping-lists", data);
            list.value = response.data;
            return true;
        } catch (error: any) {
            if (error.response?.data.errors) {
                errors.value = error.response.data.errors;
            }
            console.error("Error creating shopping list:", error);
            return false;
        } finally {
            creating.value = false;
        }
    };

    const updateList = async (id: number, data: Partial<ShoppingList>) => {
        try {
            updating.value = true;
            const response = await axios.put(`/api/shopping-lists/${id}`, data);
            list.value = response.data;
            return true;
        } catch (error) {
            console.error("Error updating shopping list:", error);
            return false;
        } finally {
            updating.value = false;
        }
    };

    const deleteList = async (id: number) => {
        try {
            deleting.value = true;
            await axios.delete(`/api/shopping-lists/${id}`);
            return true;
        } catch (error) {
            console.error("Error deleting shopping list:", error);
            return false;
        } finally {
            deleting.value = false;
        }
    };

    const createItem = async (listId: number, data: Partial<ShoppingListItem>) => {
        try {
            const response = await axios.post(`/api/shopping-lists/${listId}/items`, data);
            items.value.push(response.data);
            return true;
        } catch (error) {
            console.error("Error creating shopping list item:", error);
            return false;
        }
    };

    const updateItem = async (
        listId: number,
        itemId: number,
        data: Partial<ShoppingListItem>
    ) => {
        try {
            const response = await axios.put(
                `/api/shopping-lists/${listId}/items/${itemId}`,
                data
            );
            const index = items.value.findIndex((item) => item.id === itemId);
            if (index >= 0) {
                items.value[index] = response.data;
            }
            return true;
        } catch (error) {
            console.error("Error updating shopping list item:", error);
            return false;
        }
    };

    const deleteItem = async (listId: number, itemId: number) => {
        try {
            await axios.delete(`/api/shopping-lists/${listId}/items/${itemId}`);
            items.value = items.value.filter((item) => item.id !== itemId);
            return true;
        } catch (error) {
            console.error("Error deleting shopping list item:", error);
            return false;
        }
    };

    const addBulkItems = async (
        listId: number,
        newItems: Partial<ShoppingListItem>[]
    ) => {
        try {
            const response = await axios.post(
                `/api/shopping-lists/${listId}/items/bulk`,
                { items: newItems }
            );
            if (response.data.created) {
                items.value.push(...response.data.created);
            }
            return response.data;
        } catch (error) {
            console.error("Error adding bulk items:", error);
            return null;
        }
    };

    const bulkUpdateItems = async (
        listId: number,
        updates: Record<number, Partial<ShoppingListItem>>
    ) => {
        try {
            const response = await axios.put(
                `/api/shopping-lists/${listId}/items/bulk`,
                { updates }
            );
            // Update local items
            for (const itemId of response.data.updated) {
                const itemIndex = items.value.findIndex((item) => item.id === itemId);
                if (itemIndex >= 0 && updates[itemId]) {
                    items.value[itemIndex] = {
                        ...items.value[itemIndex],
                        ...updates[itemId],
                    };
                }
            }
            return response.data;
        } catch (error) {
            console.error("Error updating bulk items:", error);
            return null;
        }
    };

    const addInventoryItem = async (
        listId: number,
        inventoryItemId: number,
        quantity: number
    ) => {
        try {
            const response = await axios.post(
                `/api/shopping-lists/${listId}/items/from-inventory`,
                { inventory_item_id: inventoryItemId, quantity }
            );
            items.value.push(response.data);
            return true;
        } catch (error) {
            console.error("Error adding inventory item to shopping list:", error);
            return false;
        }
    };

    const addStandaloneItem = async (
        listId: number,
        data: Partial<ShoppingListItem>
    ) => {
        try {
            const response = await axios.post(
                `/api/shopping-lists/${listId}/items/standalone`,
                data
            );
            items.value.push(response.data);
            return true;
        } catch (error) {
            console.error("Error adding standalone item to shopping list:", error);
            return false;
        }
    };

    return {
        paginator,
        list,
        items,
        errors,
        loading,
        creating,
        updating,
        deleting,
        loadLists,
        loadList,
        loadItems,
        initList,
        createList,
        updateList,
        deleteList,
        createItem,
        updateItem,
        deleteItem,
        addBulkItems,
        bulkUpdateItems,
        addInventoryItem,
        addStandaloneItem,
    };
});
