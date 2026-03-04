import apiClient from "./axiosClient";

const route = "/weeklyfood";

export default {
  async generate(payload) {
    return await apiClient.post(`${route}/generate`, payload);
  },

  async getMyPlan(week = null) {
    const query = week ? `?week=${week}` : "";
    return await apiClient.get(`${route}/my-plan${query}`);
  },
};
