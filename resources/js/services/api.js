// API service to handle all HTTP requests to the Laravel backend
class ApiService {
    constructor() {
        // Base URL for the API (adjust if needed)
        this.baseURL = "/api";
        // Set up default headers
        this.headers = {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        };

        // Add CSRF token if available
        const token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            this.headers["X-CSRF-TOKEN"] = token.content;
        }
    }

    // Generic GET request
    async get(endpoint, params = {}) {
        const url = new URL(`${this.baseURL}${endpoint}`);
        Object.keys(params).forEach((key) =>
            url.searchParams.append(key, params[key]),
        );

        const response = await fetch(url, {
            method: "GET",
            headers: this.headers,
        });

        return this.handleResponse(response);
    }

    // Generic POST request
    async post(endpoint, data = {}) {
        const response = await fetch(`${this.baseURL}${endpoint}`, {
            method: "POST",
            headers: this.headers,
            body: JSON.stringify(data),
        });

        return this.handleResponse(response);
    }

    // Generic PUT request
    async put(endpoint, data = {}) {
        const response = await fetch(`${this.baseURL}${endpoint}`, {
            method: "PUT",
            headers: this.headers,
            body: JSON.stringify(data),
        });

        return this.handleResponse(response);
    }

    // Generic DELETE request
    async delete(endpoint) {
        const response = await fetch(`${this.baseURL}${endpoint}`, {
            method: "DELETE",
            headers: this.headers,
        });

        return this.handleResponse(response);
    }

    // Handle API response
    async handleResponse(response) {
        if (!response.ok) {
            const error = await response.json();
            throw new Error(
                error.message || `HTTP error! status: ${response.status}`,
            );
        }

        return response.json();
    }
}

// Create a singleton instance
const apiService = new ApiService();

export default apiService;
