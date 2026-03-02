<template>
  <div class="menu-container">
    <!-- Logó -->
    <RouterLink to="/" class="logo-link d-flex justify-content-center mb-2">
      <img src="../../pictures/logo.png" alt="Logó"/>
    </RouterLink>
    <!-- Menü -->
    <nav class="nav flex-column m-3">
      <RouterLink to="/" class="nav-link d-flex justify-content-center sidebar-link">Főoldal</RouterLink>
      <RouterLink to="/rolunk" class="nav-link d-flex justify-content-center sidebar-link">Rólunk</RouterLink>
      <RouterLink to="/login" class="nav-link d-flex justify-content-center sidebar-link">Belépés</RouterLink>

      <!-- Beállítások dropdown -->
      <div>
        <button
          class="btn btn-toggle sidebar-link w-100 text-start d-flex justify-content-between"
          @click="toggleSettings"
        >
          Beállítások
          <span :class="{ rotate: settingsOpen }">▾</span>
        </button>

        <div class="collapse" :class="{ show: settingsOpen }">
          <nav class="btn-toggle-nav list-unstyled ps-3">
            <RouterLink v-for="i in 5" :key="i" :to="`/settings/template/${i}`" class="nav-link sidebar-sub-link">
              Template {{ i }}
            </RouterLink>
          </nav>
        </div>
        <button class="btn btn-warning w-100 mt-2" @click="onGenerate">Generálás</button>
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
  background: linear-gradient(135deg, #111000, #222a00);
  padding: 1rem;
  box-shadow: 3px 0 15px rgba(249, 211, 66, 0.3);
  overflow-y: auto;
}

.logo-link img {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  border: 3px solid #f9d342;
  object-fit: cover;
  cursor: pointer;
  transition: box-shadow 0.3s;
}
.logo-link img:hover {
  box-shadow: 0 0 15px #f9d342;
}

.sidebar-link {
  padding: 0.6rem 1.5rem;
  border-radius: 30px;
  margin: 0.3rem 0;
  background: rgba(249, 211, 66, 0.1);
  font-weight: 600;
  transition: 0.3s;
  cursor: pointer;
  color: #f9d342;
  display: flex;
  justify-content: space-between;
}
.sidebar-link:hover,
.sidebar-link.router-link-active {
  background: #f9d342;
  color: #111000;
  box-shadow: 0 0 10px #f9d342, 0 0 20px #f9d342;
}

.sidebar-sub-link {
  padding: 0.4rem 1.5rem;
  margin: 0.15rem 0;
  border-radius: 20px;
  color: #f9d342;
  text-decoration: none;
}
.sidebar-sub-link:hover,
.sidebar-sub-link.router-link-active {
  background: #f9d342;
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
  background: #f9d342;
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
</style>