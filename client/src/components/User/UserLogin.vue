<template>
  <div class="login-shell d-flex justify-content-center align-items-center">
    <div class="glass-card p-4">
      <h2 class="text-center mb-4">Bejelentkezés</h2>

      <form @submit.prevent="handleSubmit" :class="{ 'was-validated': validated }" novalidate>
        <div class="mb-3">
          <label for="email" class="form-label yellow-label">Email címed:</label>
          <input
            ref="emailInput"
            type="email"
            id="email"
            v-model="user.email"
            class="form-control glass-input"
            :class="{ 'is-invalid': emailHasError }"
            placeholder="pelda@email.com"
            required
            @input="handleFieldInput"
          />
          <div class="invalid-feedback" :class="{ 'd-block': emailHasError }">
            {{ authError || "Az email üres, vagy helytelen." }}
          </div>
        </div>

        <PasswordField
          v-model="user.password"
          :label="'Jelszavad'"
          input-class="glass-input"
          label-class="yellow-label"
          placeholder="Írd be a jelszavad"
          :password-error-message="passwordErrorMessage"
          :show-error="validated"
          @update:modelValue="handlePasswordInput"
        />

        <div class="d-flex justify-content-between mt-4">
          <button type="submit" class="btn btn-yellow">Belépés</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import PasswordField from "./PasswordField.vue";

class User {
  constructor(email = "", password = "") {
    this.email = email;
    this.password = password;
  }
}

export default {
  name: "UserLogin",
  components: {
    PasswordField,
  },
  props: {
    authError: { type: String, default: null },
  },
  data() {
    return {
      validated: false,
      user: new User(),
      emailFieldInvalid: false,
    };
  },
  computed: {
    emailHasError() {
      return Boolean(this.authError || (this.validated && this.emailFieldInvalid));
    },
    passwordErrorMessage() {
      if (!this.validated) return "";
      return this.user.password ? "" : "A jelszó üres.";
    },
  },
  methods: {
    handleSubmit(event) {
      const form = event.target;
      this.validated = true;

      const emailInput = this.$refs.emailInput;
      this.emailFieldInvalid = emailInput ? !emailInput.checkValidity() : false;

      if (form.checkValidity() === false) return;
      this.$emit("clear-error");
      this.$emit("logIn", this.user);
    },
    handleFieldInput() {
      if (this.authError) {
        this.$emit("clear-error");
      }
    },
    handlePasswordInput(value) {
      this.user.password = value;
      this.handleFieldInput();
    },
  },
};
</script>

<style scoped>
.login-shell {
  min-height: 100%;
  margin-top: 2rem;
  padding: 1rem;
}

.glass-card {
  width: 26rem;
  background: rgba(20, 20, 20, 0.7);
  border-radius: 1rem;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(245, 197, 66, 0.4);
  box-shadow: 0 8px 32px rgba(245, 197, 66, 0.2);
  color: #f9d342;
}

.glass-input {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(245, 197, 66, 0.3);
  border-radius: 0.5rem;
  color: #fff;
  padding: 0.5rem 0.75rem;
  transition: 0.3s;
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

.yellow-label {
  color: #f9d342;
  font-weight: 500;
}

.btn-yellow {
  background-color: #f9d342;
  color: #111000;
  font-weight: 600;
  border-radius: 0.5rem;
  border: none;
  transition: 0.3s;
}

.btn-yellow:hover {
  background-color: #fff176;
}
</style>
