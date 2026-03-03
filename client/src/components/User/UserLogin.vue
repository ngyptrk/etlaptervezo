<template>
  <div class="d-flex justify-content-center align-items-center py-4">
    <div class="glass-card p-4">
      <h2 class="text-center mb-4">Login vagy Regisztráció</h2>

      <form @submit.prevent="handleSubmit" :class="{ 'was-validated': validated }" novalidate>
        <!-- Email -->
        <div class="mb-3">
          <label for="email" class="form-label yellow-label">Email címed:</label>
          <input
            type="email"
            id="email"
            v-model="user.email"
            class="form-control glass-input"
            placeholder="pelda@email.com"
            required
          />
          <div class="invalid-feedback">Az email üres, vagy helytelen</div>
        </div>

        <!-- Password -->
        <div class="mb-3">
          <PasswordField
            v-model="user.password"
            :label="'Jelszavad:'"
            input-class="glass-input"
            label-class="yellow-label"
            placeholder="Írd be a jelszavad"
          />
          <div class="invalid-feedback">A jelszó üres</div>
        </div>

        <!-- Gombok -->
        <div class="d-flex justify-content-between mt-4">
          <button type="submit" class="btn btn-yellow">Login</button>
          <RouterLink to="/registration" class="btn btn-outline-yellow">Regisztráció</RouterLink>
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
  data() {
    return {
      validated: false,
      user: new User(),
    };
  },
  methods: {
    handleSubmit(event) {
      const form = event.target;
      this.validated = true;

      if (form.checkValidity() === false) {
        console.log("Hiba a mezőkben!");
      } else {
        console.log("Sikeres validáció:", this.user);
        this.$emit("logIn", this.user);
      }
    },
  },
};
</script>

<style scoped>
/* Glass kártya */
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

/* Üveg input */
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

/* Label szín */
.yellow-label {
  color: #f9d342;
  font-weight: 500;
}

/* Gombok */
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
