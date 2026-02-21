<script setup lang="ts">
import { useRouter } from "vue-router";
import Content from "../../common/Content.vue";
import Card from "../../common/Card.vue";
import { useStockLocationStore } from "../../../stores/stock-location";
import StockLocationForm from "../../common/StockLocationForm.vue";

const store = useStockLocationStore();

const router = useRouter();

// Initialize form
store.initStockLocation();

// Handle form submission
const submitForm = async () => {
    if (!(await store.createStockLocationItem())) return;
    // Redirect to stock locations list on success
    router.push("/stock-locations");
};
</script>

<template>
    <Content>
        <Card>
            <template #title>Create New Stock Location</template>

            <StockLocationForm @submit-form="submitForm" />
        </Card>
    </Content>
</template>
