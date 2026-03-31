<template>
  <div v-if="isOpenConfirmModal" class="confirm-overlay" @click.self="$emit('cancel')">
    <div class="confirm-dialog" role="dialog" aria-modal="true">
      <div class="confirm-header">
        <h5 class="confirm-title">{{ title }}</h5>
        <button type="button" class="confirm-close" aria-label="Bezárás" @click="$emit('cancel')">
          ×
        </button>
      </div>
      <div class="confirm-body">
        <p>{{ message }}</p>
      </div>
      <div class="confirm-footer">
        <button type="button" class="btn btn-outline-warning" @click="$emit('cancel')">
          {{ cancel }}
        </button>
        <button type="button" class="btn btn-warning fw-semibold text-dark" @click="$emit('confirm')">
          {{ confirm }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    isOpenConfirmModal: Boolean,
    title: { type: String, default: "Megerősítés" },
    message: {
      type: String,
      default: "Biztosan törölni szeretnéd ezt az elemet?",
    },
    cancel: { type: String, default: "Nem" },
    confirm: { type: String, default: "Igen" },
  },
  watch: {
    isOpenConfirmModal(isOpen) {
      if (isOpen) {
        this.applyScrollLock();
      } else {
        this.clearScrollLock();
      }
    },
  },
  beforeUnmount() {
    this.clearScrollLock();
  },
  methods: {
    applyScrollLock() {
      // no-op: avoid layout jump
    },
    clearScrollLock() {
      // no-op: avoid layout jump
    },
  },
};
</script>

<style scoped>
.confirm-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.55);
  z-index: 3000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}

.confirm-dialog {
  width: min(520px, 96vw);
  background: linear-gradient(180deg, #181a1f, #121317);
  border: 1px solid rgba(244, 209, 74, 0.5);
  border-radius: 12px;
  box-shadow: 0 16px 34px rgba(0, 0, 0, 0.5), 0 0 20px rgba(244, 209, 74, 0.15);
  color: #f0f0f0;
}

.confirm-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(244, 209, 74, 0.28);
  padding: 0.75rem 1rem;
}

.confirm-title {
  margin: 0;
  font-weight: 700;
  color: #f4d14a;
}

.confirm-close {
  border: none;
  background: transparent;
  color: #f4d14a;
  font-size: 1.5rem;
  line-height: 1;
  cursor: pointer;
}

.confirm-body {
  padding: 1rem;
}

.confirm-body p {
  margin: 0;
}

.confirm-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.55rem;
  padding: 0.75rem 1rem 1rem;
  border-top: 1px solid rgba(244, 209, 74, 0.2);
}
</style>
