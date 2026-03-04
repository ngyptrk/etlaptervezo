<template>
  <div>
    <Modal ref="modal" :title="title" @yesEvent="yesEventHandler">
      <div class="mb-4 row pt-2">
        <label for="meal" class="col-form-label col-auto pt-1 pe-0">
          Etkezes:
        </label>
        <div class="col">
          <input
            id="meal"
            v-model="formItem.meal"
            type="text"
            class="form-control"
            @input="clearError('meal')"
            required
          />
          <div v-if="!serverErrors.meal" class="invalid-feedback position-absolute">
            Az etkezes neve kotelezo
          </div>
          <div
            v-if="serverErrors.meal"
            class="invalid-feedback position-absolute d-block"
          >
            {{ serverErrors.meal[0] }}
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script>
import Modal from "@/components/Modal/Modal.vue";

export default {
  name: "FormMeal",
  emits: ["yesEventForm"],
  components: {
    Modal,
  },
  props: {
    title: { type: String, default: "Uj etkezes" },
    item: { type: Object, required: true },
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
