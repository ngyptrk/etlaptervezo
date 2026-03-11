<template>
  <div>
    <div class="d-flex align-items-center justify-content-between mb-3 gap-3 flex-wrap">
      <div class="d-flex align-items-center"><h1 class="m-0">Receptek</h1><span class="ms-2 text-warning">({{ filteredRows.length }})</span></div>
      <div class="search-wrap"><i class="bi bi-search search-icon"></i><input v-model="searchWordInput" type="text" class="form-control search-input" placeholder="Keresés receptre..." /></div>
      <button class="btn btn-success btn-sm" @click="createHandler"><i class="bi bi-plus-lg"></i> Hozzáadás</button>
    </div>
    <div v-if="loading" class="text-warning fw-semibold">Betöltés...</div>
    <div v-else-if="filteredRows.length === 0" class="empty-list">Nincs találat</div>
    <div v-else class="recipe-grid">
      <article v-for="item in filteredRows" :key="item.id" class="recipe-card">
        <img v-if="item.picture" :src="pictureUrl(item.picture)" :alt="item.name" class="recipe-image" loading="lazy" role="button" title="Kattints a hozzávalókhoz" @click="openIngredients(item)" @error="onImageError" />
        <div class="recipe-body">
          <h5 class="m-0">{{ item.name }}</h5>
          <div class="recipe-meta">{{ mealName(item.meal_id) }} | {{ item.person }} fő</div>
          <p class="recipe-desc m-0">{{ item.description }}</p>
          <div class="d-flex gap-2 mt-2">
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

    <FormRecipe ref="form" :title="formTitle" :item="currentItem" :meals="meals" @yesEventForm="yesEventFormHandler" />
    <ConfirmModal :isOpenConfirmModal="isOpenConfirmModal" :title="confirmTitle" :message="confirmMessage" cancel="Mégsem" confirm="Igen" @cancel="closeConfirmModal" @confirm="confirmActionHandler" />
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

export default {
  name: "RecipeView",
  components: { FormRecipe, ConfirmModal },
  data() { return { loading: false, rows: [], meals: [], ingredients: [], rawIngredients: [], units: [], showIngredientsModal: false, selectedRecipe: null, mode: "create", currentItem: { id: 0, name: "", description: "", picture: "", person: 1, meal_id: 0 }, formTitle: "Új recept", isOpenConfirmModal: false, confirmTitle: "", confirmMessage: "", confirmAction: null }; },
  computed: {
    ...mapState(useSearchStore, ["searchword", "searchWord"]),
    searchWordInput: { get() { return this.searchWord; }, set(value) { this.setSearchWord(value); } },
    filteredRows() {
      if (!this.searchword) return this.rows;
      return this.rows.filter((item) => [String(item.id), item.name, item.description, this.mealName(item.meal_id)].join(" ").toLowerCase().includes(this.searchword));
    },
    selectedIngredients() { if (!this.selectedRecipe) return []; return this.ingredients.filter((x) => x.recipe_id === this.selectedRecipe.id); },
  },
  methods: {
    ...mapActions(useSearchStore, ["setSearchWord", "resetSearchWord"]),
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
  async mounted() { this.resetSearchWord(); await this.loadAll(); },
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
.search-wrap { position: relative; min-width: 320px; }
.search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #8a8a8a; }
.search-input { padding-left: 34px; border: 1px solid rgba(244, 209, 74, 0.45); background: #101216; color: #f1f1f1; }
.search-input:focus { border-color: #f4d14a; box-shadow: 0 0 0 0.2rem rgba(244, 209, 74, 0.2); background: #101216; color: #f1f1f1; }
.empty-list { border: 1px dashed rgba(244, 209, 74, 0.5); border-radius: 12px; padding: 1rem; color: #f4d14a; }
.overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.55); z-index: 1900; display: flex; align-items: flex-start; justify-content: center; padding-top: 70px; }
.ingredients-modal { width: min(600px, 92vw); background: #18191c; border: 1px solid rgba(244, 209, 74, 0.45); border-radius: 12px; color: #e9e9e9; padding: 0.9rem; box-shadow: 0 12px 28px rgba(0, 0, 0, 0.4); }
</style>
