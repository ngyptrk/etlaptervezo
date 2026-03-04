<template>
  <div>
    <div class="d-flex align-items-center mb-3">
      <h1 class="m-0">Receptek</h1>
      <span class="ms-2 text-warning">({{ rows.length }})</span>
      <button class="btn btn-success btn-sm ms-3" @click="createHandler">
        <i class="bi bi-plus-lg"></i> Hozzáadás
      </button>
    </div>

    <div v-if="loading" class="text-warning fw-semibold">Betöltés...</div>
    <div v-else-if="rows.length === 0" class="empty-list">Nincs találat</div>

    <div v-else class="recipe-grid">
      <article v-for="item in rows" :key="item.id" class="recipe-card">
        <img
          v-if="item.picture"
          :src="pictureUrl(item.picture)"
          :alt="item.name"
          class="recipe-image"
          loading="lazy"
          role="button"
          title="Kattints a hozzávalókhoz"
          @click="openIngredients(item)"
          @error="onImageError"
        />
        <div class="recipe-body">
          <h5 class="m-0">{{ item.name }}</h5>
          <div class="recipe-meta">{{ mealName(item.meal_id) }} | {{ item.person }} fő</div>
          <p class="recipe-desc m-0">{{ item.description }}</p>
          <button class="btn btn-sm btn-outline-info mt-2" @click="updateHandler(item)">
            <i class="bi bi-pencil"></i> Módosítás
          </button>
        </div>
      </article>
    </div>

    <div v-if="showIngredientsModal" class="overlay" @click.self="closeIngredients">
      <div class="ingredients-modal">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="m-0 text-warning">{{ selectedRecipe?.name }} hozzávalói</h5>
          <button class="btn btn-sm btn-outline-warning" @click="closeIngredients">
            Bezárás
          </button>
        </div>

        <div v-if="selectedIngredients.length === 0" class="text-warning">
          Nincs hozzávaló megadva ehhez a recepthez.
        </div>

        <ul v-else class="mb-0 ps-3">
          <li v-for="ing in selectedIngredients" :key="ing.id" class="mb-1">
            {{ ing.amount }} {{ unitName(ing.unit_id) }} - {{ rawIngredientName(ing.raw_ingredient_id) }}
          </li>
        </ul>
      </div>
    </div>

    <FormRecipe
      ref="form"
      :title="formTitle"
      :item="currentItem"
      :meals="meals"
      @yesEventForm="yesEventFormHandler"
    />
  </div>
</template>

<script>
import recipeService from "@/api/recipeService";
import mealService from "@/api/mealService";
import ingredientService from "@/api/ingredientService";
import rawIngredientService from "@/api/rawIngredientService";
import unitService from "@/api/unitService";
import FormRecipe from "@/components/Forms/FormRecipe.vue";

export default {
  name: "RecipeView",
  components: {
    FormRecipe,
  },
  data() {
    return {
      loading: false,
      rows: [],
      meals: [],
      ingredients: [],
      rawIngredients: [],
      units: [],
      showIngredientsModal: false,
      selectedRecipe: null,
      mode: "create",
      currentItem: {
        id: 0,
        name: "",
        description: "",
        picture: "",
        person: 1,
        meal_id: 0,
      },
      formTitle: "Új recept",
    };
  },
  computed: {
    selectedIngredients() {
      if (!this.selectedRecipe) return [];
      return this.ingredients.filter((x) => x.recipe_id === this.selectedRecipe.id);
    },
  },
  methods: {
    mealName(id) {
      return this.meals.find((x) => x.id === id)?.meal ?? `#${id}`;
    },
    rawIngredientName(id) {
      return this.rawIngredients.find((x) => x.id === id)?.raw_ingredient ?? `#${id}`;
    },
    unitName(id) {
      return this.units.find((x) => x.id === id)?.unit ?? `#${id}`;
    },
    pictureUrl(path) {
      const apiUrl = import.meta.env.VITE_API_URL || "";
      const origin = apiUrl.replace(/\/api\/?$/, "");
      const normalized = String(path)
        .split("/")
        .map((segment) => encodeURIComponent(segment))
        .join("/");
      return origin ? `${origin}/${normalized}` : `/${normalized}`;
    },
    onImageError(event) {
      event.target.style.display = "none";
    },
    openIngredients(recipe) {
      this.selectedRecipe = recipe;
      this.showIngredientsModal = true;
    },
    closeIngredients() {
      this.showIngredientsModal = false;
      this.selectedRecipe = null;
    },
    toPayload(item) {
      return {
        name: String(item.name ?? "").trim(),
        description: String(item.description ?? "").trim(),
        picture: String(item.picture ?? "").trim(),
        person: Number(item.person ?? 1),
        meal_id: Number(item.meal_id ?? 0),
      };
    },
    createHandler() {
      this.mode = "create";
      this.formTitle = "Új recept";
      this.currentItem = {
        id: 0,
        name: "",
        description: "",
        picture: "",
        person: 1,
        meal_id: this.meals[0]?.id ?? 0,
      };
      this.$refs.form.show();
    },
    updateHandler(item) {
      this.mode = "update";
      this.formTitle = "Recept módosítás";
      this.currentItem = { ...item };
      this.$refs.form.show();
    },
    async yesEventFormHandler({ item, done }) {
      try {
        const payload = this.toPayload(item);
        let response;

        if (this.mode === "create") {
          response = await recipeService.create(payload);
          if (response?.data) {
            this.rows.unshift(response.data);
          }
        } else {
          response = await recipeService.update(item.id, payload);
          const index = this.rows.findIndex((x) => x.id === item.id);
          if (index !== -1 && response?.data) {
            this.rows[index] = response.data;
          }
        }

        done(true);
        this.loadAll().catch(() => {});
      } catch (err) {
        const apiErrors = err?.response?.data?.errors ?? {};
        const apiMessage = err?.response?.data?.message;

        if (err.response && err.response.status === 422) {
          this.$refs.form?.setServerErrors(apiErrors);
        } else {
          this.$refs.form?.setServerErrors({
            general: [apiMessage || "Mentés sikertelen. Próbáld újra."],
          });
        }
        done(false);
      }
    },
    async loadAll() {
      this.loading = true;
      try {
        const [recipeRes, mealRes, ingredientRes, rawRes, unitRes] = await Promise.all([
          recipeService.getAll(),
          mealService.getAll(),
          ingredientService.getAll(),
          rawIngredientService.getAll(),
          unitService.getAll(),
        ]);
        this.rows = recipeRes.data ?? [];
        this.meals = mealRes.data ?? [];
        this.ingredients = ingredientRes.data ?? [];
        this.rawIngredients = rawRes.data ?? [];
        this.units = unitRes.data ?? [];
      } finally {
        this.loading = false;
      }
    },
  },
  async mounted() {
    await this.loadAll();
  },
};
</script>

<style scoped>
.recipe-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 12px;
}

.recipe-card {
  border: 1px solid rgba(244, 209, 74, 0.35);
  border-radius: 12px;
  overflow: hidden;
  background: #d4d4d6;
  color: #151515;
}

.recipe-image {
  width: 100%;
  height: 160px;
  object-fit: cover;
}

.recipe-body {
  padding: 0.7rem;
}

.recipe-meta {
  font-size: 0.85rem;
  font-weight: 600;
  color: #3b3b3b;
  margin-top: 0.2rem;
  margin-bottom: 0.4rem;
}

.recipe-desc {
  font-size: 0.9rem;
  color: #2f2f2f;
}

.empty-list {
  border: 1px dashed rgba(244, 209, 74, 0.5);
  border-radius: 12px;
  padding: 1rem;
  color: #f4d14a;
}

.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  z-index: 1900;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding-top: 70px;
}

.ingredients-modal {
  width: min(600px, 92vw);
  background: #18191c;
  border: 1px solid rgba(244, 209, 74, 0.45);
  border-radius: 12px;
  color: #e9e9e9;
  padding: 0.9rem;
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.4);
}
</style>
