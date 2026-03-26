import apiClient from "./axiosClient";

const route = "/recipes";

export default {
  async getAll() {
    return await apiClient.get(route);
  },

  async getById(id) {
    return await apiClient.get(`${route}/${id}`);
  },

  async create(data) {
    const isForm = data instanceof FormData;
    const payload = isForm ? data : { ...data };
    if (!isForm) {
      delete payload.id;
    }
    return await apiClient.post(route, payload, isForm ? { headers: { "Content-Type": "multipart/form-data" } } : undefined);
  },

  async update(id, data) {
    const isForm = data instanceof FormData;
    const payload = isForm ? data : { ...data };
    if (!isForm) {
      delete payload.id;
    }
    return await apiClient.patch(`${route}/${id}`, payload, isForm ? { headers: { "Content-Type": "multipart/form-data" } } : undefined);
  },

  async delete(id) {
    return await apiClient.delete(`${route}/${id}`);
  },
};
