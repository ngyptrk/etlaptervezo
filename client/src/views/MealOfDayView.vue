<template>
  <div>
    <div class="d-flex align-items-center m-0 mb-2">
      <h1 class="m-0">Napi étkezések</h1>
      <span class="ms-2 text-warning">({{ rows.length }})</span>
      <button class="btn btn-success btn-sm ms-3" @click="createHandler">
        <i class="bi bi-plus-lg"></i> Hozzáadás
      </button>
    </div>

    <div v-if="loading" class="text-warning fw-semibold">Betöltés...</div>
    <div v-else-if="rows.length === 0" class="empty-list">Nincs találat</div>

    <div v-else class="list-wrap table-responsive">
      <table class="table list-table m-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Napi étkezés</th>
            <th>Művelet</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in rows" :key="item.id">
            <td>{{ item.id }}</td>
            <td>{{ item.meal_of_day }}</td>
            <td>
              <button class="btn btn-sm btn-outline-info" @click="updateHandler(item)">
                <i class="bi bi-pencil"></i> Módosítás
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <FormMealOfDay
      ref="form"
      :title="formTitle"
      :item="currentItem"
      @yesEventForm="yesEventFormHandler"
    />
  </div>
</template>

<script>
import mealOfDayService from "@/api/mealOfDayService";
import FormMealOfDay from "@/components/Forms/FormMealOfDay.vue";

export default {
  name: "MealOfDayView",
  components: {
    FormMealOfDay,
  },
  data() {
    return {
      loading: false,
      rows: [],
      mode: "create",
      currentItem: { id: 0, meal_of_day: "" },
      formTitle: "Új napi étkezés",
    };
  },
  methods: {
    async loadAll() {
      this.loading = true;
      try {
        const response = await mealOfDayService.getAll();
        this.rows = response.data ?? [];
      } finally {
        this.loading = false;
      }
    },
    updateHandler(item) {
      this.mode = "update";
      this.formTitle = "Napi étkezés módosítása";
      this.currentItem = { ...item };
      this.$refs.form.show();
    },
    createHandler() {
      this.mode = "create";
      this.formTitle = "Új napi étkezés";
      this.currentItem = { id: 0, meal_of_day: "" };
      this.$refs.form.show();
    },
    async yesEventFormHandler({ item, done }) {
      try {
        if (this.mode === "create") {
          await mealOfDayService.create(item);
        } else {
          await mealOfDayService.update(item.id, item);
        }
        await this.loadAll();
        done(true);
      } catch (err) {
        if (err.response && err.response.status === 422) {
          this.$refs.form.setServerErrors(err.response.data.errors);
        }
        done(false);
      }
    },
  },
  async mounted() {
    await this.loadAll();
  },
};
</script>

<style scoped>
.list-wrap {
  border: 1px solid rgba(244, 209, 74, 0.35);
  border-radius: 12px;
  overflow: hidden;
}

.list-table thead th {
  background: #1e2229;
  color: #ffd84f;
  border-bottom: 1px solid #ffd84f;
}

.list-table tbody td {
  background: #d6d6d8;
  color: #141414;
  border-color: #b9b9bc;
}

.list-table tbody tr:nth-child(even) td {
  background: #cfcfd2;
}

.empty-list {
  border: 1px dashed rgba(244, 209, 74, 0.5);
  border-radius: 12px;
  padding: 1rem;
  color: #f4d14a;
}
</style>
