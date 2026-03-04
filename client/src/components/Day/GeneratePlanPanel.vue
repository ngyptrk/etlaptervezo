<template>
  <section class="plan-generator mb-4">
    <div class="row g-3 align-items-end">
      <div class="col-12 col-md-4">
        <label class="form-label">Generálás típusa</label>
        <select v-model="modeModel" class="form-select plan-input">
          <option value="days">Napok</option>
          <option value="weeks">Hetek</option>
        </select>
      </div>

      <div class="col-12 col-md-3">
        <label class="form-label">Darabszám</label>
        <input
          v-model.number="amountModel"
          type="number"
          min="1"
          max="84"
          class="form-control plan-input"
        />
      </div>

      <div class="col-12 col-md-5 d-flex gap-2">
        <button
          type="button"
          class="btn btn-warning plan-btn"
          :disabled="loading"
          @click="onGenerate"
        >
          <span v-if="!loading">Generálás</span>
          <span v-else>Generálás folyamatban...</span>
        </button>

        <button
          type="button"
          class="btn btn-outline-light plan-btn-secondary"
          :disabled="loading"
          @click="$emit('refresh')"
        >
          Frissítés
        </button>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "GeneratePlanPanel",
  emits: ["generate", "refresh"],
  props: {
    loading: { type: Boolean, default: false },
    mode: { type: String, default: "weeks" },
    amount: { type: Number, default: 1 },
  },
  data() {
    return {
      modeModel: this.mode,
      amountModel: this.amount,
    };
  },
  watch: {
    mode(value) {
      this.modeModel = value;
    },
    amount(value) {
      this.amountModel = value;
    },
  },
  methods: {
    onGenerate() {
      const amount = Number(this.amountModel || 1);
      this.$emit("generate", {
        mode: this.modeModel,
        amount: amount < 1 ? 1 : amount,
      });
    },
  },
};
</script>

<style scoped>
.plan-generator {
  border: 1px solid rgba(244, 209, 74, 0.4);
  border-radius: 12px;
  padding: 0.9rem;
  background: rgba(20, 20, 22, 0.82);
}

.form-label {
  color: #f4d14a;
  font-weight: 600;
}

.plan-input {
  background: #d8d8da;
  border-color: #b2b2b6;
  color: #151515;
}

.plan-btn {
  font-weight: 700;
  min-width: 140px;
}

.plan-btn-secondary {
  border-color: rgba(244, 209, 74, 0.5);
  color: #f4d14a;
}

.plan-btn-secondary:hover {
  background: rgba(244, 209, 74, 0.15);
  color: #ffe680;
  border-color: #ffe680;
}
</style>
