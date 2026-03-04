import { defineStore } from "pinia";

export const useGlobalLoadingStore = defineStore("globalLoading", {
  state: () => ({
    pendingRequests: 0,
    routeLoading: false,
  }),
  getters: {
    isLoading(state) {
      return state.routeLoading || state.pendingRequests > 0;
    },
  },
  actions: {
    startRequest() {
      this.pendingRequests += 1;
    },
    finishRequest() {
      this.pendingRequests = Math.max(0, this.pendingRequests - 1);
    },
    setRouteLoading(value) {
      this.routeLoading = !!value;
    },
    reset() {
      this.pendingRequests = 0;
      this.routeLoading = false;
    },
  },
});
