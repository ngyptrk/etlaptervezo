<template>
  <div>
    <button
      type="button"
      class="btn btn-sm crud-btn crud-btn-delete"
      @click="onClickButton()"
    >
      <i class="bi bi-trash3"></i>
    </button>
  </div>
</template>

<script>
export default {
  name: "ButtonsCrudDelete",
  props: {
    id: { type: Number, required: true },
  },
  methods: {
    onClickButton() {
      this.$emit("delete", this.id);
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

.crud-btn-delete {
  background: transparent;
  border-color: #cb2f2f;
  color: #cb2f2f;
}

.crud-btn-delete:hover {
  background: rgba(203, 47, 47, 0.12);
  border-color: #dd4141;
  color: #dd4141;
  box-shadow: 0 0 0 3px rgba(203, 47, 47, 0.18);
}
</style>
