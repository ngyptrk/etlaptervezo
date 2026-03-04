import apiClient from "./axiosClient";

const route = "/meals";

export default {
  async getAll() {
    return await apiClient.get(route);
  },

  async getById(id) {
    return await apiClient.get(`${route}/${id}`);
  },

  async create(data) {
    const payload = { ...data };
    delete payload.id;
    return await apiClient.post(route, payload);
  },

  async update(id, data) {
    const payload = { ...data };
    delete payload.id;
    return await apiClient.patch(`${route}/${id}`, payload);
  },

  async delete(id) {
    return await apiClient.delete(`${route}/${id}`);
  },
};
