<template>
  <div class="d-flex justify-content-center align-items-center py-4">
    <div class="glass-card p-4">
      <h2 class="text-center mb-4">Regisztráció</h2>
      <form @submit.prevent="handleSubmit" :class="{ 'was-validated': validated }" novalidate>
        <div class="mb-3">
          <label for="userName" class="form-label yellow-label">Felhasználó név:</label>
          <input
            id="userName"
            v-model="userName"
            type="text"
            class="form-control glass-input"
            :class="{ 'is-invalid': shouldShowError('name') }"
            placeholder="Írd be a felhasználóneved"
            minlength="2"
            required
            @input="clearError('name')"
          />
          <div v-if="nameErrorMessage" class="invalid-feedback d-block">
            {{ nameErrorMessage }}
          </div>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label yellow-label">Email címed:</label>
          <input
            id="email"
            v-model="email"
            type="email"
            class="form-control glass-input"
            :class="{ 'is-invalid': shouldShowError('email') }"
            placeholder="pelda@email.com"
            required
            @input="clearError('email')"
          />
          <div v-if="emailErrorMessage" class="invalid-feedback d-block">
            {{ emailErrorMessage }}
          </div>
        </div>

        <PasswordField
          ref="pass1Comp"
          class="mb-3"
          v-model="password"
          label="Jelszavad"
          input-ref="firstInput"
          label-id="password"
          :server-errors="serverErrors"
          :password-error-message="passwordErrorMessage"
          :show-error="shouldShowError('password')"
        />

        <PasswordField
          ref="pass2Comp"
          v-model="confirmPassword"
          label="Jelszavad még egyszer"
          input-ref="confirmInput"
          label-id="confirmPassword"
          :password-error-message="confirmPasswordErrorMessage"
          :show-error="shouldShowError('confirmPassword')"
        />

        <div class="d-flex justify-content-between mt-4">
          <button type="submit" class="btn btn-yellow">Regisztrálás</button>
          <button type="button" class="btn btn-outline-yellow" @click="this.$router.push('/login')">
            Mégsem
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import PasswordField from "./PasswordField.vue";

export default {
  name: "UserRegistration",
  components: {
    PasswordField,
  },
  data() {
    return {
      userName: "",
      email: "",
      password: "",
      confirmPassword: "",
      validated: false,
      passwordErrorMessage: "",
      confirmPasswordErrorMessage: "",
      serverErrors: {},
      touched: {
        name: false,
        email: false,
        password: false,
        confirmPassword: false,
      },
    };
  },
  watch: {
    userName() {
      this.touched.name = true;
    },
    email() {
      this.touched.email = true;
    },
    password() {
      this.touched.password = true;
      this.validatePasswords();
    },
    confirmPassword() {
      this.touched.confirmPassword = true;
      this.validatePasswords();
    },
  },
  computed: {
    nameErrorMessage() {
      if (this.serverErrors?.name?.length) return this.serverErrors.name[0];
      if (!this.touched.name && !this.validated) return "";
      if (!this.userName) return "A név megadása kötelező.";
      if (this.userName.length < 2) return "A név legalább 2 karakter legyen.";
      return "";
    },
    emailErrorMessage() {
      if (this.serverErrors?.email?.length) return this.serverErrors.email[0];
      if (!this.touched.email && !this.validated) return "";
      if (!this.email) return "Az email cím megadása kötelező.";
      const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email);
      if (!isEmail) return "Az email cím formátuma nem megfelelő.";
      return "";
    },
  },
  methods: {
    validatePasswords() {
      const comp2 = this.$refs.pass2Comp;
      const input2 = comp2?.$refs?.[comp2.inputRef];

      if (!this.password) {
        this.passwordErrorMessage = "A jelszó megadása kötelező.";
      } else if (this.password.length < 3) {
        this.passwordErrorMessage = "A jelszónak legalább 3 karakternek kell lennie.";
      } else {
        this.passwordErrorMessage = "";
      }

      if (input2) {
        if (this.password !== this.confirmPassword) {
          input2.setCustomValidity("A jelszavak nem egyeznek.");
          this.confirmPasswordErrorMessage = "A két jelszó nem egyezik.";
        } else {
          input2.setCustomValidity("");
          this.confirmPasswordErrorMessage = "";
        }
      } else {
        this.confirmPasswordErrorMessage =
          this.password !== this.confirmPassword ? "A két jelszó nem egyezik." : "";
      }
    },
    shouldShowError(field) {
      return Boolean((this.touched[field] || this.validated) && this.getFieldError(field));
    },
    getFieldError(field) {
      if (field === "name") return this.nameErrorMessage;
      if (field === "email") return this.emailErrorMessage;
      if (field === "password") return this.passwordErrorMessage || this.serverErrors?.password?.[0];
      if (field === "confirmPassword") return this.confirmPasswordErrorMessage;
      return "";
    },
    handleSubmit() {
      this.validatePasswords();
      this.validated = true;

      const hasErrors = Boolean(
        this.getFieldError("name") ||
          this.getFieldError("email") ||
          this.getFieldError("password") ||
          this.getFieldError("confirmPassword"),
      );

      if (hasErrors) {
        return;
      }

      this.$emit("createUser", {
        data: {
          name: this.userName,
          email: this.email,
          password: this.password,
          password_confirmation: this.confirmPassword,
        },
        done: (success) => {
          if (success) {
            this.serverErrors = {};
          }
        },
      });
    },
    setServerErrors(errors) {
      this.serverErrors = errors;
    },
    clearError(field) {
      if (this.serverErrors[field]) {
        delete this.serverErrors[field];
      }
    },
  },
};
</script>

<style scoped>
.glass-card {
  width: min(26rem, 100%);
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

.btn-outline-yellow {
  background: transparent;
  color: #f9d342;
  font-weight: 600;
  border: 2px solid #f9d342;
  border-radius: 0.5rem;
  transition: 0.3s;
}

.btn-outline-yellow:hover {
  background: rgba(249, 211, 66, 0.2);
  color: #fff;
}
</style>
