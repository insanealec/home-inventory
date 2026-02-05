import { User } from "./auth";
import { InventoryItem } from "./inventory";

export type ShoppingList = {
    id: number;
    name: string;
    notes: string;
    is_completed: boolean;
    shopping_date: string | null;
    user_id: number;
    created_at: string;
    updated_at: string;
    user?: User;
    items?: ShoppingListItem[];
};

export type ShoppingListItem = {
    id: number;
    shopping_list_id: number;
    name: string;
    quantity: number;
    unit: string;
    is_completed: boolean;
    category_id: number | null;
    notes: string;
    estimated_price: number | null;
    priority: number;
    inventory_item_id: number | null;
    sort_order: number;
    created_at: string;
    updated_at: string;
    shopping_list?: ShoppingList;
    category?: ShoppingCategory;
    inventory_item?: InventoryItem;
};

export type ShoppingCategory = {
    id: number;
    name: string;
    store_section: string;
    color: string;
    sort_order: number;
    user_id: number;
    created_at: string;
    updated_at: string;
    user?: User;
};
