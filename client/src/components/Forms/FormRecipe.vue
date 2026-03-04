<template>
  <div>
    <Modal ref="modal" :title="title" @yesEvent="yesEventHandler" modalSize="lg">
      <div v-if="serverErrors.general" class="alert alert-danger py-2">
        {{ serverErrors.general[0] }}
      </div>
      <div class="mb-3 row pt-2">
        <label for="name" class="col-form-label col-auto pt-1 pe-0">Név:</label>
        <div class="col">
          <input
            id="name"
            v-model="formItem.name"
            type="text"
            class="form-control"
            @input="clearError('name')"
          />
          <div v-if="!serverErrors.name" class="invalid-feedback position-absolute">
            Kötelező mező
          </div>
          <div v-if="serverErrors.name" class="invalid-feedback position-absolute d-block">
            {{ serverErrors.name[0] }}
          </div>
        </div>
      </div>

      <div class="mb-3 row">
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
            class="invalid-feedback position-absolute"
          >
            Kötelező mező
          </div>
          <div
            v-if="serverErrors.description"
            class="invalid-feedback position-absolute d-block"
          >
            {{ serverErrors.description[0] }}
          </div>
        </div>
      </div>

      <div class="mb-3 row">
        <label for="picture" class="col-form-label col-auto pt-1 pe-0">Kép URL:</label>
        <div class="col">
          <input
            id="picture"
            v-model="formItem.picture"
            type="text"
            class="form-control"
            @input="clearError('picture')"
          />
          <div v-if="!serverErrors.picture" class="invalid-feedback position-absolute">
            Kötelező mező
          </div>
          <div
            v-if="serverErrors.picture"
            class="invalid-feedback position-absolute d-block"
          >
            {{ serverErrors.picture[0] }}
          </div>
        </div>
      </div>

      <div class="mb-3 row">
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
          <div v-if="!serverErrors.person" class="invalid-feedback position-absolute">
            Kötelező mező
          </div>
          <div v-if="serverErrors.person" class="invalid-feedback position-absolute d-block">
            {{ serverErrors.person[0] }}
          </div>
        </div>
      </div>

      <div class="mb-3 row">
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
          <div v-if="!serverErrors.meal_id" class="invalid-feedback position-absolute">
            Kötelező mező
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
  name: "FormRecipe",
  emits: ["yesEventForm"],
  components: {
    Modal,
  },
  props: {
    title: { type: String, default: "Új recept" },
    item: { type: Object, required: true },
    meals: { type: Array, default: () => [] },
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
      if (this.serverErrors.general) {
        delete this.serverErrors.general;
      }
    },
    yesEventHandler(done) {
      this.$emit("yesEventForm", { item: this.formItem, done });
    },
  },
};
</script>
