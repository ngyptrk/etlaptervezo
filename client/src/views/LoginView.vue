<template>
  <div>
    <TopMiniModal :show="showSuccessModal" :message="successMessage" />

    <UserLogin
      :auth-error="loginError"
      @clear-error="loginError = null"
      @logIn="loginHandler"
    />
  </div>
</template>

<script>
import { mapActions } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import UserLogin from "@/components/User/UserLogin.vue";
import TopMiniModal from "@/components/Modal/TopMiniModal.vue";

export default {
  name: "LoginView",
  components: {
    UserLogin,
    TopMiniModal,
  },
  data() {
    return {
      loginError: null,
      showSuccessModal: false,
      successMessage: "Sikeres belépés",
      successTimer: null,
    };
  },
  methods: {
    ...mapActions(useUserLoginLogoutStore, ["login"]),
    async loginHandler(user) {
      this.loginError = null;
      try {
        await this.login(user);
        this.showSuccessModal = true;

        if (this.successTimer) {
          clearTimeout(this.successTimer);
        }
        this.successTimer = setTimeout(() => {
          this.$router.push("/");
        }, 900);
      } catch (error) {
        this.loginError = "Nem jó az email cím vagy a jelszó.";
      }
    },
    cleanupBodyModalStyles() {
      document.body.classList.remove("modal-open");
      document.body.style.removeProperty("overflow");
    },
  },
  mounted() {
    this.cleanupBodyModalStyles();
  },
  beforeUnmount() {
    if (this.successTimer) {
      clearTimeout(this.successTimer);
    }
    this.cleanupBodyModalStyles();
  },
};
</script>

<style></style>
