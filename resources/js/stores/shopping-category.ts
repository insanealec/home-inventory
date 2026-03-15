import { defineStore } from "pinia";
import { ref } from "vue";
import axios from "axios";
import { ShoppingCategory } from "../types/shopping-list";

export const useShoppingCategoryStore = defineStore("shoppingCategory", () => {
    const categories = ref<ShoppingCategory[]>([]);
    const errors = ref<Record<string, string>>({});
    const loading = ref(false);
    const creating = ref(false);
    const updating = ref(false);
    const deleting = ref(false);

    const loadCategories = async () => {
        try {
            loading.value = true;
            const response = await axios.get("/api/shopping-categories");
            categories.value = response.data;
        } catch (error) {
            console.error("Error loading shopping categories:", error);
        } finally {
            loading.value = false;
        }
    };

    const createCategory = async (data: Partial<ShoppingCategory>) => {
        try {
            creating.value = true;
            errors.value = {};
            const response = await axios.post("/api/shopping-categories", data);
            categories.value.push(response.data);
            return true;
        } catch (error: any) {
            if (error.response?.data.errors) {
                errors.value = error.response.data.errors;
            }
            console.error("Error creating shopping category:", error);
            return false;
        } finally {
            creating.value = false;
        }
    };

    const updateCategory = async (id: number, data: Partial<ShoppingCategory>) => {
        try {
            updating.value = true;
            const response = await axios.put(`/api/shopping-categories/${id}`, data);
            const index = categories.value.findIndex((cat) => cat.id === id);
            if (index >= 0) {
                categories.value[index] = response.data;
            }
            return true;
        } catch (error) {
            console.error("Error updating shopping category:", error);
            return false;
        } finally {
            updating.value = false;
        }
    };

    const deleteCategory = async (id: number) => {
        try {
            deleting.value = true;
            await axios.delete(`/api/shopping-categories/${id}`);
            categories.value = categories.value.filter((cat) => cat.id !== id);
            return true;
        } catch (error) {
            console.error("Error deleting shopping category:", error);
            return false;
        } finally {
            deleting.value = false;
        }
    };

    return {
        categories,
        errors,
        loading,
        creating,
        updating,
        deleting,
        loadCategories,
        createCategory,
        updateCategory,
        deleteCategory,
    };
});
