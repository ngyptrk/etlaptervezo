<template>
  <div>
    <Modal ref="modal" :title="title" @yesEvent="yesEventHandler">
      <div class="mb-4 row pt-2">
        <label for="raw_ingredient" class="col-form-label col-auto pt-1 pe-0">
          Alapanyag:
        </label>
        <div class="col">
          <input
            id="raw_ingredient"
            v-model="formItem.raw_ingredient"
            type="text"
            class="form-control"
            @input="clearError('raw_ingredient')"
            required
          />
          <div
            v-if="!serverErrors.raw_ingredient"
            class="invalid-feedback position-absolute"
          >
            Az alapanyag neve kotelezo
          </div>
          <div
            v-if="serverErrors.raw_ingredient"
            class="invalid-feedback position-absolute d-block"
          >
            {{ serverErrors.raw_ingredient[0] }}
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script>
import Modal from "@/components/Modal/Modal.vue";

export default {
  name: "FormRawIngredient",
  emits: ["yesEventForm"],
  components: {
    Modal,
  },
  props: {
    title: { type: String, default: "Uj nyers hozzavalo" },
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
