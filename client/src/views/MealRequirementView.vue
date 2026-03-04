<template>
  <div>
    <div class="d-flex align-items-center mb-3">
      <h1 class="m-0">Étkezés elvárások</h1>
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
            <th>Étel típus</th>
            <th>Művelet</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in rows" :key="item.id">
            <td>{{ item.id }}</td>
            <td>{{ mealOfDayName(item.meal_of_day_id) }}</td>
            <td>{{ mealName(item.meal_id) }}</td>
            <td>
              <button class="btn btn-sm btn-outline-info" @click="updateHandler(item)">
                <i class="bi bi-pencil"></i> Módosítás
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <FormMealRequirement
      ref="form"
      :title="formTitle"
      :item="currentItem"
      :meals="meals"
      :mealOfDays="mealOfDays"
      @yesEventForm="yesEventFormHandler"
    />
  </div>
</template>

<script>
import mealRequirementService from "@/api/mealRequirementService";
import mealOfDayService from "@/api/mealOfDayService";
import mealService from "@/api/mealService";
import FormMealRequirement from "@/components/Forms/FormMealRequirement.vue";

export default {
  name: "MealRequirementView",
  components: {
    FormMealRequirement,
  },
  data() {
    return {
      loading: false,
      rows: [],
      mealOfDays: [],
      meals: [],
      mode: "create",
      currentItem: {
        id: 0,
        meal_of_day_id: 0,
        meal_id: 0,
      },
      formTitle: "Új étkezés elvárás",
    };
  },
  methods: {
    mealOfDayName(id) {
      return this.mealOfDays.find((x) => x.id === id)?.meal_of_day ?? `#${id}`;
    },
    mealName(id) {
      return this.meals.find((x) => x.id === id)?.meal ?? `#${id}`;
    },
    async loadAll() {
      this.loading = true;
      try {
        const [reqRes, mealOfDayRes, mealRes] = await Promise.all([
          mealRequirementService.getAll(),
          mealOfDayService.getAll(),
          mealService.getAll(),
        ]);

        this.rows = reqRes.data ?? [];
        this.mealOfDays = mealOfDayRes.data ?? [];
        this.meals = mealRes.data ?? [];
      } finally {
        this.loading = false;
      }
    },
    createHandler() {
      this.mode = "create";
      this.formTitle = "Új étkezés elvárás";
      this.currentItem = {
        id: 0,
        meal_of_day_id: this.mealOfDays[0]?.id ?? 0,
        meal_id: this.meals[0]?.id ?? 0,
      };
      this.$refs.form.show();
    },
    updateHandler(item) {
      this.mode = "update";
      this.formTitle = "Étkezés elvárás módosítás";
      this.currentItem = { ...item };
      this.$refs.form.show();
    },
    async yesEventFormHandler({ item, done }) {
      try {
        if (this.mode === "create") {
          await mealRequirementService.create(item);
        } else {
          await mealRequirementService.update(item.id, item);
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
