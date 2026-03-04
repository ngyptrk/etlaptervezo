<template>
  <transition name="slide-fade">
    <div v-if="show" class="top-mini-modal" role="status" aria-live="polite">
      <i class="bi bi-check-circle-fill me-2"></i>
      <span>{{ message }}</span>
    </div>
  </transition>
</template>

<script>
export default {
  name: "TopMiniModal",
  props: {
    show: { type: Boolean, default: false },
    message: { type: String, default: "Sikeres művelet" },
  },
};
</script>

<style scoped>
.top-mini-modal {
  position: fixed;
  top: 18px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 2000;
  background: linear-gradient(180deg, #f4d14a, #edc534);
  color: #111;
  font-weight: 700;
  border: 1px solid #ffe680;
  border-radius: 999px;
  padding: 0.55rem 1rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.28);
  display: flex;
  align-items: center;
}

.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.22s ease;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  opacity: 0;
  transform: translate(-50%, -12px);
}
</style>
