import { describe, expect, it } from "vitest";
import { mount } from "@vue/test-utils";
import FormMealRequirement from "@/components/Forms/FormMealRequirement.vue";

const modalStub = {
  template: '<div class="modal-stub"><slot /></div>',
  methods: {
    show() {},
    hide() {},
  },
};

const defaultProps = {
  title: "Uj etkezes elvaras",
  item: { id: 0, meal_of_day_id: 1, meal_id: 2 },
  mealOfDays: [
    { id: 1, meal_of_day: "Reggel" },
    { id: 2, meal_of_day: "Del" },
  ],
  meals: [
    { id: 2, meal: "Leves" },
    { id: 3, meal: "Foetel" },
  ],
};

describe("FormMealRequirement", () => {
  it("renders select options from props", () => {
    const wrapper = mount(FormMealRequirement, {
      props: defaultProps,
      global: {
        stubs: {
          Modal: modalStub,
        },
      },
    });

    expect(wrapper.text()).toContain("Reggel");
    expect(wrapper.text()).toContain("Del");
    expect(wrapper.text()).toContain("Leves");
    expect(wrapper.text()).toContain("Foetel");
  });

  it("updates local form values from the select fields", async () => {
    const wrapper = mount(FormMealRequirement, {
      props: defaultProps,
      global: {
        stubs: {
          Modal: modalStub,
        },
      },
    });

    await wrapper.get("#meal_of_day_id").setValue("2");
    await wrapper.get("#meal_id").setValue("3");

    expect(wrapper.vm.formItem.meal_of_day_id).toBe(2);
    expect(wrapper.vm.formItem.meal_id).toBe(3);
  });

  it("emits the filled form item on save", () => {
    const wrapper = mount(FormMealRequirement, {
      props: defaultProps,
      global: {
        stubs: {
          Modal: modalStub,
        },
      },
    });
    const done = () => {};

    wrapper.vm.yesEventHandler(done);

    expect(wrapper.emitted("yesEventForm")).toHaveLength(1);
    expect(wrapper.emitted("yesEventForm")[0][0]).toEqual({
      item: defaultProps.item,
      done,
    });
  });

  it("shows and clears server errors", async () => {
    const wrapper = mount(FormMealRequirement, {
      props: defaultProps,
      global: {
        stubs: {
          Modal: modalStub,
        },
      },
    });

    wrapper.vm.setServerErrors({
      meal_id: ["Etel tipus kotelezo"],
    });
    await wrapper.vm.$nextTick();

    expect(wrapper.text()).toContain("Etel tipus kotelezo");

    wrapper.vm.clearError("meal_id");
    await wrapper.vm.$nextTick();

    expect(wrapper.text()).not.toContain("Etel tipus kotelezo");
  });
});
