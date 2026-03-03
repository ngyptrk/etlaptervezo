<template>
  <div class="menu-container">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Logó -->
    <RouterLink to="/" class="logo-link d-flex justify-content-center mb-2">
      <img src="../../pictures/logo.png" alt="Logó" />
    </RouterLink>
    <!-- Menü -->
    <nav class="nav flex-column sidebar-nav m-3">
      <RouterLink
        to="/"
        class="nav-link d-flex justify-content-center sidebar-link mt-1"
        ><strong>Főoldal</strong></RouterLink
      >
      <RouterLink
        to="/about"
        class="nav-link d-flex justify-content-center sidebar-link mt-1"
        ><strong>Rólunk</strong></RouterLink
      >

      <!-- Beállítások dropdown -->
      <div>
        <button
          class="btn sidebar-link w-100 text-start d-flex justify-content-center mt-1"
          @click="toggleSettings"
        >
          <i class="bi bi-gear"><strong> Adatok</strong></i>
          <span :class="{ rotate: settingsOpen}" class="ps-1"></span>
        </button>

        <div class="collapse ps-1" :class="{ show: settingsOpen }">
          <nav class="btn-toggle-nav list-unstyled ps-3">
            <RouterLink
              v-for="i in 5"
              :key="i"
              :to="`/settings/template/${i}`"
              class="nav-link sidebar-sub-link sidebar-link mt-2"
            >
              Template {{ i }}
            </RouterLink>
          </nav>
        </div>
        <button class="nav-link d-flex justify-content-center sidebar-link w-100 mt-2" @click="onGenerate">
          Generálás
        </button>
      </div>
      <div class="mt-auto">
        <RouterLink
          to="/login"
          class="nav-link d-flex justify-content-center sidebar-link mb-2"
          ><i class="bi bi-person-fill"><strong> Belépés</strong></i></RouterLink
        >
        <RouterLink to="/login" class="logout-btn"><i class="bi bi-box-arrow-right"><strong> Kijelentkezés</strong></i> </RouterLink>
      </div>
    </nav>
  </div>
</template>

<script>
export default {
  name: "Menu",
  data() {
    return {
      settingsOpen: false,
    };
  },
  methods: {
    toggleSettings() {
      this.settingsOpen = !this.settingsOpen;
    },
    onGenerate() {
      alert("Generálás elindítva!");
    },
  },
};
</script>

<style scoped>
.menu-container {
  width: 250px;
  height: 100vh;

  display: flex;
  flex-direction: column;

  background: linear-gradient(135deg, #111000, #222a00);
  padding: 1rem;
  box-shadow: 3px 0 15px rgba(249, 211, 66, 0.3);
}

.sidebar-nav {
  flex: 1;
  display: flex;
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

.sidebar-sub-link {
  padding: 0.4rem 1.5rem;
  margin: 0.15rem 0;
  border-radius: 20px;
  color: #fdaa10;
  text-decoration: none;
}
.sidebar-sub-link:hover,
.sidebar-sub-link.router-link-active {
  background: #fdaa10;
  color: #111000;
}

.btn-toggle {
  background: transparent;
  border: none;
  font-weight: 600;
  cursor: pointer;
  padding: 0.6rem 1.5rem;
  border-radius: 30px;
  width: 100%;
  display: flex;
  justify-content: space-between;
}
.btn-toggle:hover {
  background: #fdaa10;
  color: #111000;
}

.rotate {
  transition: transform 0.3s;
  transform-origin: center;
  transform: rotate(180deg);
}

.btn-warning {
  border-radius: 25px;
  font-weight: 700;
  color: #111000;
  box-shadow: 0 0 8px #f9d342;
  transition: background-color 0.3s;
}
.btn-warning:hover {
  background-color: #e1c530;
}

/* Mobil nézet */
@media (max-width: 768px) {
  .menu-container {
    width: 100%;
    height: auto;
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

  background: #8b1e1e; /* mély bordó piros */
  color: #ffffff;

  transition: all 0.25s ease;
}

/* Hover */
.logout-btn:hover {
  background: #a82424; /* enyhén világosabb */
  transform: translateY(-2px);
}

/* Active (kattintáskor) */
.logout-btn:active {
  transform: translateY(0);
  background: #741919;
}
</style>
