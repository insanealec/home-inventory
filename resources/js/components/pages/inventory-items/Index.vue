<script setup lang="ts">
import { ref } from "vue";
import Content from "../../common/Content.vue";
import Card from "../../common/Card.vue";
import LoadItems from "../../../actions/App/Actions/InventoryItem/LoadItems";
import axios from "axios";
import { Pagination } from "../../../types/common";

const paginator = ref({} as Pagination);

const loadItems = async () => {
    try {
        const response = await axios.get(LoadItems.url());
        paginator.value = response.data;
        console.log("Inventory items loaded:", paginator.value);
    } catch (error) {
        console.error("Error loading inventory items:", error);
    }
};
loadItems();
</script>

<template>
    <Content>
        <Card>
            <template #title>Inventory Items</template>
        </Card>
    </Content>
</template>
