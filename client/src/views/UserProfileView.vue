<template>
  <div>
    <h1 class="m-0 mb-3">Saját fiók</h1>

    <section class="profile-card mb-3">
      <div><strong>Név:</strong> {{ userName || "-" }}</div>
      <div><strong>Email:</strong> {{ userEmail || "-" }}</div>
      <div><strong>Szerepkör:</strong> {{ userNameWithRole || "-" }}</div>
    </section>

    <h2 class="m-0 mb-2">Igényelt receptek</h2>
    <div v-if="loading" class="text-warning fw-semibold">Betöltés...</div>
    <div v-else-if="groupedDays.length === 0" class="empty-plan">
      Nincsen még kiírt recepted.
    </div>
    <WeeklyPlanBoard v-else :groupedDays="groupedDays" />
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import weeklyFoodService from "@/api/weeklyFoodService";
import WeeklyPlanBoard from "@/components/Day/WeeklyPlanBoard.vue";

export default {
  name: "UserProfileView",
  components: {
    WeeklyPlanBoard,
  },
  data() {
    return {
      loading: false,
      planRows: [],
    };
  },
  computed: {
    ...mapState(useUserLoginLogoutStore, ["item", "userName", "userNameWithRole"]),
    userEmail() {
      return this.item?.email ?? "";
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
    ...mapActions(useUserLoginLogoutStore, ["getMeRefresh"]),
    async loadProfileData() {
      this.loading = true;
      try {
        await this.getMeRefresh();
        const response = await weeklyFoodService.getMyPlan();
        this.planRows = response.data ?? [];
      } finally {
        this.loading = false;
      }
    },
  },
  async mounted() {
    await this.loadProfileData();
  },
};
</script>

<style scoped>
.profile-card {
  border: 1px solid rgba(244, 209, 74, 0.4);
  border-radius: 12px;
  padding: 0.9rem;
  background: rgba(20, 20, 22, 0.82);
  color: #f6f6f6;
}

.empty-plan {
  border: 1px dashed rgba(244, 209, 74, 0.45);
  border-radius: 12px;
  padding: 1rem;
  color: #f4d14a;
  background: rgba(20, 20, 22, 0.62);
  font-weight: 600;
}
</style>
