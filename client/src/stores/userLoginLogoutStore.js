import { defineStore } from "pinia";
import service from "@/api/userLoginLogoutService";
import { useToastStore } from "./toastStore";

export const useUserLoginLogoutStore = defineStore("userLoginLogout", {
  state: () => ({
    item: JSON.parse(localStorage.getItem("user_data")) || null,
    loading: false,
    error: null,
    rolNames: ["Admin", "Felhasználó"],
  }),
  getters: {
    token() {
      return this.item ? this.item.token : null;
    },
    role() {
      return this.item ? this.item.role : null;
    },
    userName() {
      return this.item ? this.item.name : null;
    },
    userNameWithRole() {
      if (!this.item) return null;
      if (this.item.role === 1) return "Admin";
      return "Felhasználó";
    },
    isLoggedIn() {
      return this.item != null;
    },
  },
  actions: {
    getUserName() {
      return this.item ? this.item.name : null;
    },
    canAccess(requiredRoles) {
      if (!requiredRoles || requiredRoles.length === 0) return true;
      if (!this.isLoggedIn) return false;
      return requiredRoles.includes(this.role);
    },
    async login(data) {
      try {
        this.loading = true;
        this.error = null;
        const response = await service.login(data);
        this.item = response.data;
        if (this.item && (this.item.role == null || this.item.role === 3)) {
          this.item.role = 2;
        }
        localStorage.setItem("user_data", JSON.stringify(this.item));
        return true;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async logout() {
      try {
        this.error = null;
        this.loading = true;
        await service.logout();
        this.item = null;
        localStorage.removeItem("user_data");
        const toastStore = useToastStore();
        toastStore.messages.push("Sikeres kijelentkezés");
        toastStore.show("Success");
        return true;
      } catch (err) {
        this.error = err;
        this.item = null;
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async getMeRefresh() {
      try {
        this.error = null;
        this.loading = true;
        const response = await service.getMeRefresh();
        const me = response.data?.data ?? response.data ?? {};

        if (!this.item) {
          this.item = {};
        }

        this.item.name = me.name ?? this.item.name;
        this.item.email = me.email ?? this.item.email;
        const resolvedRole = me.role ?? this.item.role ?? 2;
        this.item.role = resolvedRole === 3 ? 2 : resolvedRole;
        localStorage.setItem("user_data", JSON.stringify(this.item));
        return true;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async updateSelf(payload) {
      try {
        this.error = null;
        this.loading = true;
        const response = await service.updateSelf(payload);
        const me = response.data?.data?.data ?? response.data?.data ?? response.data ?? {};

        if (!this.item) {
          this.item = {};
        }

        this.item.name = me.name ?? this.item.name;
        this.item.email = me.email ?? this.item.email;
        const resolvedRole = me.role ?? this.item.role ?? 2;
        this.item.role = resolvedRole === 3 ? 2 : resolvedRole;
        localStorage.setItem("user_data", JSON.stringify(this.item));
        return true;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
  },
});
