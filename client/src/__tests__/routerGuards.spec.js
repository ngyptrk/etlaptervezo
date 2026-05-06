import { beforeEach, describe, expect, it, vi } from "vitest";
import { createPinia, setActivePinia } from "pinia";
import router from "@/router";
import { useToastStore } from "@/stores/toastStore";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";

vi.mock("@/views/HomeView.vue", () => ({ default: { template: "<div>Home</div>" } }));
vi.mock("@/views/EmptyWrapperView.vue", () => ({ default: { template: "<router-view />" } }));
vi.mock("@/views/LoginView.vue", () => ({ default: { template: "<div>Login</div>" } }));
vi.mock("@/views/UnitView.vue", () => ({ default: { template: "<div>Unit</div>" } }));
vi.mock("@/views/DayView.vue", () => ({ default: { template: "<div>Day</div>" } }));
vi.mock("@/views/404.vue", () => ({ default: { template: "<div>404</div>" } }));

describe("router guards", () => {
  beforeEach(async () => {
    localStorage.clear();
    setActivePinia(createPinia());
    await router.push("/");
    await router.isReady();
  });

  it("redirects guests from protected pages to login", async () => {
    await router.push({ name: "unit" });

    expect(router.currentRoute.value.path).toBe("/login");
  });

  it("allows an admin to open admin pages", async () => {
    const authStore = useUserLoginLogoutStore();
    authStore.item = { id: 1, name: "Admin", role: 1, token: "token" };

    await router.push({ name: "unit" });

    expect(router.currentRoute.value.name).toBe("unit");
  });

  it("redirects logged in users without the required role and shows a toast", async () => {
    const authStore = useUserLoginLogoutStore();
    const toastStore = useToastStore();
    authStore.item = { id: 2, name: "User", role: 2, token: "token" };

    await router.push({ name: "unit" });

    expect(router.currentRoute.value.path).toBe("/");
    expect(toastStore.messages).toContain("Ehhez az oldalhoz nincs jogod!");
    expect(toastStore.type).toBe("Error");
  });
});
