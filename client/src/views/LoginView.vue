<template>
  <div>
    <TopMiniModal :show="showSuccessModal" :message="successMessage" />

    <UserLogin @logIn="loginHandler" />
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
      showSuccessModal: false,
      successMessage: "Sikeres belépés",
      successTimer: null,
    };
  },
  methods: {
    ...mapActions(useUserLoginLogoutStore, ["login"]),
    async loginHandler(user) {
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
        console.log("Bejelentkezési hiba!");
      }
    },
  },
  beforeUnmount() {
    if (this.successTimer) {
      clearTimeout(this.successTimer);
    }
  },
};
</script>

<style></style>
