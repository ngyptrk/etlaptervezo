<template>
  <div>
    <button
      type="button"
      class="btn btn-sm crud-btn crud-btn-update"
      @click="onClickButton()"
    >
      <i class="bi bi-pencil"></i>
    </button>
  </div>
</template>

<script>
export default {
  name: "ButtonsCrudUpdate",
  props: {
    id: { type: Number, required: true },
  },
  methods: {
    onClickButton() {
      this.$emit("update", this.id);
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

.crud-btn-update {
  background: transparent;
  border-color: #2f95bf;
  color: #2f95bf;
}

.crud-btn-update:hover {
  background: rgba(47, 149, 191, 0.12);
  border-color: #3ea9d6;
  color: #3ea9d6;
  box-shadow: 0 0 0 3px rgba(47, 149, 191, 0.2);
}
</style>
