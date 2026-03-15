<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import Content from "../../common/Content.vue";
import Card from "../../common/Card.vue";
import { useShoppingListStore } from "../../../stores/shopping-list";

const store = useShoppingListStore();
const router = useRouter();

store.initList();

const form = ref({
    name: "",
    notes: "",
    shopping_date: "",
});

const submitForm = async () => {
    if (!form.value.name.trim()) {
        alert("Please enter a shopping list name");
        return;
    }

    const success = await store.createList({
        name: form.value.name,
        notes: form.value.notes,
        shopping_date: form.value.shopping_date || null,
    });

    if (success) {
        router.push("/shopping-lists");
    }
};
</script>

<template>
    <Content>
        <Card>
            <template #title>Create New Shopping List</template>

            <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Shopping List Name *
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="e.g., Weekly Groceries"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Notes
                    </label>
                    <textarea
                        v-model="form.notes"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        rows="3"
                        placeholder="Optional notes about this shopping list"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Shopping Date
                    </label>
                    <input
                        v-model="form.shopping_date"
                        type="date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                </div>

                <div class="flex gap-4">
                    <button
                        type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        Create Shopping List
                    </button>
                    <router-link
                        to="/shopping-lists"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md shadow-sm hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
                    >
                        Cancel
                    </router-link>
                </div>
            </form>
        </Card>
    </Content>
</template>
