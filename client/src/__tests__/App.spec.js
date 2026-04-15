import { describe, it, expect, beforeEach } from "vitest";
import { createPinia, setActivePinia } from "pinia";
import { useGlobalLoadingStore } from "@/stores/globalLoadingStore";

describe("global loading store", () => {
  beforeEach(() => {
    setActivePinia(createPinia());
  });

  it("tracks pending requests and route loading", () => {
    const store = useGlobalLoadingStore();

    expect(store.isLoading).toBe(false);

    store.startRequest();
    expect(store.isLoading).toBe(true);

    store.finishRequest();
    expect(store.isLoading).toBe(false);

    store.setRouteLoading(true);
    expect(store.isLoading).toBe(true);

    store.reset();
    expect(store.isLoading).toBe(false);
  });
});
