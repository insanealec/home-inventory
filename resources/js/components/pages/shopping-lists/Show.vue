<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import Content from "../../common/Content.vue";
import Card from "../../common/Card.vue";
import ShoppingListItemList from "../../ShoppingListItemList.vue";
import { useShoppingListStore } from "../../../stores/shopping-list";
import { useShoppingCategoryStore } from "../../../stores/shopping-category";
import { useInventoryStore } from "../../../stores/inventory";

const store = useShoppingListStore();
const categoryStore = useShoppingCategoryStore();
const inventoryStore = useInventoryStore();
const route = useRoute();

const listId = parseInt(route.params.id as string);

const showAddItemForm = ref(false);
const addMode = ref<"freeform" | "inventory">("freeform");

const newItem = ref({
    name: "",
    quantity: 1,
    unit: "",
    category_id: null as number | null,
    estimated_price: null as number | null,
    notes: "",
});

const inventoryAdd = ref({
    inventory_item_id: null as number | null,
    quantity: 1,
});

onMounted(async () => {
    await store.loadList(listId);
    await categoryStore.loadCategories();
    await inventoryStore.loadItems();
});

const resetForms = () => {
    newItem.value = { name: "", quantity: 1, unit: "", category_id: null, estimated_price: null, notes: "" };
    inventoryAdd.value = { inventory_item_id: null, quantity: 1 };
    showAddItemForm.value = false;
};

const addFreeformItem = async () => {
    if (!newItem.value.name.trim()) return;
    const success = await store.createItem(listId, {
        name: newItem.value.name,
        quantity: newItem.value.quantity,
        unit: newItem.value.unit || null,
        category_id: newItem.value.category_id,
        estimated_price: newItem.value.estimated_price,
        notes: newItem.value.notes,
    });
    if (success) { resetForms(); }
};

const addFromInventory = async () => {
    if (!inventoryAdd.value.inventory_item_id) return;
    const success = await store.addInventoryItem(
        listId,
        inventoryAdd.value.inventory_item_id,
        inventoryAdd.value.quantity,
    );
    if (success) { resetForms(); }
};

const handleDeleteItem = () => {
    if (store.list) { store.loadItems(store.list.id); }
};
</script>

<template>
    <Content>
        <div v-if="store.list" class="space-y-4">
            <!-- List Header Card -->
            <Card>
                <template #title>{{ store.list.name }}</template>
                <div class="space-y-2">
                    <div v-if="store.list.notes" class="text-gray-700 dark:text-gray-300">
                        <strong>Notes:</strong> {{ store.list.notes }}
                    </div>
                    <div class="text-gray-700 dark:text-gray-300">
                        <strong>Status:</strong>
                        <span
                            v-if="store.list.is_completed"
                            class="ml-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold"
                        >
                            Completed
                        </span>
                        <span
                            v-else
                            class="ml-2 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold"
                        >
                            Active
                        </span>
                    </div>
                    <div v-if="store.list.shopping_date" class="text-gray-700 dark:text-gray-300">
                        <strong>Shopping Date:</strong> {{ store.list.shopping_date }}
                    </div>
                    <div class="flex gap-2 mt-4">
                        <router-link
                            :to="`/shopping-lists/${listId}/edit`"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700"
                        >
                            Edit List
                        </router-link>
                        <router-link
                            to="/shopping-lists"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md shadow-sm hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
                        >
                            Back to Lists
                        </router-link>
                    </div>
                </div>
            </Card>

            <!-- Items Card -->
            <Card>
                <template #title>Items ({{ store.items.length }})</template>
                <ShoppingListItemList
                    :list-id="listId"
                    :items="store.items"
                    @delete:item="handleDeleteItem"
                />
            </Card>

            <!-- Add Item Card -->
            <Card>
                <template #title>
                    <button
                        @click="showAddItemForm = !showAddItemForm"
                        class="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-400 font-semibold"
                    >
                        {{ showAddItemForm ? "Cancel" : "+ Add Item" }}
                    </button>
                </template>

                <div v-if="showAddItemForm" class="space-y-4">
                    <!-- Mode toggle -->
                    <div class="flex rounded-md border border-gray-300 dark:border-gray-600 overflow-hidden w-fit">
                        <button
                            type="button"
                            @click="addMode = 'freeform'"
                            :class="addMode === 'freeform'
                                ? 'bg-indigo-600 text-white'
                                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                            class="px-4 py-2 text-sm font-medium transition-colors"
                        >
                            Free-form item
                        </button>
                        <button
                            type="button"
                            @click="addMode = 'inventory'"
                            :class="addMode === 'inventory'
                                ? 'bg-indigo-600 text-white'
                                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                            class="px-4 py-2 text-sm font-medium border-l border-gray-300 dark:border-gray-600 transition-colors"
                        >
                            From inventory
                        </button>
                    </div>

                    <!-- Free-form form -->
                    <form v-if="addMode === 'freeform'" @submit.prevent="addFreeformItem" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Item Name *
                            </label>
                            <input
                                v-model="newItem.name"
                                type="text"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Quantity *
                                </label>
                                <input
                                    v-model.number="newItem.quantity"
                                    type="number"
                                    min="1"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Unit
                                </label>
                                <input
                                    v-model="newItem.unit"
                                    type="text"
                                    placeholder="e.g., kg, liters"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Category
                                </label>
                                <select
                                    v-model.number="newItem.category_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                                    <option :value="null">None</option>
                                    <option v-for="cat in categoryStore.categories" :key="cat.id" :value="cat.id">
                                        {{ cat.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Est. Price
                                </label>
                                <input
                                    v-model.number="newItem.estimated_price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Notes
                            </label>
                            <textarea
                                v-model="newItem.notes"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                rows="2"
                            />
                        </div>

                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700">
                            Add Item
                        </button>
                    </form>

                    <!-- From inventory form -->
                    <form v-else @submit.prevent="addFromInventory" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Inventory Item *
                            </label>
                            <select
                                v-model.number="inventoryAdd.inventory_item_id"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                <option :value="null">— Select an item —</option>
                                <option
                                    v-for="item in inventoryStore.paginator.data"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.name }}{{ item.unit ? ` (${item.unit})` : '' }}{{ item.stock_location_id ? ` · ${item.quantity} in stock` : '' }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Quantity *
                            </label>
                            <input
                                v-model.number="inventoryAdd.quantity"
                                type="number"
                                min="1"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            />
                        </div>

                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700">
                            Add to List
                        </button>
                    </form>
                </div>
            </Card>
        </div>
        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
            Loading shopping list...
        </div>
    </Content>
</template>
