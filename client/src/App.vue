<template>
  <div class="app-container">
    <!-- Bal oldali menü -->
    <Menu class="sidebar"/>

    <!-- Fő tartalom -->
    <div class="main-content">
      <Header/>
      <Breadcrumb/>
      <RouterView/>
    </div>

    <ToastContanier/>
  </div>
</template>

<script>
import Menu from './components/Layout/Menu.vue';
import Header from './components/Layout/Header.vue';
import Footer from './components/Layout/Footer.vue';
import Breadcrumb from './components/Layout/Breadcrumb.vue';
import ToastContanier from './components/Message/ToastContanier.vue';

export default {
  components: {
    Menu,
    Header,
    Footer,
    Breadcrumb,
    ToastContanier,
  },
  data() {
  }
};
</script>

<style scoped>
.app-container {
  display: flex;
  min-height: 100vh;
  position: relative;
  overflow: hidden;

  background: #111000;
  color: #f9d342;
}

/* Animált háttér réteg */
.app-container::before {
  content: "";
  position: absolute;
  inset: -50%;

  background: radial-gradient(circle at 20% 30%, rgba(249, 211, 66, 0.15), transparent 40%),
              radial-gradient(circle at 80% 70%, rgba(249, 211, 66, 0.1), transparent 50%),
              radial-gradient(circle at 50% 50%, rgba(249, 211, 66, 0.08), transparent 60%);

  animation: backgroundMove 15s linear infinite;
  z-index: 0;
}

/* Tartalom mindig a háttér fölött */
.app-container > * {
  position: relative;
  z-index: 1;
}

/* Mozgás */
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


/* Bal oldali menü */
.sidebar {
  width: 250px;           /* fix szélesség */
  height: 100vh;          /* teljes magasság */
  background: linear-gradient(135deg, #111000, #222a00);
  box-shadow: 3px 0 15px rgba(249, 211, 66, 0.3);
  padding: 1rem;
  overflow-y: auto;
  flex-shrink: 0;         /* ne zsugorodjon a flexboxban */
}

/* Fő tartalom a sidebar mellett */
.main-content {
  flex: 1;
  padding: 1rem 2rem;
  min-height: 100vh;
  overflow-y: auto;
}

/* Mobil nézet */
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
}
</style>