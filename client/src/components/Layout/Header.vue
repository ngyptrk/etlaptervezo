<template>
  <div class="d-flex align-items-center justify-content-between w-100">
  <!-- Bal oldal: üres, csak a cím középre tolásához -->
  <div></div>

  <!-- Középső: cím -->
  <h1 class="pill-dark text-center m-0">Étlaptervező</h1>

  <!-- Jobb oldal: profil gomb -->
  <div>
  <RouterLink 
    v-if="isLoggedIn" 
    class="pill-dark nav-link user-info" 
    to="/userprofil"
  >
    <i class="bi bi-person"></i>
    {{ userNameWithRole }}
  </RouterLink>

  <div v-else class="pill-dark nav-link user-info p-1">
    <i class="bi bi-person"></i>
    Jelentkezz be!
  </div>
</div>
</div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useSearchStore } from "@/stores/searchStore";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import userLoginLogoutService from "@/api/userLoginLogoutService";
export default {
  computed: {
    ...mapState(useSearchStore, ["searchWord"]),
    ...mapState(useUserLoginLogoutStore, ["isLoggedIn", "userNameWithRole"]),
  },
};
</script>

<style>
.user-info {
  white-space: pre-line; /* a \n karaktereket sortörésként kezeli */
  color: #f5c542;
  font-weight: 600;
  text-align: center;
}

.pill-dark {
  display: inline-block;        /* inline-block kell, hogy a padding számítson */
  padding: 0.6em 2em;           /* elég nagy vízszintes padding a buborékhoz */
  font-size: clamp(1.4rem, 3vw, 2.5rem);
  font-weight: 700;
  color: #f5c542;

  background: rgba(20, 20, 20, 0.85); /* sötét hátterű buborék */
  border-radius: 999px;               /* teljesen lekerekített */
  border: 2px solid #f5c542;

  box-shadow: 0 0 20px rgba(245, 197, 66, 0.5); /* nagyobb glow, buborék hatás */
  text-align: center;                 /* középre az szöveg */
  transition: all 0.3s ease;

  /* Ha szeretnél egy “lebegő” animációt */
  transform: translateY(0);
}
</style>
