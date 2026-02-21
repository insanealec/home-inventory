import { defineStore } from "pinia";
import { ref } from "vue";
import axios from "axios";
import { createStockLocation, type StockLocation } from "../types/inventory";
import { Pagination } from "../types/common";
import CreateStockLocation from "../actions/App/Actions/StockLocation/CreateStockLocation";
import LoadStockLocations from "../actions/App/Actions/StockLocation/LoadStockLocations";
import LoadStockLocation from "../actions/App/Actions/StockLocation/LoadStockLocation";
import UpdateStockLocation from "../actions/App/Actions/StockLocation/UpdateStockLocation";
import DeleteStockLocation from "../actions/App/Actions/StockLocation/DeleteStockLocation";

export const useStockLocationStore = defineStore("stockLocation", () => {
    const paginator = ref<Pagination<StockLocation>>({
        data: [],
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
        links: [],
    } as Pagination<StockLocation>);
    const stockLocation = ref<StockLocation | null>(null);
    const errors = ref<Record<string, string>>({});
    const loading = ref(false);
    const creating = ref(false);
    const updating = ref(false);
    const deleting = ref(false);

    const loadStockLocations = async (
        page: number = 1,
        sortField: string | null = null,
        sortDirection: "asc" | "desc" = "asc",
    ) => {
        try {
            loading.value = true;
            const params: any = { page };
            if (sortField) {
                params.sortBy = sortField;
                params.sortDirection = sortDirection;
            }
            const response = await axios.get(LoadStockLocations.url(), { params });
            paginator.value = response.data;
        } catch (error) {
            console.error("Error loading stock locations:", error);
        } finally {
            loading.value = false;
        }
    };

    const loadStockLocation = async (id: number) => {
        try {
            loading.value = true;
            const response = await axios.get(LoadStockLocation.url(id));
            stockLocation.value = response.data;
        } catch (error) {
            console.error("Error loading stock location:", error);
        } finally {
            loading.value = false;
        }
    };

    const initStockLocation = () => {
        stockLocation.value = createStockLocation();
    };

    const createStockLocationItem = async () => {
        try {
            creating.value = true;
            // Reset errors
            errors.value = {};
            const response = await axios.post(CreateStockLocation.url(), stockLocation.value);
            return true;
        } catch (error: any) {
            if (error.response && error.response.data.errors) {
                // Handle validation errors
                errors.value = error.response.data.errors;
            }
            console.error("Error creating stock location:", error);
            return false;
        } finally {
            creating.value = false;
        }
    };

    const updateStockLocationItem = async () => {
        if (!stockLocation.value?.id) return false;
        try {
            updating.value = true;
            const response = await axios.put(UpdateStockLocation.url(stockLocation.value.id), stockLocation.value);
            stockLocation.value = response.data;
            return true;
        } catch (error) {
            console.error("Error updating stock location:", error);
            return false;
        } finally {
            updating.value = false;
        }
    };

    const deleteStockLocationItem = async (id: number) => {
        try {
            deleting.value = true;
            await axios.delete(DeleteStockLocation.url(id));
            return true;
        } catch (error) {
            console.error("Error deleting stock location:", error);
            return false;
        } finally {
            deleting.value = false;
        }
    };

    return {
        paginator,
        stockLocation,
        errors,
        loading,
        creating,
        updating,
        deleting,
        loadStockLocations,
        loadStockLocation,
        initStockLocation,
        createStockLocationItem,
        updateStockLocationItem,
        deleteStockLocationItem,
    };
});
