import axios from 'axios';
import JwtService from "@/core/services/JwtService";
import { useTahunStore } from "@/stores/tahun";
import { formDataToObject } from "./utils";

const instance = axios.create({
    baseURL: import.meta.env.VITE_APP_API_URL,
    withCredentials: true,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json'
    },
    transformRequest: [
        (data) => {
            const store = useTahunStore();
            if (data instanceof FormData) {
                if (formDataToObject(data).tahun) return data;

                data.append("tahun", store.tahun);
                return data;
            } else {
                if (data?.tahun) return data;

                return {
                    ...data,
                    tahun: store.tahun,
                };
            }
        },
        ...axios.defaults.transformRequest,
    ],
});

// Add request interceptor to add CSRF token
instance.interceptors.request.use(config => {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token.content;
    }
    config.headers["Authorization"] = "Bearer " + JwtService.getToken();
    config.headers["Accept"] = "application/json";
    return config;
});

instance.interceptors.response.use((response) => {
    if (response.data == null) {
        return Promise.reject({
            error: "Error",
            message: "Error",
        });
    }

    if (response.data.code == "0") {
        return Promise.reject({
            error: "Error",
            message: response.data.msg,
        });
    }

    return response;
});

export default instance;
