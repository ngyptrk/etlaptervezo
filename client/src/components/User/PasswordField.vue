<template>
  <div class="mb-3">
    <!-- Label -->
    <label v-if="label" :for="labelId" :class="labelClasses">{{ label }}:</label>

    <!-- Input és toggle -->
    <div class="input-group">
      <input
        :type="showPassword ? 'text' : 'password'"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :ref="inputRef || null"
        :class="inputClasses"
        :id="labelId"
        :placeholder="placeholder"
        required
      />
      <button type="button" class="toggle-btn" @click="showPassword = !showPassword">
        <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
      </button>

      <!-- Hibák -->
      <div
        class="invalid-feedback"
        :class="{ 'd-block': showError && hasError }"
      >
        {{ serverErrors?.password?.[0] || passwordErrorMessage || "A jelszo kotelezo" }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    modelValue: { type: String },
    label: { type: String, default: "Jelszó" },
    labelId: { type: String, default: "" },
    placeholder: { type: String, default: "Írd be a jelszavad" },
    inputRef: { type: String, default: "" },
    inputClass: { type: String, default: "" },
    labelClass: { type: String, default: "" },
    passwordErrorMessage: { type: String, default: "" },
    showError: { type: Boolean, default: false },
    serverErrors: { type: Object, default: () => ({}) },
  },
  data() {
    return {
      showPassword: false,
    };
  },
  computed: {
    hasError() {
      return Boolean(this.passwordErrorMessage || this.serverErrors?.password);
    },
    inputClasses() {
      return [
        "form-control",
        this.inputClass || "glass-input",
        { "is-invalid": this.hasError },
      ];
    },
    labelClasses() {
      return [this.labelClass || "yellow-label"];
    },
  },
};
</script>

<style scoped>
/* Üveg stílusú input */
.glass-input {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(245, 197, 66, 0.3);
  border-radius: 0.5rem;
  color: #fff;
  padding: 0.5rem 0.75rem;
  transition: 0.3s;
  flex: 1;
}

.glass-input::placeholder {
  color: #ffffff;
  opacity: 0.8;
}

.glass-input:focus {
  border-color: #f9d342;
  box-shadow: 0 0 8px #f9d342;
  background: rgba(255, 255, 255, 0.1);
  outline: none;
  color: #fff;
}

/* Label sárga */
.yellow-label {
  color: #f9d342;
  font-weight: 500;
  display: block;
  margin-bottom: 0.25rem;
}

/* Toggle gomb */
.input-group {
  display: flex;
  align-items: center;
}

.toggle-btn {
  background: none;
  border: none;
  color: #f9d342;
  cursor: pointer;
  margin-left: 0.5rem;
  font-size: 1.1rem;
}

.toggle-btn:hover {
  transform: scale(1.1);
  transition: 0.2s;
}
</style>
