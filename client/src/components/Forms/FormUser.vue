<template>
  <div>
    <Modal ref="modal" :title="title" @yesEvent="yesEventHandler">
      <div class="mb-4 row pt-2">
        <label for="name" class="col-form-label col-auto pt-1 pe-0">Név:</label>
        <div class="col">
          <input
            id="name"
            v-model="formItem.name"
            type="text"
            class="form-control"
            @input="clearError('name')"
            required
          />
          <div v-if="!serverErrors.name" class="invalid-feedback position-absolute">
            A név kötelező
          </div>
          <div
            v-if="serverErrors.name"
            class="invalid-feedback position-absolute d-block"
          >
            {{ serverErrors.name[0] }}
          </div>
        </div>
      </div>

      <div class="mb-4 row pt-2">
        <label for="email" class="col-form-label col-auto pt-1 pe-0">Email:</label>
        <div class="col">
          <input
            id="email"
            v-model="formItem.email"
            type="email"
            class="form-control"
            @input="clearError('email')"
            required
          />
          <div v-if="!serverErrors.email" class="invalid-feedback position-absolute">
            Az email kötelező
          </div>
          <div
            v-if="serverErrors.email"
            class="invalid-feedback position-absolute d-block"
          >
            {{ serverErrors.email[0] }}
          </div>
        </div>
      </div>

      <div class="mb-4 row pt-2">
        <label for="role" class="col-form-label col-auto pt-1 pe-0">Szerepkör:</label>
        <div class="col">
          <select
            id="role"
            v-model.number="formItem.role"
            class="form-select"
            style="width: 140px"
            :disabled="disableRole"
            @change="clearError('role')"
          >
            <option :value="1">Admin</option>
            <option :value="2">Tanár</option>
            <option :value="3">Diák</option>
          </select>
          <small v-if="disableRole" class="text-warning">
            Saját admin szerepkör nem módosítható.
          </small>
          <div v-if="serverErrors.role" class="invalid-feedback position-absolute d-block">
            {{ serverErrors.role[0] }}
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script>
import Modal from "@/components/Modal/Modal.vue";

export default {
  emits: ["yesEventForm"],
  name: "FormUser",
  components: {
    Modal,
  },
  props: {
    title: { type: String, default: "Felhasználó módosítása" },
    item: { type: Object, required: true },
    disableRole: { type: Boolean, default: false },
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
