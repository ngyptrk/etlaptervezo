<template>
  <div>
    <div class="d-flex align-items-center justify-content-between mb-3 gap-3 flex-wrap">
      <div class="d-flex align-items-center"><h1 class="m-0">Receptek</h1><span class="ms-2 text-warning">({{ filteredRows.length }})</span></div>
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="search-wrap"><i class="bi bi-search search-icon"></i><input v-model="searchWordInput" type="text" class="form-control search-input" placeholder="Keresés receptre..." /></div>
        <button class="btn btn-outline-warning btn-sm" :class="{ active: showFilters }" @click="toggleFilters" title="Szűrés">
          <i class="bi bi-funnel me-1"></i> Szűrés
        </button>
      </div>
      <button v-if="isAdmin" class="btn btn-success btn-sm" @click="createHandler"><i class="bi bi-plus-lg"></i> Hozzáadás</button>
    </div>
    <div v-if="showFilters" class="filter-panel mb-3">
      <div class="filter-row">
        <button class="filter-chip" :class="{ active: showOnlyFavorites }" @click="toggleFavoritesFilter">
          <i class="bi bi-star-fill me-1"></i> Kedvencek ({{ favoriteCount }})
        </button>
      </div>
      <div class="filter-divider"></div>
      <div class="filter-row">
        <button class="filter-chip" :class="{ active: selectedMealId === 0 }" @click="selectMeal(0)">
          Összes ({{ totalCount }})
        </button>
        <button v-for="meal in meals" :key="meal.id" class="filter-chip" :class="{ active: selectedMealId === meal.id }" @click="selectMeal(meal.id)">
          {{ meal.meal }} ({{ mealCount(meal.id) }})
        </button>
      </div>
    </div>
    <div v-if="loading" class="text-warning fw-semibold">Betöltés...</div>
    <div v-else-if="filteredRows.length === 0" class="empty-list">{{ emptyMessage }}</div>
    <div v-else class="recipe-grid">
      <article v-for="item in filteredRows" :key="item.id" class="recipe-card">
        <img v-if="item.picture" :src="pictureUrl(item.picture)" :alt="item.name" class="recipe-image" loading="lazy" role="button" title="Kattints a hozzávalókhoz" @click="openIngredients(item)" @error="onImageError" />
        <div class="recipe-body">
          <div class="d-flex align-items-start justify-content-between gap-2">
            <h5 class="m-0">{{ item.name }}</h5>
            <button class="favorite-button" :class="{ active: isFavorite(item.id) }" @click="toggleFavorite(item.id)" :title="isFavorite(item.id) ? 'Eltávolítás a kedvencekből' : 'Kedvencnek jelölés'">
              <i class="bi" :class="isFavorite(item.id) ? 'bi-star-fill' : 'bi-star'"></i>
            </button>
          </div>
          <div class="recipe-meta">{{ mealName(item.meal_id) }} | {{ item.person }} fő</div>
          <p class="recipe-desc m-0">{{ item.description }}</p>
          <div v-if="isAdmin" class="d-flex gap-2 mt-2">
            <button class="btn btn-sm btn-outline-info" @click="updateHandler(item)"><i class="bi bi-pencil"></i> Módosítás</button>
            <button class="btn btn-sm btn-outline-danger" @click="deleteHandler(item)"><i class="bi bi-trash"></i> Törlés</button>
          </div>
        </div>
      </article>
    </div>

    <div v-if="showIngredientsModal" class="overlay" @click.self="closeIngredients">
      <div class="ingredients-modal">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="m-0 text-warning">{{ selectedRecipe?.name }} hozzávalói</h5>
          <button class="btn btn-sm btn-outline-warning" @click="closeIngredients">Bezárás</button>
        </div>
        <div v-if="selectedIngredients.length === 0" class="text-warning">Nincs hozzávaló megadva ehhez a recepthez.</div>
        <ul v-else class="mb-0 ps-3"><li v-for="ing in selectedIngredients" :key="ing.id" class="mb-1">{{ ing.amount }} {{ unitName(ing.unit_id) }} - {{ rawIngredientName(ing.raw_ingredient_id) }}</li></ul>
      </div>
    </div>

    <FormRecipe v-if="isAdmin" ref="form" :title="formTitle" :item="currentItem" :meals="meals" @yesEventForm="yesEventFormHandler" />
    <ConfirmModal v-if="isAdmin" :isOpenConfirmModal="isOpenConfirmModal" :title="confirmTitle" :message="confirmMessage" cancel="Mégsem" confirm="Igen" @cancel="closeConfirmModal" @confirm="confirmActionHandler" />
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import recipeService from "@/api/recipeService";
import mealService from "@/api/mealService";
import ingredientService from "@/api/ingredientService";
import rawIngredientService from "@/api/rawIngredientService";
import unitService from "@/api/unitService";
import FormRecipe from "@/components/Forms/FormRecipe.vue";
import ConfirmModal from "@/components/Confirm/ConfirmModal.vue";
import { useSearchStore } from "@/stores/searchStore";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";

export default {
  name: "RecipeView",
  components: { FormRecipe, ConfirmModal },
  data() { return { loading: false, rows: [], meals: [], ingredients: [], rawIngredients: [], units: [], showIngredientsModal: false, selectedRecipe: null, mode: "create", currentItem: { id: 0, name: "", description: "", picture: "", person: 1, meal_id: 0 }, formTitle: "Új recept", isOpenConfirmModal: false, confirmTitle: "", confirmMessage: "", confirmAction: null, favoriteIds: [], showOnlyFavorites: false, showFilters: false, selectedMealId: 0 }; },
  computed: {
    ...mapState(useSearchStore, ["searchword", "searchWord"]),
    ...mapState(useUserLoginLogoutStore, ["role", "item"]),
    searchWordInput: { get() { return this.searchWord; }, set(value) { this.setSearchWord(value); } },
    isAdmin() { return this.role === 1; },
    currentUserId() { return this.item?.id ?? 0; },
    favoritesStorageKey() { return `favorite_recipes_${this.currentUserId || "guest"}`; },
    baseFilteredRows() {
      if (!this.searchword) return this.rows;
      return this.rows.filter((item) => [String(item.id), item.name, item.description, this.mealName(item.meal_id)].join(" ").toLowerCase().includes(this.searchword));
    },
    totalCount() { return this.baseFilteredRows.length; },
    favoriteCount() { return this.baseFilteredRows.filter((item) => this.isFavorite(item.id)).length; },
    emptyMessage() {
      if (this.showOnlyFavorites) return "Nincs kedvenc recept.";
      return "Nincs találat";
    },
    filteredRows() {
      let filtered = this.baseFilteredRows;
      if (this.showOnlyFavorites) {
        filtered = filtered.filter((item) => this.isFavorite(item.id));
      }
      if (this.selectedMealId !== 0) {
        filtered = filtered.filter((item) => Number(item.meal_id) === Number(this.selectedMealId));
      }
      return filtered;
    },
    selectedIngredients() { if (!this.selectedRecipe) return []; return this.ingredients.filter((x) => x.recipe_id === this.selectedRecipe.id); },
  },
  watch: {
    currentUserId() { this.loadFavorites(); },
    "$route.query.favorites"() { this.applyFavoritesFromRoute(); },
  },
  methods: {
    ...mapActions(useSearchStore, ["setSearchWord", "resetSearchWord"]),
    applyFavoritesFromRoute() {
      const wantsFavorites = String(this.$route?.query?.favorites || "") === "1";
      if (wantsFavorites) {
        this.showFilters = true;
        this.showOnlyFavorites = true;
      }
    },
    openConfirmModal({ title, message, onConfirm }) { this.confirmTitle = title; this.confirmMessage = message; this.confirmAction = onConfirm; this.isOpenConfirmModal = true; },
    closeConfirmModal() { this.isOpenConfirmModal = false; this.confirmTitle = ""; this.confirmMessage = ""; this.confirmAction = null; },
    async confirmActionHandler() { const action = this.confirmAction; this.closeConfirmModal(); if (typeof action === "function") await action(); },
    mealName(id) { return this.meals.find((x) => x.id === id)?.meal ?? `#${id}`; },
    rawIngredientName(id) { return this.rawIngredients.find((x) => x.id === id)?.raw_ingredient ?? `#${id}`; },
    unitName(id) { return this.units.find((x) => x.id === id)?.unit ?? `#${id}`; },
    pictureUrl(path) { const apiUrl = import.meta.env.VITE_API_URL || ""; const origin = apiUrl.replace(/\/api\/?$/, ""); const normalized = String(path).split("/").map((s) => encodeURIComponent(s)).join("/"); return origin ? `${origin}/${normalized}` : `/${normalized}`; },
    onImageError(event) { event.target.style.display = "none"; },
    openIngredients(recipe) { this.selectedRecipe = recipe; this.showIngredientsModal = true; },
    closeIngredients() { this.showIngredientsModal = false; this.selectedRecipe = null; },
    loadFavorites() {
      try {
        const raw = localStorage.getItem(this.favoritesStorageKey);
        const parsed = JSON.parse(raw || "[]");
        this.favoriteIds = Array.isArray(parsed) ? parsed.map((id) => Number(id)).filter((id) => Number.isFinite(id)) : [];
      } catch {
        this.favoriteIds = [];
      }
    },
    saveFavorites() {
      localStorage.setItem(this.favoritesStorageKey, JSON.stringify(this.favoriteIds));
    },
    isFavorite(id) { return this.favoriteIds.includes(Number(id)); },
    toggleFavorite(id) {
      const normalized = Number(id);
      if (this.isFavorite(normalized)) {
        this.favoriteIds = this.favoriteIds.filter((itemId) => itemId !== normalized);
      } else {
        this.favoriteIds = [...this.favoriteIds, normalized];
      }
      this.saveFavorites();
    },
    toggleFavoritesFilter() { this.showOnlyFavorites = !this.showOnlyFavorites; },
    toggleFilters() { this.showFilters = !this.showFilters; },
    selectMeal(id) { this.selectedMealId = Number(id) || 0; },
    mealCount(mealId) { return this.baseFilteredRows.filter((item) => Number(item.meal_id) === Number(mealId)).length; },
    toPayload(item) { return { name: String(item.name ?? "").trim(), description: String(item.description ?? "").trim(), picture: String(item.picture ?? "").trim(), person: Number(item.person ?? 1), meal_id: Number(item.meal_id ?? 0) }; },
    createHandler() { this.mode = "create"; this.formTitle = "Új recept"; this.currentItem = { id: 0, name: "", description: "", picture: "", person: 1, meal_id: this.meals[0]?.id ?? 0 }; this.$refs.form.show(); },
    updateHandler(item) { this.startUpdate(item); },
    startUpdate(item) { this.mode = "update"; this.formTitle = "Recept módosítás"; this.currentItem = { ...item }; this.$refs.form.show(); },
    deleteHandler(item) { this.openConfirmModal({ title: "Törlés megerősítése", message: `Biztosan törölni szeretnéd ezt a receptet: "${item.name}"?`, onConfirm: async () => { await recipeService.delete(item.id); await this.loadAll(); } }); },
    async yesEventFormHandler({ item, done }) {
      try { const payload = this.toPayload(item); if (this.mode === "create") await recipeService.create(payload); else await recipeService.update(item.id, payload); await this.loadAll(); done(true); }
      catch (err) { const apiErrors = err?.response?.data?.errors ?? {}; const apiMessage = err?.response?.data?.message; if (err.response && err.response.status === 422) this.$refs.form?.setServerErrors(apiErrors); else this.$refs.form?.setServerErrors({ general: [apiMessage || "Mentés sikertelen. Próbáld újra."] }); done(false); }
    },
    async loadAll() { this.loading = true; try { const [recipeRes, mealRes, ingredientRes, rawRes, unitRes] = await Promise.all([recipeService.getAll(), mealService.getAll(), ingredientService.getAll(), rawIngredientService.getAll(), unitService.getAll()]); this.rows = recipeRes.data ?? []; this.meals = mealRes.data ?? []; this.ingredients = ingredientRes.data ?? []; this.rawIngredients = rawRes.data ?? []; this.units = unitRes.data ?? []; } finally { this.loading = false; } },
  },
  async mounted() { this.resetSearchWord(); this.loadFavorites(); await this.loadAll(); this.applyFavoritesFromRoute(); },
  beforeUnmount() { this.resetSearchWord(); },
};
</script>

<style scoped>
.recipe-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px; }
.recipe-card { border: 1px solid rgba(244, 209, 74, 0.35); border-radius: 12px; overflow: hidden; background: #d4d4d6; color: #151515; }
.recipe-image { width: 100%; height: 160px; object-fit: cover; }
.recipe-body { padding: 0.7rem; }
.recipe-meta { font-size: 0.85rem; font-weight: 600; color: #3b3b3b; margin-top: 0.2rem; margin-bottom: 0.4rem; }
.recipe-desc { font-size: 0.9rem; color: #2f2f2f; }
.favorite-button { width: 32px; height: 32px; border-radius: 999px; border: 1px solid rgba(244, 209, 74, 0.4); background: #111217; color: #c2c2c2; display: inline-flex; align-items: center; justify-content: center; transition: transform 0.15s ease, box-shadow 0.15s ease, color 0.15s ease, border-color 0.15s ease; }
.favorite-button:hover { color: #f4d14a; border-color: rgba(244, 209, 74, 0.75); transform: translateY(-1px); }
.favorite-button.active { color: #f4d14a; border-color: rgba(244, 209, 74, 0.95); box-shadow: 0 0 0 0.2rem rgba(244, 209, 74, 0.2); }
.filter-panel { border: 1px solid rgba(244, 209, 74, 0.35); border-radius: 12px; background: #15171c; padding: 0.75rem; }
.filter-row { display: flex; flex-wrap: wrap; gap: 8px; }
.filter-divider { height: 1px; background: rgba(244, 209, 74, 0.25); margin: 0.6rem 0; }
.filter-chip { border: 1px solid rgba(244, 209, 74, 0.35); border-radius: 999px; padding: 0.35rem 0.7rem; background: #101216; color: #e1e1e1; font-size: 0.85rem; transition: border-color 0.15s ease, color 0.15s ease, background 0.15s ease, transform 0.15s ease; }
.filter-chip:hover { border-color: rgba(244, 209, 74, 0.7); color: #f4d14a; transform: translateY(-1px); }
.filter-chip.active { border-color: rgba(244, 209, 74, 0.95); color: #151515; background: #f4d14a; font-weight: 600; }
.search-wrap { position: relative; min-width: 320px; }
.search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #8a8a8a; }
.search-input { padding-left: 34px; border: 1px solid rgba(244, 209, 74, 0.45); background: #101216; color: #f1f1f1; }
.search-input:focus { border-color: #f4d14a; box-shadow: 0 0 0 0.2rem rgba(244, 209, 74, 0.2); background: #101216; color: #f1f1f1; }
.empty-list { border: 1px dashed rgba(244, 209, 74, 0.5); border-radius: 12px; padding: 1rem; color: #f4d14a; }
.overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.55); z-index: 1900; display: flex; align-items: flex-start; justify-content: center; padding-top: 70px; }
.ingredients-modal { width: min(600px, 92vw); background: #18191c; border: 1px solid rgba(244, 209, 74, 0.45); border-radius: 12px; color: #e9e9e9; padding: 0.9rem; box-shadow: 0 12px 28px rgba(0, 0, 0, 0.4); }
</style>
