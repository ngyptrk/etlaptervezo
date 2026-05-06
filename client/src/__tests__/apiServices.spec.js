import { beforeEach, describe, expect, it, vi } from "vitest";
import mealRequirementService from "@/api/mealRequirementService";
import recipeService from "@/api/recipeService";
import apiClient from "@/api/axiosClient";

vi.mock("@/api/axiosClient", () => ({
  default: {
    get: vi.fn(),
    post: vi.fn(),
    patch: vi.fn(),
    delete: vi.fn(),
  },
}));

describe("api services", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("calls meal requirement endpoints with the expected methods", async () => {
    await mealRequirementService.getAll();
    expect(apiClient.get).toHaveBeenCalledWith("/mealrequirements");

    await mealRequirementService.getById(4);
    expect(apiClient.get).toHaveBeenCalledWith("/mealrequirements/4");

    await mealRequirementService.delete(7);
    expect(apiClient.delete).toHaveBeenCalledWith("/mealrequirements/7");
  });

  it("removes id from meal requirement create and update payloads", async () => {
    await mealRequirementService.create({ id: 12, meal_id: 2, meal_of_day_id: 1 });
    expect(apiClient.post).toHaveBeenCalledWith("/mealrequirements", {
      meal_id: 2,
      meal_of_day_id: 1,
    });

    await mealRequirementService.update(12, { id: 12, meal_id: 3, meal_of_day_id: 2 });
    expect(apiClient.patch).toHaveBeenCalledWith("/mealrequirements/12", {
      meal_id: 3,
      meal_of_day_id: 2,
    });
  });

  it("sends recipe FormData as multipart data", async () => {
    const formData = new FormData();
    formData.append("name", "Palacsinta");

    await recipeService.create(formData);
    await recipeService.update(5, formData);

    expect(apiClient.post).toHaveBeenCalledWith("/recipes", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    expect(apiClient.patch).toHaveBeenCalledWith("/recipes/5", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
  });

  it("removes id from plain recipe payloads", async () => {
    await recipeService.create({ id: 1, name: "Leves" });
    await recipeService.update(1, { id: 1, name: "Foetel" });

    expect(apiClient.post).toHaveBeenCalledWith("/recipes", { name: "Leves" }, undefined);
    expect(apiClient.patch).toHaveBeenCalledWith("/recipes/1", { name: "Foetel" }, undefined);
  });
});
