<template>
  <div>
    <Modal ref="modal" :title="title" @yesEvent="yesEventHandler">
      <div class="mb-3 row pt-2">
        <label for="recipe_id" class="col-form-label col-auto pt-1 pe-0">Recept:</label>
        <div class="col">
          <select
            id="recipe_id"
            v-model.number="formItem.recipe_id"
            class="form-select"
            @change="clearError('recipe_id')"
            required
          >
            <option :value="0" disabled>Valassz...</option>
            <option v-for="item in recipes" :key="item.id" :value="item.id">
              {{ item.name }}
            </option>
          </select>
          <div v-if="!serverErrors.recipe_id" class="invalid-feedback position-absolute">
            Kotelezo mezo
          </div>
          <div
            v-if="serverErrors.recipe_id"
            class="invalid-feedback position-absolute d-block"
          >
            {{ serverErrors.recipe_id[0] }}
          </div>
        </div>
      </div>

      <div class="mb-3 row">
        <label for="raw_ingredient_id" class="col-form-label col-auto pt-1 pe-0">
          Nyers hozzavalo:
        </label>
        <div class="col">
          <select
            id="raw_ingredient_id"
            v-model.number="formItem.raw_ingredient_id"
            class="form-select"
            @change="clearError('raw_ingredient_id')"
            required
          >
            <option :value="0" disabled>Valassz...</option>
            <option v-for="item in rawIngredients" :key="item.id" :value="item.id">
              {{ item.raw_ingredient }}
            </option>
          </select>
          <div
            v-if="!serverErrors.raw_ingredient_id"
            class="invalid-feedback position-absolute"
          >
            Kotelezo mezo
          </div>
          <div
            v-if="serverErrors.raw_ingredient_id"
            class="invalid-feedback position-absolute d-block"
          >
            {{ serverErrors.raw_ingredient_id[0] }}
          </div>
        </div>
      </div>

      <div class="mb-3 row">
        <label for="amount" class="col-form-label col-auto pt-1 pe-0">Mennyiseg:</label>
        <div class="col">
          <input
            id="amount"
            v-model.number="formItem.amount"
            type="number"
            min="1"
            class="form-control"
            @input="clearError('amount')"
            required
          />
          <div v-if="!serverErrors.amount" class="invalid-feedback position-absolute">
            A mennyiseg kotelezo
          </div>
          <div
            v-if="serverErrors.amount"
            class="invalid-feedback position-absolute d-block"
          >
            {{ serverErrors.amount[0] }}
          </div>
        </div>
      </div>

      <div class="mb-3 row">
        <label for="unit_id" class="col-form-label col-auto pt-1 pe-0">
          Mertekegyseg:
        </label>
        <div class="col">
          <select
            id="unit_id"
            v-model.number="formItem.unit_id"
            class="form-select"
            @change="clearError('unit_id')"
            required
          >
            <option :value="0" disabled>Valassz...</option>
            <option v-for="item in units" :key="item.id" :value="item.id">
              {{ item.unit }}
            </option>
          </select>
          <div v-if="!serverErrors.unit_id" class="invalid-feedback position-absolute">
            Kotelezo mezo
          </div>
          <div
            v-if="serverErrors.unit_id"
            class="invalid-feedback position-absolute d-block"
          >
            {{ serverErrors.unit_id[0] }}
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script>
import Modal from "@/components/Modal/Modal.vue";

export default {
  name: "FormIngredient",
  emits: ["yesEventForm"],
  components: {
    Modal,
  },
  props: {
    title: { type: String, default: "Uj hozzavalo" },
    item: { type: Object, required: true },
    recipes: { type: Array, default: () => [] },
    rawIngredients: { type: Array, default: () => [] },
    units: { type: Array, default: () => [] },
  },
  data() {
    return {
      formItem: { ...this.item },
      serverErrors: {},
    };
  },
  watch: {
    item(value) {
      this.formItem = { ...value };
      this.serverErrors = {};
    },
  },
  methods: {
    show() {
      this.serverErrors = {};
      this.$refs.modal.show();
    },
    setServerErrors(errors) {
      this.serverErrors = errors;
    },
    clearError(field) {
      if (this.serverErrors[field]) {
        delete this.serverErrors[field];
      }
    },
    yesEventHandler(done) {
      this.$emit("yesEventForm", { item: this.formItem, done });
    },
  },
};
</script>
