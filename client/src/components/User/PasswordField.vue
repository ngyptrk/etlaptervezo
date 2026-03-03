<template>
  <div class="mb-3">
    <!-- Label -->
    <label v-if="label" :for="labelId" class="yellow-label">{{ label }}:</label>

    <!-- Input és toggle -->
    <div class="input-group">
      <input
        :type="showPassword ? 'text' : 'password'"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        class="form-control glass-input"
        :id="labelId"
        :placeholder="placeholder"
        required
      />
      <button type="button" class="toggle-btn" @click="showPassword = !showPassword">
        <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
      </button>

      <!-- Hibák -->
      <div class="invalid-feedback">
        {{ passwordErrorMessage || "A jelszó kötelező" }}
      </div>
      <div v-if="serverErrors?.password" class="invalid-feedback d-block">
        {{ serverErrors?.password[0] }}
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
    passwordErrorMessage: { type: String, default: "" },
    serverErrors: { type: Object, default: () => ({}) },
  },
  data() {
    return {
      showPassword: false,
    };
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
