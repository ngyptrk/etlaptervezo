import axios from "axios";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import { useToastStore } from "@/stores/toastStore";
import { useGlobalLoadingStore } from "@/stores/globalLoadingStore";

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

apiClient.interceptors.request.use(
  (config) => {
    const loadingStore = useGlobalLoadingStore();
    if (!config?.meta?.skipGlobalLoading) {
      loadingStore.startRequest();
    }

    const token = useUserLoginLogoutStore().token;
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error),
);

apiClient.interceptors.response.use(
  (response) => {
    const loadingStore = useGlobalLoadingStore();
    if (!response.config?.meta?.skipGlobalLoading) {
      loadingStore.finishRequest();
    }
    return response.data;
  },
  (error) => {
    const loadingStore = useGlobalLoadingStore();
    if (!error.config?.meta?.skipGlobalLoading) {
      loadingStore.finishRequest();
    }

    const toastStore = useToastStore();

    if (error.response) {
      const status = error.response.status;
      let message = error.response.data.message || "Hiba történt";

      if (status === 422) {
        toastStore.messages.push(message);
        toastStore.show("Error");
        return Promise.reject(error);
      }

      if (status === 401) {
        toastStore.messages.push(message);
        toastStore.show("Error");
        return Promise.reject(error);
      }

      if (status === 500) {
        if (message.includes("1451")) {
          message = "A sor nem törölhető, mert már szerepel egy másik táblában!";
        } else {
          message = "Szerver oldali hiba történt a művelet során.";
        }
        error.response.data.message = message;
      }

      toastStore.messages.push(message);
      toastStore.show("Error");
    } else {
      toastStore.messages.push("A szerver nem elérhető.");
      toastStore.show("Error");
    }

    return Promise.reject(error);
  },
);

export default apiClient;
