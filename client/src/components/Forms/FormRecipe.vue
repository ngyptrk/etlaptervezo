<template>
  <div>
    <Modal ref="modal" :title="title" @yesEvent="yesEventHandler" modalSize="lg">
      <div v-if="serverErrors.general" class="alert alert-danger py-2">
        {{ serverErrors.general[0] }}
      </div>
      <div class="recipe-form-row row pt-2">
        <label for="name" class="col-form-label col-auto pt-1 pe-0">Név:</label>
        <div class="col">
          <input
            id="name"
            v-model="formItem.name"
            type="text"
            class="form-control"
            @input="clearError('name')"
          />
          <div v-if="!serverErrors.name" class="invalid-feedback recipe-error">
            Kötelező mező
          </div>
          <div v-if="serverErrors.name" class="invalid-feedback recipe-error d-block">
            {{ serverErrors.name[0] }}
          </div>
        </div>
      </div>

      <div class="recipe-form-row row">
        <label for="description" class="col-form-label col-auto pt-1 pe-0">Leírás:</label>
        <div class="col">
          <textarea
            id="description"
            v-model="formItem.description"
            class="form-control"
            rows="3"
            @input="clearError('description')"
          ></textarea>
          <div
            v-if="!serverErrors.description"
            class="invalid-feedback recipe-error"
          >
            Kötelező mező
          </div>
          <div
            v-if="serverErrors.description"
            class="invalid-feedback recipe-error d-block"
          >
            {{ serverErrors.description[0] }}
          </div>
        </div>
      </div>

      <div class="recipe-form-row row">
        <label for="picture" class="col-form-label col-auto pt-1 pe-0">Kép (PNG):</label>
        <div class="col">
          <input
            id="picture"
            type="file"
            accept=".png,image/png"
            class="form-control"
            :required="Number(formItem.id) <= 0"
            @change="onPictureChange"
          />
          <div v-if="formItem.picture" class="form-text text-warning">
            Jelenlegi kép: {{ formItem.picture }}
          </div>
          <div v-if="!serverErrors.picture" class="invalid-feedback recipe-error">
            Kötelező mező
          </div>
          <div
            v-if="serverErrors.picture"
            class="invalid-feedback recipe-error d-block"
          >
            {{ serverErrors.picture[0] }}
          </div>
        </div>
      </div>

      <div class="recipe-form-row row">
        <label for="person" class="col-form-label col-auto pt-1 pe-0">Adag:</label>
        <div class="col">
          <input
            id="person"
            v-model.number="formItem.person"
            type="number"
            min="1"
            class="form-control"
            @input="clearError('person')"
          />
          <div v-if="!serverErrors.person" class="invalid-feedback recipe-error">
            Kötelező mező
          </div>
          <div v-if="serverErrors.person" class="invalid-feedback recipe-error d-block">
            {{ serverErrors.person[0] }}
          </div>
        </div>
      </div>

      <div class="recipe-form-row row">
        <label for="meal_id" class="col-form-label col-auto pt-1 pe-0">Étkezés:</label>
        <div class="col">
          <select
            id="meal_id"
            v-model.number="formItem.meal_id"
            class="form-select"
            @change="clearError('meal_id')"
          >
            <option :value="0" disabled>Válassz...</option>
            <option v-for="item in meals" :key="item.id" :value="item.id">
              {{ item.meal }}
            </option>
          </select>
          <div v-if="!serverErrors.meal_id" class="invalid-feedback recipe-error">
            Kötelező mező
          </div>
          <div
            v-if="serverErrors.meal_id"
            class="invalid-feedback recipe-error d-block"
          >
            {{ serverErrors.meal_id[0] }}
          </div>
        </div>
      </div>

      <div class="ingredients-divider"></div>

      <div class="ingredients-section">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h6 class="m-0 ingredients-title">Hozzávalók</h6>
        </div>

        <div v-if="Number(formItem.id) <= 0" class="ingredients-empty">
          Előbb mentsd a receptet, utána szerkesztheted a hozzávalókat.
        </div>

        <div v-else>
          <div v-if="recipeIngredients.length === 0" class="ingredients-empty mb-2">
            Még nincs hozzáadott hozzávaló.
          </div>

          <div v-else class="ingredients-list mb-3">
            <div v-for="ing in recipeIngredients" :key="ing.id" class="ingredients-row">
              <div class="ingredients-name">
                {{ ing.amount }} {{ unitName(ing.unit_id) }} - {{ rawIngredientName(ing.raw_ingredient_id) }}
              </div>
              <div class="ingredients-actions">
                <button type="button" class="btn btn-sm btn-outline-info" @click="startIngredientEdit(ing)">
                  Módosítás
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" @click="deleteIngredient(ing)">
                  Törlés
                </button>
              </div>
            </div>
          </div>

          <div class="ingredient-form">
            <div class="row g-2 align-items-end">
              <div class="col-md-5">
                <label for="form_raw_ingredient_id" class="form-label small mb-1">Nyers hozzávaló</label>
                <select
                  id="form_raw_ingredient_id"
                  v-model.number="ingredientForm.raw_ingredient_id"
                  class="form-select"
                  @change="clearIngredientError('raw_ingredient_id')"
                >
                  <option :value="0" disabled>Válassz...</option>
                  <option v-for="item in rawIngredients" :key="item.id" :value="item.id">
                    {{ item.raw_ingredient }}
                  </option>
                </select>
                <div v-if="ingredientErrors.raw_ingredient_id" class="invalid-feedback d-block">
                  {{ ingredientErrors.raw_ingredient_id[0] }}
                </div>
              </div>
              <div class="col-md-3">
                <label for="form_amount" class="form-label small mb-1">Mennyiség</label>
                <input
                  id="form_amount"
                  v-model.number="ingredientForm.amount"
                  type="number"
                  min="1"
                  class="form-control"
                  @input="clearIngredientError('amount')"
                />
                <div v-if="ingredientErrors.amount" class="invalid-feedback d-block">
                  {{ ingredientErrors.amount[0] }}
                </div>
              </div>
              <div class="col-md-4">
                <label for="form_unit_id" class="form-label small mb-1">Mértékegység</label>
                <select
                  id="form_unit_id"
                  v-model.number="ingredientForm.unit_id"
                  class="form-select"
                  @change="clearIngredientError('unit_id')"
                >
                  <option :value="0" disabled>Válassz...</option>
                  <option v-for="item in units" :key="item.id" :value="item.id">
                    {{ item.unit }}
                  </option>
                </select>
                <div v-if="ingredientErrors.unit_id" class="invalid-feedback d-block">
                  {{ ingredientErrors.unit_id[0] }}
                </div>
              </div>
            </div>

            <div class="d-flex gap-2 mt-2">
              <button type="button" class="btn btn-sm btn-outline-warning" @click="saveIngredient" :disabled="ingredientSaving">
                {{ ingredientMode === "create" ? "Hozzáadás" : "Mentés" }}
              </button>
              <button v-if="ingredientMode === 'update'" type="button" class="btn btn-sm btn-outline-light" @click="cancelIngredientEdit">
                Mégsem
              </button>
            </div>
            <div v-if="ingredientErrors.general" class="text-danger mt-2 small">
              {{ ingredientErrors.general[0] }}
            </div>
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script>
import Modal from "@/components/Modal/Modal.vue";
import ingredientService from "@/api/ingredientService";

export default {
  name: "FormRecipe",
  emits: ["yesEventForm", "ingredientsChanged"],
  components: {
    Modal,
  },
  props: {
    title: { type: String, default: "Új recept" },
    item: { type: Object, required: true },
    meals: { type: Array, default: () => [] },
    ingredients: { type: Array, default: () => [] },
    rawIngredients: { type: Array, default: () => [] },
    units: { type: Array, default: () => [] },
  },
  data() {
    return {
      formItem: { ...this.item },
      serverErrors: {},
      pictureFile: null,
      ingredientForm: { id: 0, recipe_id: 0, raw_ingredient_id: 0, amount: 1, unit_id: 0 },
      ingredientMode: "create",
      ingredientErrors: {},
      ingredientSaving: false,
    };
  },
  computed: {
    recipeIngredients() {
      const recipeId = Number(this.formItem.id) || 0;
      if (!recipeId) return [];
      return this.ingredients.filter((item) => Number(item.recipe_id) === recipeId);
    },
  },
  watch: {
    item(value) {
      this.formItem = { ...value };
      this.serverErrors = {};
      this.pictureFile = null;
      this.resetIngredientForm();
    },
    ingredients() {
      if (this.ingredientMode === "update") {
        const current = this.recipeIngredients.find((ing) => ing.id === this.ingredientForm.id);
        if (!current) {
          this.resetIngredientForm();
        }
      }
    },
  },
  methods: {
    show() {
      this.serverErrors = {};
      this.pictureFile = null;
      this.resetIngredientForm();
      this.$refs.modal.show();
    },
    setServerErrors(errors) {
      this.serverErrors = errors;
    },
    clearError(field) {
      if (this.serverErrors[field]) {
        delete this.serverErrors[field];
      }
      if (this.serverErrors.general) {
        delete this.serverErrors.general;
      }
    },
    yesEventHandler(done) {
      this.$emit("yesEventForm", { item: { ...this.formItem, pictureFile: this.pictureFile }, done });
    },
    onPictureChange(event) {
      const file = event?.target?.files?.[0] ?? null;
      this.pictureFile = file;
      this.clearError("picture");
    },
    resetIngredientForm() {
      this.ingredientMode = "create";
      this.ingredientErrors = {};
      const rawId = this.rawIngredients[0]?.id ?? 0;
      const unitId = this.units[0]?.id ?? 0;
      this.ingredientForm = {
        id: 0,
        recipe_id: Number(this.formItem.id) || 0,
        raw_ingredient_id: rawId,
        amount: 1,
        unit_id: unitId,
      };
    },
    rawIngredientName(id) {
      return this.rawIngredients.find((x) => x.id === id)?.raw_ingredient ?? `#${id}`;
    },
    unitName(id) {
      return this.units.find((x) => x.id === id)?.unit ?? `#${id}`;
    },
    clearIngredientError(field) {
      if (this.ingredientErrors[field]) {
        delete this.ingredientErrors[field];
      }
      if (this.ingredientErrors.general) {
        delete this.ingredientErrors.general;
      }
    },
    startIngredientEdit(ing) {
      this.ingredientMode = "update";
      this.ingredientErrors = {};
      this.ingredientForm = {
        id: ing.id,
        recipe_id: ing.recipe_id,
        raw_ingredient_id: ing.raw_ingredient_id,
        amount: ing.amount,
        unit_id: ing.unit_id,
      };
    },
    cancelIngredientEdit() {
      this.resetIngredientForm();
    },
    async saveIngredient() {
      if (Number(this.formItem.id) <= 0) return;
      this.ingredientSaving = true;
      this.ingredientErrors = {};
      try {
        const payload = {
          recipe_id: Number(this.formItem.id),
          raw_ingredient_id: Number(this.ingredientForm.raw_ingredient_id),
          amount: Number(this.ingredientForm.amount),
          unit_id: Number(this.ingredientForm.unit_id),
        };
        if (this.ingredientMode === "create") {
          await ingredientService.create(payload);
        } else {
          await ingredientService.update(this.ingredientForm.id, payload);
        }
        this.$emit("ingredientsChanged");
        this.resetIngredientForm();
      } catch (err) {
        const apiErrors = err?.response?.data?.errors ?? {};
        const apiMessage = err?.response?.data?.message;
        if (err?.response?.status === 422) {
          this.ingredientErrors = apiErrors;
        } else {
          this.ingredientErrors = { general: [apiMessage || "Mentés sikertelen. Próbáld újra."] };
        }
      } finally {
        this.ingredientSaving = false;
      }
    },
    async deleteIngredient(ing) {
      if (!ing?.id) return;
      const ok = window.confirm("Biztosan törölni szeretnéd ezt a hozzávalót?");
      if (!ok) return;
      try {
        await ingredientService.delete(ing.id);
        this.$emit("ingredientsChanged");
        if (this.ingredientMode === "update" && this.ingredientForm.id === ing.id) {
          this.resetIngredientForm();
        }
      } catch (err) {
        const apiMessage = err?.response?.data?.message;
        this.ingredientErrors = { general: [apiMessage || "Törlés sikertelen. Próbáld újra."] };
      }
    },
  },
};
</script>

<style scoped>
.recipe-form-row {
  margin-bottom: 1rem;
}

.recipe-form-row > .col {
  min-width: 0;
}

.recipe-form-row > .col-form-label {
  flex: 0 0 88px;
  width: 88px;
}

.recipe-error {
  margin-top: 0.35rem;
  line-height: 1.25;
  white-space: normal;
}

.ingredients-divider {
  height: 1px;
  background: rgba(244, 209, 74, 0.3);
  margin: 1rem 0 0.75rem;
}

.ingredients-title {
  color: #f4d14a;
  font-weight: 700;
}

.ingredients-empty {
  border: 1px dashed rgba(244, 209, 74, 0.4);
  border-radius: 10px;
  padding: 0.6rem 0.75rem;
  color: #f4d14a;
}

.ingredients-list {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.ingredients-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 0.35rem 0.5rem;
  border: 1px solid rgba(244, 209, 74, 0.25);
  border-radius: 10px;
  background: rgba(10, 10, 12, 0.35);
}

.ingredients-name {
  color: #e9e9e9;
  font-size: 0.92rem;
}

.ingredients-actions {
  display: inline-flex;
  gap: 0.4rem;
}

.ingredient-form {
  border: 1px solid rgba(244, 209, 74, 0.25);
  border-radius: 12px;
  padding: 0.75rem;
  background: rgba(12, 12, 14, 0.45);
}
</style>
