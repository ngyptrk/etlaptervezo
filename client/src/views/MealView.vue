<template>
  <div>
    <div class="d-flex align-items-center mb-3">
      <h1 class="m-0">Étkezések</h1>
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
            <th>Étkezés</th>
            <th>Művelet</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in rows" :key="item.id">
            <td>{{ item.id }}</td>
            <td>{{ item.meal }}</td>
            <td>
              <button class="btn btn-sm btn-outline-info" @click="updateHandler(item)">
                <i class="bi bi-pencil"></i> Módosítás
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <FormMeal
      ref="form"
      :title="formTitle"
      :item="currentItem"
      @yesEventForm="yesEventFormHandler"
    />
  </div>
</template>

<script>
import mealService from "@/api/mealService";
import FormMeal from "@/components/Forms/FormMeal.vue";

export default {
  name: "MealView",
  components: {
    FormMeal,
  },
  data() {
    return {
      loading: false,
      rows: [],
      mode: "create",
      currentItem: { id: 0, meal: "" },
      formTitle: "Új étkezés",
    };
  },
  methods: {
    async loadAll() {
      this.loading = true;
      try {
        const response = await mealService.getAll();
        this.rows = response.data ?? [];
      } finally {
        this.loading = false;
      }
    },
    createHandler() {
      this.mode = "create";
      this.formTitle = "Új étkezés";
      this.currentItem = { id: 0, meal: "" };
      this.$refs.form.show();
    },
    updateHandler(item) {
      this.mode = "update";
      this.formTitle = "Étkezés módosítás";
      this.currentItem = { ...item };
      this.$refs.form.show();
    },
    async yesEventFormHandler({ item, done }) {
      try {
        if (this.mode === "create") {
          await mealService.create(item);
        } else {
          await mealService.update(item.id, item);
        }
        await this.loadAll();
        done(true);
      } catch (err) {
        if (err.response && err.response.status === 422) {
          this.$refs.form.setServerErrors(err.response.data.errors ?? {});
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
