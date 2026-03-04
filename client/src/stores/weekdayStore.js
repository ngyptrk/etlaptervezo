import { defineStore } from "pinia";
import service from "@/api/weekdayService";

class Item {
  constructor(id = 0, day = "") {
    this.id = id;
    this.day = day;
  }
}

export const useWeekdayStore = defineStore("weekdays", {
  state: () => ({
    item: new Item(),
    items: [],
    loading: false,
    error: null,
    sortColumn: "id",
    sortDirection: "asc",
  }),
  getters: {
    getItemsLength() {
      return this.items.length;
    },
  },
  actions: {
    clearItem() {
      this.item = new Item();
    },
    sortItems(column = "id") {
      this.sortDirection =
        this.sortColumn === column && this.sortDirection === "asc"
          ? "desc"
          : "asc";
      this.sortColumn = column;

      const direction = this.sortDirection === "asc" ? 1 : -1;
      this.items = [...this.items].sort((a, b) => {
        const left = `${a[column] ?? ""}`.toLowerCase();
        const right = `${b[column] ?? ""}`.toLowerCase();
        if (left < right) return -1 * direction;
        if (left > right) return 1 * direction;
        return 0;
      });
    },
    async getAll() {
      this.loading = true;
      this.error = null;
      try {
        const response = await service.getAll();
        this.items = response.data ?? [];
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async getById(id) {
      this.loading = true;
      this.error = null;
      try {
        const response = await service.getById(id);
        this.item = response.data;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async create(data) {
      this.loading = true;
      this.error = null;
      try {
        await service.create(data);
        await this.getAll();
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async update(id, data) {
      this.loading = true;
      this.error = null;
      try {
        await service.update(id, data);
        await this.getAll();
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async delete(id) {
      this.loading = true;
      this.error = null;
      try {
        await service.delete(id);
        await this.getAll();
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
  },
});
