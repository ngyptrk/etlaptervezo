import { describe, expect, it } from "vitest";
import { mount } from "@vue/test-utils";
import FormRecipe from "@/components/Forms/FormRecipe.vue";

const modalStub = {
  template: '<div class="modal-stub"><slot /></div>',
  methods: {
    show() {},
    hide() {},
  },
};

const props = {
  title: "Uj recept",
  item: {
    id: 0,
    name: "Leves",
    description: "Finom",
    picture: "",
    person: 2,
    meal_id: 1,
  },
  meals: [{ id: 1, meal: "Ebed" }],
  ingredients: [],
  rawIngredients: [{ id: 10, raw_ingredient: "Repa" }],
  units: [{ id: 20, unit: "kg" }],
};

describe("FormRecipe", () => {
  it("renders the main recipe fields and meal options", () => {
    const wrapper = mount(FormRecipe, {
      props,
      global: { stubs: { Modal: modalStub } },
    });

    expect(wrapper.get("#name").element.value).toBe("Leves");
    expect(wrapper.get("#description").element.value).toBe("Finom");
    expect(wrapper.text()).toContain("Ebed");
  });

  it("emits the recipe item with the selected picture file on save", async () => {
    const wrapper = mount(FormRecipe, {
      props,
      global: { stubs: { Modal: modalStub } },
    });
    const file = new File(["png"], "recept.png", { type: "image/png" });
    const done = () => {};

    Object.defineProperty(wrapper.get("#picture").element, "files", {
      value: [file],
    });

    await wrapper.get("#picture").trigger("change");
    wrapper.vm.yesEventHandler(done);

    expect(wrapper.emitted("yesEventForm")).toHaveLength(1);
    expect(wrapper.emitted("yesEventForm")[0][0].item.pictureFile).toBe(file);
    expect(wrapper.emitted("yesEventForm")[0][0].done).toBe(done);
  });

  it("shows server errors in normal flow under fields", async () => {
    const wrapper = mount(FormRecipe, {
      props,
      global: { stubs: { Modal: modalStub } },
    });

    wrapper.vm.setServerErrors({ name: ["A recept neve kotelezo."] });
    await wrapper.vm.$nextTick();

    const error = wrapper.findAll(".recipe-error").find((node) => node.text() === "A recept neve kotelezo.");

    expect(error.exists()).toBe(true);
    expect(error.classes()).not.toContain("position-absolute");
  });
});
