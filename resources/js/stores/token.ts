import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { User } from "../types/auth";
import axios, { AxiosError } from "axios";

export const useTokenStore = defineStore("token", () => {
    const tokens = ref<Array<any>>([]);
    const loadTokens = async () => {
        try {
            const response = await axios.get("/api/tokens");
            tokens.value = response.data;
        } catch (error) {
            console.error("Failed to load tokens:", error);
        }
    };

    const newToken = ref({
        name: "",
        abilities: ["*"],
    });
    const storeToken = async () => {
        if (!newToken.value.name.trim()) return;
        try {
            const response = await axios.post("/api/tokens", newToken.value);
            tokens.value.push(response.data.accessToken);
            // TODO - make a modal with an easy copy button
            alert(`New Token Created: ${response.data.plainTextToken}`);
            newToken.value.name = ""; // Reset input after creation
        } catch (error) {
            console.error("Failed to create token:", error);
        }
    };

    const destroyToken = async (tokenId: string) => {
        try {
            await axios.delete(`/api/tokens/${tokenId}`);
            tokens.value = tokens.value.filter((token) => token.id !== tokenId);
        } catch (error) {
            console.error("Failed to destroy token:", error);
        }
    };

    return {
        tokens,
        loadTokens,
        storeToken,
        destroyToken,
        newToken,
    };
});
