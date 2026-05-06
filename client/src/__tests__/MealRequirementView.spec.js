import { beforeEach, describe, expect, it, vi } from "vitest";
import { createPinia, setActivePinia } from "pinia";
import { flushPromises, mount } from "@vue/test-utils";
import MealRequirementView from "@/views/MealRequirementView.vue";
import mealRequirementService from "@/api/mealRequirementService";
import mealOfDayService from "@/api/mealOfDayService";
import mealService from "@/api/mealService";

vi.mock("@/api/mealRequirementService", () => ({
  default: {
    getAll: vi.fn(),
    create: vi.fn(),
    update: vi.fn(),
    delete: vi.fn(),
  },
}));

vi.mock("@/api/mealOfDayService", () => ({
  default: {
    getAll: vi.fn(),
  },
}));

vi.mock("@/api/mealService", () => ({
  default: {
    getAll: vi.fn(),
  },
}));

const formStub = {
  template: '<div class="form-stub"></div>',
  methods: {
    show: vi.fn(),
    setServerErrors(errors) {
      setServerErrorsMock(errors);
    },
  },
};

const setServerErrorsMock = vi.fn();
let pinia;

const confirmStub = {
  props: ["isOpenConfirmModal", "title", "message"],
  emits: ["cancel", "confirm"],
  template: `
    <div v-if="isOpenConfirmModal" class="confirm-stub">
      <h2>{{ title }}</h2>
      <p>{{ message }}</p>
      <button class="confirm-yes" @click="$emit('confirm')">Igen</button>
    </div>
  `,
};

function mountView() {
  return mount(MealRequirementView, {
    global: {
      plugins: [pinia],
      stubs: {
        FormMealRequirement: formStub,
        ConfirmModal: confirmStub,
      },
    },
  });
}

describe("MealRequirementView", () => {
  beforeEach(() => {
    vi.clearAllMocks();
    setServerErrorsMock.mockClear();
    pinia = createPinia();
    setActivePinia(pinia);
    mealRequirementService.getAll.mockResolvedValue({
      data: [
        { id: 1, meal_of_day_id: 1, meal_id: 10 },
        { id: 2, meal_of_day_id: 2, meal_id: 20 },
      ],
    });
    mealOfDayService.getAll.mockResolvedValue({
      data: [
        { id: 1, meal_of_day: "Reggel" },
        { id: 2, meal_of_day: "Del" },
      ],
    });
    mealService.getAll.mockResolvedValue({
      data: [
        { id: 10, meal: "Leves" },
        { id: 20, meal: "Foetel" },
      ],
    });
  });

  it("loads and renders meal requirements", async () => {
    const wrapper = mountView();
    await flushPromises();

    expect(mealRequirementService.getAll).toHaveBeenCalled();
    expect(mealOfDayService.getAll).toHaveBeenCalled();
    expect(mealService.getAll).toHaveBeenCalled();
    expect(wrapper.text()).toContain("Reggel");
    expect(wrapper.text()).toContain("Leves");
    expect(wrapper.text()).toContain("Del");
    expect(wrapper.text()).toContain("Foetel");
  });

  it("filters rows by search input", async () => {
    const wrapper = mountView();
    await flushPromises();

    await wrapper.get(".search-input").setValue("foetel");

    expect(wrapper.text()).not.toContain("Leves");
    expect(wrapper.text()).toContain("Foetel");
  });

  it("opens the delete confirmation and deletes after confirm", async () => {
    mealRequirementService.delete.mockResolvedValue({});
    const wrapper = mountView();
    await flushPromises();

    await wrapper.findAll(".btn-outline-danger")[0].trigger("click");
    expect(wrapper.text()).toContain("Törlés megerősítése");

    await wrapper.get(".confirm-yes").trigger("click");
    await flushPromises();

    expect(mealRequirementService.delete).toHaveBeenCalledWith(1);
  });

  it("passes validation errors to the child form on save failure", async () => {
    mealRequirementService.create.mockRejectedValue({
      response: {
        status: 422,
        data: { errors: { meal_id: ["Etel tipus kotelezo"] } },
      },
    });
    const wrapper = mountView();
    await flushPromises();
    const done = vi.fn();

    await wrapper.vm.yesEventFormHandler({
      item: { id: 0, meal_of_day_id: 1, meal_id: 0 },
      done,
    });

    expect(setServerErrorsMock).toHaveBeenCalledWith({
      meal_id: ["Etel tipus kotelezo"],
    });
    expect(done).toHaveBeenCalledWith(false);
  });
});
