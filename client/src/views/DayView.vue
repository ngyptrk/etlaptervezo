<template>
  <div>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <h1 class="m-0">Napok</h1>

      <div v-if="weeksCount > 1" class="d-flex align-items-center gap-2">
        <label class="text-warning fw-semibold">Hét:</label>
        <select
          v-model.number="selectedWeek"
          class="form-select week-select"
          @change="fetchPlan"
        >
          <option :value="0">Összes</option>
          <option v-for="week in availableWeeks" :key="week" :value="week">
            {{ week }}. hét
          </option>
        </select>
      </div>
    </div>

    <GeneratePlanPanel
      :loading="loading"
      :mode="mode"
      :amount="amount"
      @generate="onGenerate"
      @refresh="fetchPlan"
    />

    <div v-if="!multiWeekEnabled" class="migration-hint mb-3">
      Többhetes generáláshoz futtasd: <code>php artisan migrate</code>
    </div>

    <div v-if="loading" class="text-warning fw-semibold">Betöltés...</div>

    <div v-else-if="groupedDays.length === 0" class="empty-plan">
      Nincsen még kiírt recepted.
    </div>

    <WeeklyPlanBoard v-else :groupedDays="groupedDays" />
  </div>
</template>

<script>
import weeklyFoodService from "@/api/weeklyFoodService";
import GeneratePlanPanel from "@/components/Day/GeneratePlanPanel.vue";
import WeeklyPlanBoard from "@/components/Day/WeeklyPlanBoard.vue";

export default {
  name: "DayView",
  components: {
    GeneratePlanPanel,
    WeeklyPlanBoard,
  },
  data() {
    return {
      loading: false,
      mode: "weeks",
      amount: 1,
      selectedWeek: 0,
      planRows: [],
      weeksCount: 0,
      multiWeekEnabled: true,
    };
  },
  computed: {
    availableWeeks() {
      return Array.from({ length: this.weeksCount }, (_, i) => i + 1);
    },
    groupedDays() {
      const groups = new Map();

      this.planRows.forEach((row) => {
        const key = `${row.plan_week}-${row.weekday_id}`;
        if (!groups.has(key)) {
          groups.set(key, {
            week: row.plan_week,
            weekdayId: row.weekday_id,
            weekdayName: row.weekday?.day ?? "Ismeretlen nap",
            meals: [],
          });
        }

        groups.get(key).meals.push({
          id: row.id,
          mealPeriod: row.meal_requirement?.meal_of_day?.meal_of_day ?? "Étkezés",
          mealType: row.meal_requirement?.meal?.meal ?? "Típus",
          recipeName: row.recipe?.name ?? "Nincs recept",
          recipeDescription: row.recipe?.description ?? "",
          recipePicture: row.recipe?.picture ?? "",
        });
      });

      return Array.from(groups.values()).sort((a, b) => {
        if (a.week !== b.week) return a.week - b.week;
        return a.weekdayId - b.weekdayId;
      });
    },
  },
  methods: {
    async fetchPlan() {
      this.loading = true;
      try {
        const response = await weeklyFoodService.getMyPlan(this.selectedWeek || null);
        this.planRows = response.data ?? [];
        this.weeksCount = response.meta?.weeks ?? 0;
        this.multiWeekEnabled = response.meta?.multi_week_enabled ?? true;
      } catch (error) {
        this.planRows = [];
        this.weeksCount = 0;
      } finally {
        this.loading = false;
      }
    },
    async onGenerate(payload) {
      this.loading = true;
      try {
        this.mode = payload.mode;
        this.amount = payload.amount;
        const response = await weeklyFoodService.generate(payload);
        this.multiWeekEnabled = response.data?.multi_week_enabled ?? true;
        this.selectedWeek = 0;
        await this.fetchPlan();
      } catch (error) {
      } finally {
        this.loading = false;
      }
    },
  },
  async mounted() {
    await this.fetchPlan();
  },
};
</script>

<style scoped>
.week-select {
  width: 130px;
  background: #d7d7d9;
  border-color: #b1b4b8;
}

.empty-plan {
  border: 1px dashed rgba(244, 209, 74, 0.45);
  border-radius: 12px;
  padding: 1rem;
  color: #f4d14a;
  background: rgba(20, 20, 22, 0.62);
  font-weight: 600;
}

.migration-hint {
  border: 1px solid rgba(255, 169, 64, 0.55);
  background: rgba(255, 169, 64, 0.15);
  color: #ffd88c;
  border-radius: 10px;
  padding: 0.6rem 0.8rem;
}

.migration-hint code {
  color: #fff3bf;
}
</style>
