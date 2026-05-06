<template>
  <div class="menu-container">
    <div class="menu-header">
      <RouterLink to="/" class="logo-link">
        <img :src="logoUrl" alt="Étlaptervező" />
      </RouterLink>
      <button
        class="menu-toggle"
        type="button"
        aria-label="Menü megnyitása"
        @click="$emit('toggle')"
      >
        ☰
      </button>
    </div>

    <nav class="nav flex-column sidebar-nav">
      <RouterLink to="/" class="nav-link d-flex justify-content-center sidebar-link mt-1">
        <strong>Főoldal</strong>
      </RouterLink>

      <RouterLink to="/about" class="nav-link d-flex justify-content-center sidebar-link mt-1">
        <strong>Rólunk</strong>
      </RouterLink>

      <div v-if="isLoggedIn && hasMenuAccessByName('adatok')">
        <button
          class="btn sidebar-link w-100 text-start d-flex justify-content-center mt-1"
          type="button"
          @click="toggleSettings"
        >
          <i class="bi bi-gear">
            <strong> Adatok</strong>
          </i>
          <span :class="{ rotate: settingsOpen }" class="ps-1"></span>
        </button>

        <div class="collapse mt-2" :class="{ show: settingsOpen }">
          <nav class="btn-toggle-nav list-unstyled ps-3">
            <RouterLink
              v-if="hasMenuAccessByName('day')"
              :to="{ name: 'day' }"
              class="nav-link d-flex justify-content-center sidebar-link"
            >
              Napok
            </RouterLink>
            <RouterLink
              v-if="hasMenuAccessByName('ingredient')"
              :to="{ name: 'ingredient' }"
              class="nav-link d-flex justify-content-center sidebar-link"
            >
              Hozzávalók
            </RouterLink>
            <RouterLink
              v-if="hasMenuAccessByName('rawingredient')"
              :to="{ name: 'rawingredient' }"
              class="nav-link d-flex justify-content-center sidebar-link"
            >
              Nyers hozzávalók
            </RouterLink>
            <RouterLink
              v-if="hasMenuAccessByName('meal')"
              :to="{ name: 'meal' }"
              class="nav-link d-flex justify-content-center sidebar-link"
            >
              Étkezések
            </RouterLink>
            <RouterLink
              v-if="hasMenuAccessByName('unit')"
              :to="{ name: 'unit' }"
              class="nav-link d-flex justify-content-center sidebar-link"
            >
              Mértékegységek
            </RouterLink>
            <RouterLink
              v-if="hasMenuAccessByName('weekday')"
              :to="{ name: 'weekday' }"
              class="nav-link d-flex justify-content-center sidebar-link"
            >
              Hét napjai
            </RouterLink>
            <RouterLink
              v-if="hasMenuAccessByName('mealofday')"
              :to="{ name: 'mealofday' }"
              class="nav-link d-flex justify-content-center sidebar-link"
            >
              Napi étkezések
            </RouterLink>
            <RouterLink
              v-if="hasMenuAccessByName('mealreq')"
              :to="{ name: 'mealreq' }"
              class="nav-link d-flex justify-content-center sidebar-link"
            >
              Étkezés elvárások
            </RouterLink>
            <RouterLink
              v-if="hasMenuAccessByName('recipe')"
              :to="{ name: 'recipe' }"
              class="nav-link d-flex justify-content-center sidebar-link"
            >
              Receptek
            </RouterLink>
            <RouterLink
              v-if="hasMenuAccessByName('users')"
              :to="{ name: 'users' }"
              class="nav-link d-flex justify-content-center sidebar-link"
            >
              Felhasználók
            </RouterLink>
          </nav>
        </div>
      </div>

      <div class="mt-auto">
        <div v-if="!isLoggedIn">
          <RouterLink to="/login" class="nav-link d-flex justify-content-center sidebar-link mb-2">
            <i class="bi bi-person-fill"><strong> Belépés</strong></i>
          </RouterLink>
        </div>
      </div>

      <div>
        <div v-if="!isLoggedIn">
          <RouterLink
            to="/registration"
            class="nav-link d-flex justify-content-center sidebar-link mb-2"
          >
            <i class="bi bi-person-fill"><strong> Regisztrálás</strong></i>
          </RouterLink>
        </div>
      </div>

      <div v-if="isLoggedIn">
        <RouterLink
          to="/login"
          class="logout-btn w-100 d-flex justify-content-center align-items-center"
          @click="onClickLogout()"
        >
          <i class="bi bi-box-arrow-right me-2"></i>
          <strong>Kijelentkezés</strong>
        </RouterLink>
      </div>
    </nav>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import logoUrl from "@/pictures/logo.png";

export default {
  name: "Menu",
  emits: ["toggle"],
  data() {
    return {
      settingsOpen: false,
      logoUrl,
    };
  },
  computed: {
    ...mapState(useUserLoginLogoutStore, ["isLoggedIn", "userNameWithRole"]),
  },
  methods: {
    ...mapActions(useUserLoginLogoutStore, ["logout"]),
    toggleSettings() {
      this.settingsOpen = !this.settingsOpen;
    },
    hasMenuAccessByName(name) {
      const userStore = useUserLoginLogoutStore();
      const resolved = this.$router.resolve({ name });

      if (!resolved || !resolved.matched.length) {
        return false;
      }

      return resolved.matched.every((route) => {
        const requiredRoles = route.meta?.roles;
        return userStore.canAccess(requiredRoles);
      });
    },
    async onClickLogout() {
      try {
        await this.logout();
        this.$router.push("/");
      } catch {
        console.log("Kijelentkezési hiba!");
      }
    },
  },
};
</script>

<style scoped>
.menu-container {
  width: 100%;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: linear-gradient(135deg, #111000, #222a00);
  padding: 1rem 0.75rem;
  box-shadow: 3px 0 15px rgba(249, 211, 66, 0.3);
  position: sticky;
  top: 0;
  overscroll-behavior: contain;
}

.menu-header {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.75rem;
}

.menu-toggle {
  position: absolute;
  right: 0.25rem;
  border: 1px solid rgba(244, 209, 74, 0.6);
  background: transparent;
  color: #f4d14a;
  font-size: 1.35rem;
  border-radius: 10px;
  width: 44px;
  height: 44px;
  line-height: 1;
  display: none;
  align-items: center;
  justify-content: center;
}

.logo-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.sidebar-nav {
  flex: 1;
  display: flex;
  flex-direction: column;
  margin: 0;
}

.logo-link img {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  border: 3px solid #fdaa10;
  object-fit: cover;
  cursor: pointer;
  transition: box-shadow 0.3s;
}

.logo-link img:hover {
  box-shadow: 0 0 15px #fdaa10;
}

.sidebar-link {
  padding: 0.6rem 1.5rem;
  border-radius: 30px;
  margin: 0.3rem 0;
  background: rgba(249, 211, 66, 0.1);
  font-weight: 600;
  transition: 0.3s;
  cursor: pointer;
  color: #fdaa10;
  display: flex;
  justify-content: space-between;
}

.sidebar-link:hover,
.sidebar-link.router-link-active {
  background: #fdaa10;
  color: #111000;
  box-shadow: 0 0 0.1px #fdaa10, 0 0 10px #fdaa10;
}

.rotate {
  transition: transform 0.3s;
  transform-origin: center;
  transform: rotate(180deg);
}

@media (max-width: 768px) {
  .menu-container {
    width: 100%;
    min-height: auto;
    position: sticky;
    top: 0;
    z-index: 2;
  }

  .menu-toggle {
    display: inline-flex;
  }
}

.logout-btn {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0.7rem 1.5rem;
  border-radius: 30px;
  font-weight: 600;
  text-decoration: none;
  background: #8b1e1e;
  color: #ffffff;
  transition: all 0.25s ease;
}

.logout-btn:hover {
  background: #a82424;
  transform: translateY(-2px);
}

.logout-btn:active {
  transform: translateY(0);
  background: #741919;
}
</style>
