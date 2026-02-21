<script setup lang="ts">
import { ref } from "vue";
import { useRouter, useRoute } from "vue-router";
import Content from "../../common/Content.vue";
import Card from "../../common/Card.vue";
import { useStockLocationStore } from "../../../stores/stock-location";
import StockLocationForm from "../../common/StockLocationForm.vue";

const store = useStockLocationStore();

const router = useRouter();
const route = useRoute();

// Load location data for editing
store.loadStockLocation(parseInt(route.params.id as string));

// Handle form submission
const submitForm = async () => {
    const success = await store.updateStockLocationItem();
    if (!success) return;
    // Redirect to stock location detail page on success
    router.push(`/stock-locations/${store?.stockLocation?.id}`);
};
</script>

<template>
    <Content>
        <Card>
            <template #title>Edit Stock Location</template>

            <StockLocationForm @submit-form="submitForm" />
        </Card>
    </Content>
</template>
