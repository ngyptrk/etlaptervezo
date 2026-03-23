import apiClient from "./axiosClient";

const route = "/favorite-recipes";

export default {
  async getAll() {
    return await apiClient.get(route, { meta: { skipGlobalLoading: true } });
  },

  async add(recipeId) {
    return await apiClient.post(route, { recipe_id: recipeId }, { meta: { skipGlobalLoading: true } });
  },

  async remove(recipeId) {
    return await apiClient.delete(`${route}/${recipeId}`, { meta: { skipGlobalLoading: true } });
  },
};
