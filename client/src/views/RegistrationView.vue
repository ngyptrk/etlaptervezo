<template>
  <div>
    <TopMiniModal :show="showSuccessModal" :message="successMessage" />
    <UserRegistration
      ref="form"
      @createUser="handlerCreateUser"
    />
  </div>
</template>

<script>
import { mapActions } from "pinia";
import { useUserStore } from "@/stores/userStore";
import UserRegistration from "@/components/User/UserRegistration.vue";
import TopMiniModal from "@/components/Modal/TopMiniModal.vue";
export default {
  name: "RegistrationView",
  components: {
    UserRegistration,
    TopMiniModal,
  },
  data() {
    return {
      showSuccessModal: false,
      successMessage: "Sikeres regisztráció",
      successTimer: null,
    };
  },
  methods: {
    ...mapActions(useUserStore, ["createUser"]),
    async handlerCreateUser({ data, done }) {
      try {
        await this.createUser(data, { toast: false });
        done(true);
        this.showSuccessModal = true;

        if (this.successTimer) {
          clearTimeout(this.successTimer);
        }
        this.successTimer = setTimeout(() => {
          this.cleanupBodyModalStyles();
          this.$router.push("/login");
        }, 900);
      } catch (err) {
        if (err.response && err.response.status === 422) {
          this.$refs.form.setServerErrors(err.response.data.errors);
        }
        done(false);
      }
    },
    cleanupBodyModalStyles() {
      document.body.classList.remove("modal-open");
      document.body.style.removeProperty("overflow");
    },
  },
  beforeUnmount() {
    if (this.successTimer) {
      clearTimeout(this.successTimer);
    }
    this.cleanupBodyModalStyles();
  },
};
</script>

<style>
</style>
