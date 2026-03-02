<template>
  <div class="app-container">
    <!-- Bal oldali menü -->
    <Menu class="sidebar"/>

    <!-- Fő tartalom -->
    <div class="main-content">
      <Header/>
      <Breadcrumb/>
      <RouterView/>
      <Footer :content="footerContent"/>
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
    return {
      footerContent: "Ez egy másik lábléc szöveg",
    }
  }
};
</script>

<style scoped>
.app-container {
  display: flex;
  min-height: 100vh;
  background: #111000;
  color: #f9d342;
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