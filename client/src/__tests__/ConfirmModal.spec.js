import { describe, expect, it } from "vitest";
import { mount } from "@vue/test-utils";
import ConfirmModal from "@/components/Confirm/ConfirmModal.vue";

describe("ConfirmModal", () => {
  it("does not render while closed", () => {
    const wrapper = mount(ConfirmModal, {
      props: {
        isOpenConfirmModal: false,
      },
    });

    expect(wrapper.find(".confirm-overlay").exists()).toBe(false);
  });

  it("renders title, message and action labels when open", () => {
    const wrapper = mount(ConfirmModal, {
      props: {
        isOpenConfirmModal: true,
        title: "Torles",
        message: "Biztosan torlod?",
        cancel: "Megsem",
        confirm: "Igen",
      },
    });

    expect(wrapper.get(".confirm-title").text()).toBe("Torles");
    expect(wrapper.get(".confirm-body").text()).toContain("Biztosan torlod?");
    expect(wrapper.text()).toContain("Megsem");
    expect(wrapper.text()).toContain("Igen");
  });

  it("emits confirm and cancel events from its buttons", async () => {
    const wrapper = mount(ConfirmModal, {
      props: {
        isOpenConfirmModal: true,
      },
    });

    const buttons = wrapper.findAll("button");

    await buttons[1].trigger("click");
    await buttons[2].trigger("click");

    expect(wrapper.emitted("cancel")).toHaveLength(1);
    expect(wrapper.emitted("confirm")).toHaveLength(1);
  });

  it("emits cancel when the user clicks the overlay background", async () => {
    const wrapper = mount(ConfirmModal, {
      props: {
        isOpenConfirmModal: true,
      },
    });

    await wrapper.get(".confirm-overlay").trigger("click");

    expect(wrapper.emitted("cancel")).toHaveLength(1);
  });
});
