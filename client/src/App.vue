<template>
  <div class="app-container">
    <div v-if="isLoading" class="global-loading-overlay">
      <div class="global-loading-modal">
        <img src="@/pictures/logo.png" alt="Étlaptervező" class="loading-logo" />
        <div class="spinner-border text-warning mb-3" role="status"></div>
        <div class="loading-title">Kérlek várj egy pillanatot...</div>
        <div class="loading-subtitle">Az oldal betöltése folyamatban van.</div>
      </div>
    </div>

    <div class="mobile-bar">
      <button
        class="mobile-toggle"
        type="button"
        aria-label="Menü megnyitása"
        @click="toggleSidebar"
      >
        ☰
      </button>
    </div>

    <div
      v-if="isSidebarOpen"
      class="sidebar-backdrop"
      @click="closeSidebar"
    ></div>

    <Menu
      class="sidebar"
      :class="{ open: isSidebarOpen }"
      @toggle="toggleSidebar"
    />

    <div class="main-content">
      <Header />
      <Breadcrumb />
      <section class="content-shell">
        <RouterView />
      </section>
    </div>

    <ToastContanier />
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useGlobalLoadingStore } from "@/stores/globalLoadingStore";
import Menu from "./components/Layout/Menu.vue";
import Header from "./components/Layout/Header.vue";
import Breadcrumb from "./components/Layout/Breadcrumb.vue";
import ToastContanier from "./components/Message/ToastContanier.vue";

export default {
  components: {
    Menu,
    Header,
    Breadcrumb,
    ToastContanier,
  },
  data() {
    return {
      isSidebarOpen: false,
    };
  },
  computed: {
    ...mapState(useGlobalLoadingStore, ["isLoading"]),
  },
  methods: {
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen;
    },
    closeSidebar() {
      this.isSidebarOpen = false;
    },
  },
};
</script>

<style scoped>
.app-container {
  display: flex;
  min-height: 100dvh;
  width: 100%;
  position: relative;
  overflow-x: hidden;
  background:
    radial-gradient(circle at 20% 0%, rgba(255, 201, 41, 0.15), transparent 36%),
    linear-gradient(140deg, #0f0f10, #1a1a1c 45%, #111111 100%);
  color: #f4d14a;
}

.app-container::before {
  content: "";
  position: absolute;
  inset: -50%;
  background:
    radial-gradient(circle at 20% 30%, rgba(249, 211, 66, 0.12), transparent 40%),
    radial-gradient(circle at 80% 70%, rgba(90, 90, 90, 0.14), transparent 52%),
    radial-gradient(circle at 50% 50%, rgba(249, 211, 66, 0.06), transparent 62%);
  animation: backgroundMove 18s linear infinite;
  z-index: 0;
}

.app-container > * {
  position: relative;
  z-index: 1;
}

.global-loading-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  background: rgba(8, 8, 10, 0.78);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
}

.global-loading-modal {
  width: min(560px, 92vw);
  min-height: 360px;
  border: 1px solid rgba(244, 209, 74, 0.58);
  border-radius: 18px;
  background: linear-gradient(180deg, rgba(25, 25, 27, 0.96), rgba(17, 17, 18, 0.98));
  box-shadow:
    0 18px 48px rgba(0, 0, 0, 0.52),
    0 0 24px rgba(255, 209, 74, 0.26);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 1.5rem 1.4rem;
}

.loading-logo {
  width: 130px;
  height: 130px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #f4d14a;
  box-shadow: 0 0 18px rgba(244, 209, 74, 0.45);
  margin-bottom: 1.1rem;
}

.loading-title {
  font-size: clamp(1.2rem, 2.2vw, 1.7rem);
  font-weight: 800;
  color: #ffe36f;
}

.loading-subtitle {
  margin-top: 0.35rem;
  color: #e8dba5;
  font-size: 0.98rem;
}

@keyframes backgroundMove {
  0% {
    transform: rotate(0deg) translate(0, 0);
  }
  50% {
    transform: rotate(180deg) translate(5%, 5%);
  }
  100% {
    transform: rotate(360deg) translate(0, 0);
  }
}

.sidebar {
  width: clamp(200px, 22vw, 280px);
  height: 100dvh;
  background: linear-gradient(135deg, #111000, #222a00);
  box-shadow: 3px 0 15px rgba(249, 211, 66, 0.3);
  padding: 1rem;
  overflow-y: auto;
  overscroll-behavior: contain;
  flex-shrink: 0;
  min-width: 0;
  position: sticky;
  top: 0;
  align-self: flex-start;
  z-index: 2;
}


.mobile-bar {
  display: none;
}

.sidebar-backdrop {
  display: none;
}

.main-content {
  flex: 1;
  min-width: 0;
  padding: clamp(0.8rem, 2vw, 2rem);
  height: 100dvh;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  overscroll-behavior: contain;
}

.content-shell {
  margin-top: 0.4rem;
  padding: 1.2rem;
  border: 1px solid rgba(255, 209, 74, 0.4);
  border-radius: 14px;
  background: linear-gradient(180deg, rgba(25, 25, 27, 0.9), rgba(17, 17, 18, 0.94));
  box-shadow:
    0 12px 32px rgba(0, 0, 0, 0.35),
    0 0 18px rgba(255, 209, 74, 0.12);
  overflow: visible;
}

@media (max-width: 768px) {
  .app-container {
    flex-direction: column;
  }
  .mobile-bar {
    position: sticky;
    top: 0;
    z-index: 3;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 0.6rem 0.9rem;
    background: linear-gradient(135deg, #111000, #222a00);
    box-shadow: 0 3px 12px rgba(249, 211, 66, 0.25);
  }
  .mobile-toggle {
    border: 1px solid rgba(244, 209, 74, 0.6);
    background: transparent;
    color: #f4d14a;
    font-size: 1.35rem;
    border-radius: 10px;
    width: 44px;
    height: 44px;
    line-height: 1;
  }
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100dvh;
    width: min(82vw, 320px);
    transform: translateX(-100%);
    transition: transform 0.2s ease;
    z-index: 4;
  }
  .sidebar.open {
    transform: translateX(0);
  }
  .sidebar-backdrop {
    display: block;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    z-index: 3;
  }
  .main-content {
    padding: 1rem;
    height: auto;
  }
  .content-shell {
    padding: 0.8rem;
  }
}
</style>
