import { beforeEach, describe, expect, it } from "vitest";
import { createPinia, setActivePinia } from "pinia";
import { mount } from "@vue/test-utils";
import App from "@/App.vue";
import { useGlobalLoadingStore } from "@/stores/globalLoadingStore";
import { useToastStore } from "@/stores/toastStore";
import ToastContanier from "@/components/Message/ToastContanier.vue";

describe("UI state", () => {
  let pinia;

  beforeEach(() => {
    pinia = createPinia();
    setActivePinia(pinia);
  });

  it("opens and closes the mobile sidebar from App state", async () => {
    const wrapper = mount(App, {
      global: {
        plugins: [pinia],
        stubs: {
          Menu: { template: '<aside class="sidebar-stub" />' },
          Header: true,
          Breadcrumb: true,
          RouterView: true,
          ToastContanier: true,
        },
      },
    });

    expect(wrapper.vm.isSidebarOpen).toBe(false);
    expect(wrapper.find(".sidebar-backdrop").exists()).toBe(false);

    await wrapper.get(".mobile-toggle").trigger("click");

    expect(wrapper.vm.isSidebarOpen).toBe(true);
    expect(wrapper.find(".sidebar-backdrop").exists()).toBe(true);

    await wrapper.get(".sidebar-backdrop").trigger("click");

    expect(wrapper.vm.isSidebarOpen).toBe(false);
  });

  it("shows the global loading overlay when the loading store is active", async () => {
    const store = useGlobalLoadingStore();
    const wrapper = mount(App, {
      global: {
        plugins: [pinia],
        stubs: {
          Menu: true,
          Header: true,
          Breadcrumb: true,
          RouterView: true,
          ToastContanier: true,
        },
      },
    });

    expect(wrapper.find(".global-loading-overlay").exists()).toBe(false);

    store.startRequest();
    await wrapper.vm.$nextTick();

    expect(wrapper.find(".global-loading-overlay").exists()).toBe(true);
  });

  it("renders and closes toast messages", async () => {
    const store = useToastStore();
    store.messages = ["Sikeres mentes"];
    store.type = "Success";

    const wrapper = mount(ToastContanier, {
      global: {
        plugins: [pinia],
      },
    });

    expect(wrapper.text()).toContain("Success");
    expect(wrapper.text()).toContain("Sikeres mentes");

    await wrapper.get(".btn-close").trigger("click");

    expect(store.messages).toEqual([]);
  });
});
