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

    <Menu class="sidebar" />

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
  computed: {
    ...mapState(useGlobalLoadingStore, ["isLoading"]),
  },
};
</script>

<style scoped>
.app-container {
  display: flex;
  min-height: 100vh;
  position: relative;
  overflow: hidden;
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
  width: 250px;
  height: 100vh;
  background: linear-gradient(135deg, #111000, #222a00);
  box-shadow: 3px 0 15px rgba(249, 211, 66, 0.3);
  padding: 1rem;
  overflow-y: auto;
  flex-shrink: 0;
}

.main-content {
  flex: 1;
  padding: 1rem 2rem;
  min-height: 100vh;
  overflow-y: auto;
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
}

@media (max-width: 768px) {
  .app-container {
    flex-direction: column;
  }
  .sidebar {
    width: 100%;
    height: auto;
  }
  .main-content {
    padding: 1rem;
  }
  .content-shell {
    padding: 0.8rem;
  }
}
</style>
