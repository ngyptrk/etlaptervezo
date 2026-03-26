<template>
  <div class="d-flex align-items-center justify-content-between w-100">
    <div></div>

    <h1 class="pill-dark text-center m-0">Étlaptervező</h1>

    <div>
      <RouterLink v-if="isLoggedIn" class="pill-dark nav-link user-info" to="/userprofil">
        Felhasználó: {{ userName }}<br>
        Szerepkör: {{ userNameWithRole }} 
      </RouterLink>

      <div v-else class="pill-dark pill-user nav-link user-info p-1">
        <i class="bi bi-person"></i>
        Jelentkezz be!
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";

export default {
  computed: {
    ...mapState(useUserLoginLogoutStore, ["isLoggedIn", "userNameWithRole", "userName"]),
  },
};
</script>

<style>
.user-info {
  white-space: pre-line;
  color: #f5c542;
  font-weight: 600;
  text-align: center;
  padding: 0.6rem 1.4rem !important;
  line-height: 1.3;
}

.pill-dark {
  display: inline-block;
  padding: 0.6em 2em;
  font-size: clamp(1.4rem, 3vw, 2.5rem);
  font-weight: 700;
  color: #f5c542;
  background: rgba(20, 20, 20, 0.85);
  border-radius: 999px;
  border: 2px solid #f5c542;
  box-shadow: 0 0 20px rgba(245, 197, 66, 0.5);
  text-align: center;
  transition: all 0.3s ease;
  transform: translateY(0);
}

</style>
