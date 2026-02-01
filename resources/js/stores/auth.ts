import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { User } from "../types/auth";

export const useAuthStore = defineStore("auth", () => {
    const user = ref<User | null>(null);
    const isAuthenticated = computed(() => user.value !== null);

    const setUser = (authUser: User | null) => {
        user.value = authUser;
    }

    return {
        user,
        isAuthenticated,
        setUser,
    };
});
