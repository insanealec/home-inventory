import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { User } from "../types/auth";
import axios, { AxiosError } from "axios";

export const useAuthStore = defineStore("auth", () => {
    const user = ref<User | null>(null);
    const isAuthenticated = computed(() => user.value !== null);
    const setUser = (authUser: User | null) => {
        user.value = authUser;
    };

    const loginForm = ref({
        email: "",
        password: "",
    });
    const login = async () => {
        try {
            const response = await axios.post("/login", loginForm.value);
            console.log(response);
            if (response.status === 200) {
                redirectToDashboard();
            }
        } catch (error) {
            handleErrors(error);
        }
    };

    const registerForm = ref({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
    });
    const register = async () => {
        try {
            const response = await axios.post("/register", registerForm.value);
            console.log(response);
            if (response.status === 201) {
                redirectToDashboard();
            }
        } catch (error) {
            handleErrors(error);
        }
    };

    const logout = async () => {
        try {
            await axios.post("/logout", {});
            redirectToHome();
        } catch (error) {
            console.error("Logout failed:", error);
        }
    };

    const errors = ref({});
    const handleErrors = (error: any) => {
        const axiosError = error as AxiosError<{
            message: string;
            errors: Record<string, string[]>;
        }>;
        if (axiosError.response && axiosError.response.status === 422) {
            console.error(
                axiosError?.response?.data?.message || "Validation error",
            );
            errors.value = axiosError?.response?.data?.errors ?? [];
        }
    };

    const redirectToHome = () => {
        window.location.href = "/";
    };
    const redirectToDashboard = () => {
        window.location.href = "/dashboard";
    };

    return {
        user,
        isAuthenticated,
        setUser,
        loginForm,
        login,
        registerForm,
        register,
        logout,
        errors,
    };
});
