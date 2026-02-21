<script setup lang="ts">
import { ref } from "vue";
import { useRouter, useRoute } from "vue-router";
import Content from "../../common/Content.vue";
import Card from "../../common/Card.vue";
import { useInventoryStore } from "../../../stores/inventory";
import ItemForm from "../../common/ItemForm.vue";

const store = useInventoryStore();

const router = useRouter();
const route = useRoute();

// Load item data for editing
store.loadItem(parseInt(route.params.id as string));

// Temp Stock locations for dropdown
const stockLocations = ref([
    { id: 1, name: "Basement" },
    { id: 2, name: "Garage" },
    { id: 3, name: "Living Room" },
    { id: 4, name: "Kitchen" },
]);

// Handle form submission
const submitForm = async () => {
    const success = await store.updateItem();
    if (!success) return;
    // Redirect to inventory items list on success
    router.push(`/inventory/${store?.item?.id}`);
};
</script>

<template>
    <Content>
        <Card>
            <template #title>Edit Inventory Item</template>

            <ItemForm @submit-form="submitForm" />
        </Card>
    </Content>
</template>
