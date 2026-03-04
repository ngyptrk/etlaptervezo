<template>
  <div>
    <button
      type="button"
      class="btn btn-sm crud-btn crud-btn-create"
      @click="onClickButton()"
    >
      <i class="bi bi-plus-lg"></i>
    </button>
  </div>
</template>

<script>
export default {
  name: "ButtonsCrudCreate",
  methods: {
    onClickButton() {
      this.$emit("create");
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

.crud-btn-create {
  background: #1f8f5b;
  border-color: #1f8f5b;
  color: #ffffff;
}

.crud-btn-create:hover {
  background: #27a66a;
  border-color: #27a66a;
  box-shadow: 0 0 0 3px rgba(39, 166, 106, 0.2);
}
</style>
