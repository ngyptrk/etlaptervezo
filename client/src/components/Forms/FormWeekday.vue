<template>
  <div>
    <Modal ref="modal" :title="title" @yesEvent="yesEventHandler">
      <div class="mb-4 row pt-2">
        <label for="day" class="col-form-label col-auto pt-1 pe-0">Nap:</label>
        <div class="col">
          <input
            id="day"
            v-model="formItem.day"
            type="text"
            class="form-control"
            @input="clearError('day')"
            required
          />
          <div v-if="!serverErrors.day" class="invalid-feedback position-absolute">
            A nap kötelező
          </div>
          <div
            v-if="serverErrors.day"
            class="invalid-feedback position-absolute d-block"
          >
            {{ serverErrors.day[0] }}
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script>
import Modal from "@/components/Modal/Modal.vue";

export default {
  name: "FormWeekday",
  emits: ["yesEventForm"],
  components: {
    Modal,
  },
  props: {
    title: { type: String, default: "Új nap felvitele" },
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
    hide() {
      this.$refs.modal.hide();
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
