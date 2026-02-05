import { User } from "./auth";

export type InventoryItem = {
    id: number;
    name: string;
    sku: string;
    stock_location_id: number;
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
