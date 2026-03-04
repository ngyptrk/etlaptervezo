<template>
  <div>
    <button
      type="button"
      class="btn btn-sm crud-btn crud-btn-password"
      @click="onClickButton()"
    >
      <i class="bi bi-key"></i>
    </button>
  </div>
</template>

<script>
export default {
  name: "ButtonsCrudPassword",
  props: {
    id: { type: Number, required: true },
  },
  methods: {
    onClickButton() {
      this.$emit("passwordChange", this.id);
    },
  },
};
</script>

<style scoped>
.crud-btn {
  border-radius: 8px;
  border-width: 1px;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.crud-btn:hover {
  transform: translateY(-1px);
}

.crud-btn-password {
  background: transparent;
  border-color: #f4d14a;
  color: #c8a929;
}

.crud-btn-password:hover {
  background: rgba(244, 209, 74, 0.14);
  border-color: #ffe680;
  color: #f4d14a;
  box-shadow: 0 0 0 3px rgba(244, 209, 74, 0.2);
}
</style>
