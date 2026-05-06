import { describe, expect, it } from "vitest";
import { mount } from "@vue/test-utils";
import PasswordField from "@/components/User/PasswordField.vue";

describe("PasswordField", () => {
  it("renders a password input with the given label and placeholder", () => {
    const wrapper = mount(PasswordField, {
      props: {
        modelValue: "",
        label: "Jelszo",
        labelId: "password",
        placeholder: "Add meg a jelszot",
      },
    });

    const input = wrapper.get("input");

    expect(wrapper.text()).toContain("Jelszo");
    expect(input.attributes("id")).toBe("password");
    expect(input.attributes("placeholder")).toBe("Add meg a jelszot");
    expect(input.attributes("type")).toBe("password");
  });

  it("emits v-model updates when the user types", async () => {
    const wrapper = mount(PasswordField, {
      props: {
        modelValue: "",
      },
    });

    await wrapper.get("input").setValue("titok123");

    expect(wrapper.emitted("update:modelValue")).toEqual([[ "titok123" ]]);
  });

  it("toggles between hidden and visible password text", async () => {
    const wrapper = mount(PasswordField, {
      props: {
        modelValue: "titok123",
      },
    });

    const input = wrapper.get("input");
    expect(input.attributes("type")).toBe("password");

    await wrapper.get("button.toggle-btn").trigger("click");
    expect(input.attributes("type")).toBe("text");

    await wrapper.get("button.toggle-btn").trigger("click");
    expect(input.attributes("type")).toBe("password");
  });

  it("shows server validation errors", () => {
    const wrapper = mount(PasswordField, {
      props: {
        modelValue: "",
        showError: true,
        serverErrors: {
          password: ["A jelszo kotelezo"],
        },
      },
    });

    expect(wrapper.get(".invalid-feedback").classes()).toContain("d-block");
    expect(wrapper.text()).toContain("A jelszo kotelezo");
    expect(wrapper.get("input").classes()).toContain("is-invalid");
  });
});
