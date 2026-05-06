import { beforeEach, describe, expect, it, vi } from "vitest";
import { createPinia, setActivePinia } from "pinia";
import { useGlobalLoadingStore } from "@/stores/globalLoadingStore";
import { useSearchStore } from "@/stores/searchStore";
import { useToastStore } from "@/stores/toastStore";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";

describe("stores", () => {
  beforeEach(() => {
    localStorage.clear();
    setActivePinia(createPinia());
  });

  it("tracks pending requests and route loading", () => {
    const store = useGlobalLoadingStore();

    expect(store.isLoading).toBe(false);

    store.startRequest();
    expect(store.pendingRequests).toBe(1);
    expect(store.isLoading).toBe(true);

    store.finishRequest();
    store.finishRequest();
    expect(store.pendingRequests).toBe(0);
    expect(store.isLoading).toBe(false);

    store.setRouteLoading(true);
    expect(store.isLoading).toBe(true);

    store.reset();
    expect(store.isLoading).toBe(false);
  });

  it("stores a trimmed search word and exposes it lowercase", () => {
    const store = useSearchStore();

    store.setSearchWord("  ReGGeli  ");

    expect(store.searchWord).toBe("ReGGeli");
    expect(store.searchword).toBe("reggeli");

    store.resetSearchWord();
    expect(store.searchWord).toBe("");
  });

  it("clears toast state after the timeout", () => {
    vi.useFakeTimers();
    const store = useToastStore();

    store.messages.push("Mentve");
    store.show("Success");

    expect(store.type).toBe("Success");
    expect(store.messages).toEqual(["Mentve"]);

    vi.advanceTimersByTime(3000);

    expect(store.type).toBeNull();
    expect(store.messages).toEqual([]);
    vi.useRealTimers();
  });

  it("checks route access by login state and role", () => {
    const store = useUserLoginLogoutStore();

    expect(store.canAccess()).toBe(true);
    expect(store.canAccess([1])).toBe(false);

    store.item = { id: 1, name: "Admin", role: 1, token: "token" };

    expect(store.isLoggedIn).toBe(true);
    expect(store.canAccess([1])).toBe(true);
    expect(store.canAccess([2])).toBe(false);
    expect(store.userNameWithRole).toBe("Admin");
  });
});
