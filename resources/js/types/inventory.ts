import { User } from "./auth";

export type InventoryItem = {
    id: number | null;
    name: string;
    sku: string;
    stock_location_id: number | null;
    position: string;
    description: string;
    quantity: number;
    reorder_point: number;
    reorder_quantity: number;
    min_stock_level: number;
    max_stock_level: number;
    unit_price: number;
    unit: string;
    expiration_date: string | null;
    user_id: number;
    created_at: string;
    updated_at: string;
    stock_location?: StockLocation;
    user?: User;
};

export function createInventoryItem(): InventoryItem {
    return {
        id: null,
        name: "",
        sku: "",
        stock_location_id: null,
        position: "",
        description: "",
        quantity: 0,
        reorder_point: 0,
        reorder_quantity: 0,
        min_stock_level: 0,
        max_stock_level: 0,
        unit_price: 0,
        unit: "",
        expiration_date: null,
        user_id: 0,
        created_at: "",
        updated_at: "",
    };
}

export type StockLocation = {
    id: number;
    name: string;
    short_name: string;
    description: string;
    user_id: number;
    created_at: string;
    updated_at: string;
    inventory_items?: InventoryItem[];
    user?: User;
};
export function createStockLocation(): StockLocation {
    return {
        id: 0,
        name: "",
        short_name: "",
        description: "",
        user_id: 0,
        created_at: "",
        updated_at: "",
    };
}