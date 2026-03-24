<template>
  <div class="profile-dashboard">
    <div class="profile-header">
      <div>
        <h1 class="m-0">Saját fiók</h1>
        <div class="profile-subtitle">Áttekintés a heti receptjeidről és aktivitásodról.</div>
      </div>
      <section class="profile-card">
        <div><strong>Név:</strong> {{ userName || "-" }}</div>
        <div><strong>Email:</strong> {{ userEmail || "-" }}</div>
        <div><strong>Szerepkör:</strong> {{ userNameWithRole || "-" }}</div>
        <div class="profile-actions">
          <button class="btn btn-outline-warning btn-sm" type="button" @click="toggleEditForm">
            Adataim módosítása
          </button>
        </div>
      </section>
    </div>

    <transition name="fade-slide" mode="out-in">
      <section v-if="showEditForm" class="profile-edit-card">
        <div class="section-head">
          <h2 class="m-0">Adataim módosítása</h2>
        </div>
        <div class="edit-grid">
          <div class="edit-field">
            <label class="edit-label" for="profile-name">Név</label>
            <input id="profile-name" v-model.trim="profileForm.name" type="text" class="form-control edit-input" placeholder="Írd be a neved" />
            <div v-if="profileErrors.name" class="edit-error">{{ profileErrors.name }}</div>
          </div>
          <div class="edit-field">
            <label class="edit-label" for="profile-email">Email</label>
            <input id="profile-email" v-model.trim="profileForm.email" type="email" class="form-control edit-input" placeholder="nev@email.hu" />
            <div v-if="profileErrors.email" class="edit-error">{{ profileErrors.email }}</div>
          </div>
        </div>
        <div class="edit-actions">
          <button class="btn btn-outline-warning" type="button" :disabled="savingProfile" @click="cancelEdit">
            Mégsem
          </button>
          <button class="btn btn-warning" type="button" :disabled="savingProfile || !profileChanged" @click="saveProfile">
            Mentés
          </button>
        </div>
        <div v-if="profileErrors.general" class="edit-error mt-2">{{ profileErrors.general }}</div>
      </section>
    </transition>

    <section class="stats-grid" aria-label="Statisztikák">
      <div class="stat-card">
        <div class="stat-label">Hetek száma</div>
        <div class="stat-value">{{ weekCount }}</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Receptek száma</div>
        <div class="stat-value">{{ recipeCount }}</div>
      </div>
      <button class="stat-card stat-card-link" type="button" @click="goToFavorites">
        <div class="stat-label">Kedvencek</div>
        <div class="stat-value">{{ favoriteCount }}</div>
      </button>
      <div class="stat-card">
        <div class="stat-label">Aktív napok</div>
        <div class="stat-value">{{ activeDayCount }}</div>
      </div>
    </section>

    <div v-if="loading" class="text-warning fw-semibold">Betöltés...</div>

    <template v-else>
      <section class="today-highlight">
        <div class="section-head">
          <h2 class="m-0">Mai menü</h2>
          <span v-if="latestWeek" class="week-badge">{{ latestWeek }}. hét</span>
        </div>
        <div v-if="todayMeals.length === 0" class="empty-plan">
          Nincs mára kiírt recepted.
        </div>
        <div v-else class="highlight-grid">
          <article v-for="meal in todayMeals" :key="meal.id" class="recipe-card-horizontal highlight-card">
            <div class="recipe-media">
              <img v-if="meal.recipePicture" :src="buildImageUrl(meal.recipePicture)" :alt="meal.recipeName" class="recipe-image" loading="lazy" @error="onImageError" />
            </div>
            <div class="recipe-content">
              <div class="recipe-title">{{ meal.recipeName }}</div>
              <div class="recipe-meta">{{ meal.mealPeriod }} • {{ meal.mealType }}</div>
              <p class="recipe-desc">{{ meal.recipeDescription }}</p>
              <button class="btn btn-sm btn-outline-warning" @click="toggleIngredients(meal.id)">
                Hozzávalók <i class="bi bi-basket"></i>
              </button>
              <div v-if="openMeals[meal.id]" class="ingredients-content">
                <ul v-if="meal.ingredients?.length" class="ingredients-list">
                  <li v-for="(ing, idx) in meal.ingredients" :key="`${meal.id}-highlight-${idx}`">
                    {{ ing.amount }} {{ ing.unit }} - {{ ing.name }}
                  </li>
                </ul>
                <div v-else class="ingredients-empty">Ehhez a recepthez nincs hozzávaló megadva.</div>
              </div>
            </div>
          </article>
        </div>
      </section>

      <section class="tabs-section">
        <div class="section-head">
          <h2 class="m-0">Igényelt receptek</h2>
          <span v-if="latestWeek" class="week-badge">{{ latestWeek }}. hét</span>
        </div>

        <div class="day-tabs" role="tablist">
          <button
            v-for="tab in weekdayTabs"
            :key="tab.id"
            class="day-tab"
            :class="{ active: activeDayId === tab.id }"
            role="tab"
            @click="setActiveDay(tab.id)"
          >
            {{ tab.name }}
            <span class="tab-count">{{ dayMealsById[tab.id]?.length || 0 }}</span>
          </button>
        </div>

        <transition name="fade-slide" mode="out-in">
          <div :key="activeDayId">
            <div v-if="activeDayMeals.length === 0" class="empty-plan">
              Nincs még kiírt recepted erre a napra.
            </div>
            <div v-else class="recipes-grid">
              <article v-for="meal in activeDayMeals" :key="meal.id" class="recipe-card-horizontal">
                <div class="recipe-media">
                  <img v-if="meal.recipePicture" :src="buildImageUrl(meal.recipePicture)" :alt="meal.recipeName" class="recipe-image" loading="lazy" @error="onImageError" />
                </div>
                <div class="recipe-content">
                  <div class="recipe-title">{{ meal.recipeName }}</div>
                  <div class="recipe-meta">{{ meal.mealPeriod }} • {{ meal.mealType }}</div>
                  <p class="recipe-desc">{{ meal.recipeDescription }}</p>
                  <button class="btn btn-sm btn-outline-warning" @click="toggleIngredients(meal.id)">
                    Hozzávalók <i class="bi bi-basket"></i>
                  </button>
                  <div v-if="openMeals[meal.id]" class="ingredients-content">
                    <ul v-if="meal.ingredients?.length" class="ingredients-list">
                      <li v-for="(ing, idx) in meal.ingredients" :key="`${meal.id}-tab-${idx}`">
                        {{ ing.amount }} {{ ing.unit }} - {{ ing.name }}
                      </li>
                    </ul>
                    <div v-else class="ingredients-empty">Ehhez a recepthez nincs hozzávaló megadva.</div>
                  </div>
                </div>
              </article>
            </div>
          </div>
        </transition>
      </section>
    </template>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import weeklyFoodService from "@/api/weeklyFoodService";
import { useToastStore } from "@/stores/toastStore";

export default {
  name: "UserProfileView",
  data() {
    return {
      loading: false,
      planRows: [],
      activeDayId: 1,
      openMeals: {},
      favoriteIds: [],
      profileForm: { name: "", email: "" },
      profileErrors: {},
      savingProfile: false,
      showEditForm: false,
    };
  },
  computed: {
    ...mapState(useUserLoginLogoutStore, ["item", "userName", "userNameWithRole"]),
    userEmail() {
      return this.item?.email ?? "";
    },
    currentUserId() {
      return this.item?.id ?? 0;
    },
    favoritesStorageKey() {
      return `favorite_recipes_${this.currentUserId || "guest"}`;
    },
    favoriteCount() {
      return this.favoriteIds.length;
    },
    profileChanged() {
      const currentName = this.userName || "";
      const currentEmail = this.userEmail || "";
      return this.profileForm.name !== currentName || this.profileForm.email !== currentEmail;
    },
    weekdayTabs() {
      return [
        { id: 1, name: "Hétfő" },
        { id: 2, name: "Kedd" },
        { id: 3, name: "Szerda" },
        { id: 4, name: "Csütörtök" },
        { id: 5, name: "Péntek" },
        { id: 6, name: "Szombat" },
        { id: 7, name: "Vasárnap" },
      ];
    },
    latestWeek() {
      const weeks = this.planRows.map((row) => Number(row.plan_week || 0));
      return weeks.length ? Math.max(...weeks) : 0;
    },
    rowsForLatestWeek() {
      if (!this.latestWeek) return [];
      return this.planRows.filter((row) => Number(row.plan_week) === Number(this.latestWeek));
    },
    dayMealsById() {
      const map = {};
      this.weekdayTabs.forEach((tab) => {
        map[tab.id] = [];
      });

      this.rowsForLatestWeek.forEach((row) => {
        const key = Number(row.weekday_id) || 0;
        if (!map[key]) map[key] = [];
        map[key].push({
          id: row.id,
          mealPeriod: row.meal_requirement?.meal_of_day?.meal_of_day ?? "Étkezés",
          mealType: row.meal_requirement?.meal?.meal ?? "Típus",
          recipeName: row.recipe?.name ?? "Nincs recept",
          recipeDescription: row.recipe?.description ?? "",
          recipePicture: row.recipe?.picture ?? "",
          ingredients: (row.recipe?.ingredients ?? []).map((ing) => ({
            amount: ing.amount ?? 0,
            unit: ing.unit?.unit ?? "",
            name: ing.raw_ingredient?.raw_ingredient ?? "Ismeretlen",
          })),
        });
      });

      return map;
    },
    activeDayMeals() {
      return this.dayMealsById[this.activeDayId] ?? [];
    },
    todayWeekdayId() {
      const day = new Date().getDay();
      const map = { 0: 7, 1: 1, 2: 2, 3: 3, 4: 4, 5: 5, 6: 6 };
      return map[day] || 1;
    },
    todayMeals() {
      return this.dayMealsById[this.todayWeekdayId] ?? [];
    },
    weekCount() {
      const weeks = new Set(this.planRows.map((row) => Number(row.plan_week || 0)).filter(Boolean));
      return weeks.size;
    },
    recipeCount() {
      const recipes = new Set(this.planRows.map((row) => row.recipe?.id).filter(Boolean));
      return recipes.size || this.planRows.length;
    },
    activeDayCount() {
      return Object.values(this.dayMealsById).filter((items) => items.length > 0).length;
    },
  },
  watch: {
    currentUserId() {
      this.loadFavorites();
    },
    item: {
      handler() {
        this.resetProfileForm();
      },
      deep: true,
    },
  },
  methods: {
    ...mapActions(useUserLoginLogoutStore, ["getMeRefresh", "updateSelf"]),
    toggleEditForm() {
      this.showEditForm = !this.showEditForm;
      if (this.showEditForm) {
        this.resetProfileForm();
      }
    },
    cancelEdit() {
      this.resetProfileForm();
      this.showEditForm = false;
    },
    resetProfileForm() {
      this.profileForm = {
        name: this.userName || "",
        email: this.userEmail || "",
      };
      this.profileErrors = {};
    },
    async saveProfile() {
      this.savingProfile = true;
      this.profileErrors = {};
      const payload = {
        name: this.profileForm.name?.trim() || undefined,
        email: this.profileForm.email?.trim() || undefined,
      };

      try {
        await this.updateSelf(payload);
        const toastStore = useToastStore();
        toastStore.messages.push("Sikeres mentés.");
        toastStore.show("Success");
        this.resetProfileForm();
        this.showEditForm = false;
      } catch (err) {
        if (err?.response?.status === 422) {
          const errors = err?.response?.data?.errors ?? {};
          this.profileErrors = {
            name: errors.name?.[0],
            email: errors.email?.[0],
          };
        } else {
          this.profileErrors.general = "A mentés sikertelen. Próbáld újra.";
        }
      } finally {
        this.savingProfile = false;
      }
    },
    goToFavorites() {
      this.$router.push({ name: "recipe", query: { favorites: "1" } });
    },
    setActiveDay(id) {
      this.activeDayId = Number(id) || 1;
    },
    buildImageUrl(picturePath) {
      const apiUrl = import.meta.env.VITE_API_URL || "";
      const origin = apiUrl.replace(/\/api\/?$/, "");
      const normalized = String(picturePath || "")
        .split("/")
        .map((segment) => encodeURIComponent(segment))
        .join("/");

      return origin ? `${origin}/${normalized}` : `/${normalized}`;
    },
    onImageError(event) {
      event.target.style.display = "none";
    },
    toggleIngredients(mealId) {
      this.openMeals[mealId] = !this.openMeals[mealId];
    },
    loadFavorites() {
      try {
        const raw = localStorage.getItem(this.favoritesStorageKey);
        const parsed = JSON.parse(raw || "[]");
        this.favoriteIds = Array.isArray(parsed) ? parsed.map((id) => Number(id)).filter((id) => Number.isFinite(id)) : [];
      } catch {
        this.favoriteIds = [];
      }
    },
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
    this.activeDayId = this.todayWeekdayId;
    this.loadFavorites();
    this.resetProfileForm();
    await this.loadProfileData();
  },
};
</script>

<style scoped>
.profile-dashboard {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.profile-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.profile-subtitle {
  color: #d5d5d5;
  margin-top: 0.35rem;
}

.profile-card {
  border: 1px solid rgba(244, 209, 74, 0.4);
  border-radius: 14px;
  padding: 0.9rem 1rem;
  background: rgba(20, 20, 22, 0.82);
  color: #f6f6f6;
  min-width: 260px;
  box-shadow: 0 0 18px rgba(244, 209, 74, 0.08);
}

.profile-actions {
  margin-top: 0.75rem;
}

.profile-edit-card {
  border: 1px solid rgba(244, 209, 74, 0.28);
  border-radius: 16px;
  padding: 1rem;
  background: rgba(12, 12, 14, 0.76);
  box-shadow: 0 10px 26px rgba(0, 0, 0, 0.35);
}

.edit-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.edit-field {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.edit-label {
  font-size: 0.85rem;
  color: #cfcfcf;
  font-weight: 600;
}

.edit-input {
  border: 1px solid rgba(244, 209, 74, 0.45);
  background: #101216;
  color: #f1f1f1;
}

.edit-input:focus {
  border-color: #f4d14a;
  box-shadow: 0 0 0 0.2rem rgba(244, 209, 74, 0.2);
  background: #101216;
  color: #f1f1f1;
}

.edit-actions {
  display: flex;
  gap: 0.6rem;
  justify-content: flex-end;
  margin-top: 0.9rem;
}

.edit-error {
  color: #f4d14a;
  font-size: 0.85rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 0.9rem;
}

.stat-card {
  border: 1px solid rgba(244, 209, 74, 0.4);
  border-radius: 14px;
  padding: 0.9rem 1rem;
  background: rgba(14, 15, 18, 0.92);
  color: #f3f3f3;
  box-shadow: 0 10px 22px rgba(0, 0, 0, 0.35);
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.stat-card:hover {
  transform: scale(1.02);
  border-color: rgba(244, 209, 74, 0.9);
  box-shadow: 0 0 18px rgba(244, 209, 74, 0.25), 0 10px 24px rgba(0, 0, 0, 0.4);
}

.stat-card-link {
  text-align: left;
  cursor: pointer;
  width: 100%;
  display: block;
  appearance: none;
}

.stat-label {
  font-size: 0.85rem;
  color: #b7b7b7;
}

.stat-value {
  font-size: 1.6rem;
  font-weight: 700;
  color: #f4d14a;
}

.section-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.8rem;
  margin-bottom: 0.8rem;
}

.week-badge {
  color: #111;
  background: #f4d14a;
  border-radius: 999px;
  padding: 0.25rem 0.8rem;
  font-size: 0.82rem;
  font-weight: 700;
}

.today-highlight {
  border: 1px solid rgba(244, 209, 74, 0.28);
  border-radius: 16px;
  padding: 1rem;
  background: rgba(15, 15, 16, 0.8);
}

.highlight-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.tabs-section {
  border: 1px solid rgba(244, 209, 74, 0.2);
  border-radius: 16px;
  padding: 1rem;
  background: rgba(12, 12, 14, 0.72);
}

.day-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.day-tab {
  border: 1px solid rgba(244, 209, 74, 0.4);
  border-radius: 999px;
  padding: 0.4rem 0.85rem;
  background: rgba(15, 16, 20, 0.9);
  color: #e2e2e2;
  font-size: 0.85rem;
  transition: transform 0.2s ease, border-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
}

.day-tab:hover {
  transform: translateY(-1px);
  border-color: rgba(244, 209, 74, 0.75);
  color: #f4d14a;
}

.day-tab.active {
  color: #111;
  background: #f4d14a;
  border-color: rgba(244, 209, 74, 0.95);
  box-shadow: 0 0 12px rgba(244, 209, 74, 0.35);
  font-weight: 700;
}

.tab-count {
  margin-left: 0.4rem;
  font-weight: 700;
}

.recipes-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.recipe-card-horizontal {
  display: grid;
  grid-template-columns: 120px 1fr;
  gap: 1rem;
  border: 1px solid rgba(244, 209, 74, 0.35);
  border-radius: 16px;
  padding: 0.9rem;
  background: rgba(16, 16, 20, 0.95);
  color: #f2f2f2;
  box-shadow: 0 10px 24px rgba(0, 0, 0, 0.35);
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.recipe-card-horizontal:hover {
  transform: scale(1.02);
  border-color: rgba(244, 209, 74, 0.85);
  box-shadow: 0 0 16px rgba(244, 209, 74, 0.25), 0 10px 24px rgba(0, 0, 0, 0.4);
}

.recipe-card-horizontal.highlight-card {
  grid-template-columns: 150px 1fr;
  padding: 1rem;
}

.recipe-media {
  display: flex;
  align-items: center;
  justify-content: center;
}

.recipe-image {
  width: 100%;
  height: 100%;
  max-height: 120px;
  object-fit: cover;
  border-radius: 12px;
  border: 1px solid rgba(244, 209, 74, 0.35);
}

.recipe-card-horizontal.highlight-card .recipe-image {
  max-height: 140px;
}

.recipe-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: #f4d14a;
}

.recipe-meta {
  margin-top: 0.2rem;
  font-size: 0.9rem;
  color: #d0d0d0;
}

.recipe-desc {
  margin: 0.4rem 0 0.65rem;
  color: #cfcfcf;
  font-size: 0.9rem;
}

.ingredients-content {
  margin-top: 0.6rem;
  border-top: 1px dashed rgba(244, 209, 74, 0.35);
  padding-top: 0.5rem;
}

.ingredients-list {
  margin: 0;
  padding-left: 1rem;
  font-size: 0.86rem;
  color: #e5e5e5;
}

.ingredients-empty {
  font-size: 0.85rem;
  color: #bcbcbc;
}

.empty-plan {
  border: 1px dashed rgba(244, 209, 74, 0.45);
  border-radius: 12px;
  padding: 1rem;
  color: #f4d14a;
  background: rgba(20, 20, 22, 0.62);
  font-weight: 600;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(8px);
}

@media (max-width: 1200px) {
  .recipes-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 992px) {
  .edit-grid {
    grid-template-columns: 1fr;
  }

  .stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .highlight-grid {
    grid-template-columns: 1fr;
  }

  .recipes-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .profile-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .recipe-card-horizontal {
    grid-template-columns: 1fr;
  }

  .recipe-card-horizontal.highlight-card {
    grid-template-columns: 1fr;
  }

  .recipe-image {
    max-height: 180px;
  }
}
</style>
