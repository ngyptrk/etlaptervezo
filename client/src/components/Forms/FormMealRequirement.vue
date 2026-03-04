<template>
  <div>
    <Modal ref="modal" :title="title" @yesEvent="yesEventHandler">
      <div class="mb-3 row pt-2">
        <label for="meal_of_day_id" class="col-form-label col-auto pt-1 pe-0">
          Napi etkezes:
        </label>
        <div class="col">
          <select
            id="meal_of_day_id"
            v-model.number="formItem.meal_of_day_id"
            class="form-select"
            @change="clearError('meal_of_day_id')"
            required
          >
            <option :value="0" disabled>Valassz...</option>
            <option v-for="item in mealOfDays" :key="item.id" :value="item.id">
              {{ item.meal_of_day }}
            </option>
          </select>
          <div
            v-if="!serverErrors.meal_of_day_id"
            class="invalid-feedback position-absolute"
          >
            Kotelezo mezo
          </div>
          <div
            v-if="serverErrors.meal_of_day_id"
            class="invalid-feedback position-absolute d-block"
          >
            {{ serverErrors.meal_of_day_id[0] }}
          </div>
        </div>
      </div>

      <div class="mb-3 row">
        <label for="meal_id" class="col-form-label col-auto pt-1 pe-0">
          Etel tipus:
        </label>
        <div class="col">
          <select
            id="meal_id"
            v-model.number="formItem.meal_id"
            class="form-select"
            @change="clearError('meal_id')"
            required
          >
            <option :value="0" disabled>Valassz...</option>
            <option v-for="item in meals" :key="item.id" :value="item.id">
              {{ item.meal }}
            </option>
          </select>
          <div v-if="!serverErrors.meal_id" class="invalid-feedback position-absolute">
            Kotelezo mezo
          </div>
          <div
            v-if="serverErrors.meal_id"
            class="invalid-feedback position-absolute d-block"
          >
            {{ serverErrors.meal_id[0] }}
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script>
import Modal from "@/components/Modal/Modal.vue";

export default {
  name: "FormMealRequirement",
  emits: ["yesEventForm"],
  components: {
    Modal,
  },
  props: {
    title: { type: String, default: "Uj etkezes elvaras" },
    item: { type: Object, required: true },
    meals: { type: Array, default: () => [] },
    mealOfDays: { type: Array, default: () => [] },
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
