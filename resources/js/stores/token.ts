import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { User } from "../types/auth";
import axios, { AxiosError } from "axios";

export const useTokenStore = defineStore("token", () => {
    const tokens = ref<Array<any>>([]);
    const loadTokens = async () => {
        try {
            const response = await axios.get("/tokens");
            tokens.value = response.data;
        } catch (error) {
            console.error("Failed to load tokens:", error);
        }
    };

    const newToken = ref({
        name: "",
        abilities: ['*'],
    });
    const storeToken = async () => {
        try {
            const response = await axios.post("/tokens/create", newToken.value);
            tokens.value.push(response.data);
        } catch (error) {
            console.error("Failed to create token:", error);
        }
    };

    const destroyToken = async (tokenId: string) => {
        try {
            await axios.post("/tokens/destroy", { token_id: tokenId });
            tokens.value = tokens.value.filter(token => token.id !== tokenId);
        } catch (error) {
            console.error("Failed to destroy token:", error);
        }
    };

    return {
        tokens,
        loadTokens,
        storeToken,
        destroyToken,
    };
});