<template>
  <div class="about-page">
    <section class="hero">
      <div class="hero-content">
        <h1>Rólunk</h1>
        <p class="hero-text">
          Egy modern, testreszabható étlaprendszer, ahol te döntöd el,
          mi kerüljön a tányérodra.
        </p>
      </div>
    </section>

    <section class="story">
      <div class="content">
        <h2>Miért hoztuk létre?</h2>
        <p>
          Ez a webalkalmazás szakmai vizsga keretében készült. Célunk egy olyan
          interaktív rendszer megvalósítása volt, amelyben a felhasználók saját
          ízlésük szerint állíthatják össze ételeiket.
        </p>
      </div>
    </section>

    <!-- FEATURES -->
    <section class="features">
      <h2>Főbb funkciók</h2>

      <div class="cards">
        <div class="card">
          <h3>Receptek</h3>
          <p>Folyton frissülő receptek.</p>
        </div>

        <div class="card">
          <h3>Napi ajánlatok</h3>
          <p>Napi étel ajánlás.</p>
        </div>

        <div class="card">
          <h3>Reszponzív design</h3>
          <p>Mobilon és asztali gépen is tökéletes élmény.</p>
        </div>
      </div>
    </section>

    <section v-if="isAdmin" class="tech">
      <h2>Technikai információk</h2>

      <div class="tech-grid">
        <div><strong>Fejlesztési mód:</strong> {{ dev }}</div>
        <div><strong>Production mód:</strong> {{ prod }}</div>
        <div><strong>SSR:</strong> {{ ssr }}</div>
        <div><strong>Mód:</strong> {{ mode }}</div>
        <div><strong>Verzió:</strong> {{ ver }}</div>
        <div><strong>API URL:</strong> {{ apiUrl }}</div>
      </div>
    </section>

    <section class="cta">
      <h2>Próbáld ki most!</h2>
      <p>Állítsd össze saját ételed néhány kattintással.</p>
      <router-link :to="ctaTarget" class="cta-button"> Kipróbálom </router-link>
    </section>
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";

export default {
  computed: {
    ...mapState(useUserLoginLogoutStore, ["role", "isLoggedIn"]),
    isAdmin() {
      return this.role === 1;
    },
    ctaTarget() {
      return this.isLoggedIn ? "/adatok/day" : "/login";
    },
  },
  data() {
    return {
      dev: import.meta.env.DEV,
      prod: import.meta.env.PROD,
      ssr: import.meta.env.SSR,
      mode: import.meta.env.MODE,
      title: import.meta.env.VITE_APP_TITLE,
      ver: import.meta.env.VITE_APP_VER,
      apiUrl: import.meta.env.VITE_API_URL,
    };
  },
};
</script>

<style scoped>
.about-page {
  min-height: 100%;
  font-family: "Segoe UI", sans-serif;
  color: white;
  background: radial-gradient(circle at top, #2a2a00, #000000 60%);
}

/* HERO */

.hero {
  text-align: center;
  padding: 120px 20px 80px;
}

.hero h1 {
  font-size: 64px;
  font-weight: 900;
  margin-bottom: 20px;
  background: linear-gradient(90deg, #ffd700, #fff3a0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero-text {
  font-size: 20px;
  color: #e6e6e6;
  max-width: 700px;
  margin: auto;
  line-height: 1.6;
}

/* STORY */

.story {
  padding: 70px 20px;
  max-width: 900px;
  margin: auto;
}

.story h2 {
  font-size: 34px;
  margin-bottom: 20px;
  color: #ffd700;
}

.story p {
  line-height: 1.8;
  color: #dddddd;
}

/* FEATURES */

.features {
  padding: 80px 20px;
  text-align: center;
}

.features h2 {
  font-size: 34px;
  margin-bottom: 50px;
  color: #ffd700;
}

.cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 30px;
  max-width: 1100px;
  margin: auto;
}

.card {
  backdrop-filter: blur(10px);
  background: rgba(255, 255, 255, 0.05);
  border-radius: 18px;
  padding: 35px;
  transition: all 0.35s ease;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.card:hover {
  transform: translateY(-10px) scale(1.02);
  box-shadow: 0 10px 35px rgba(255, 215, 0, 0.35);
  border: 1px solid rgba(255, 215, 0, 0.5);
}

.card h3 {
  margin-bottom: 10px;
  font-size: 22px;
}

.card p {
  color: #cccccc;
}

.tech {
  padding: 80px 20px;
}

.tech h2 {
  text-align: center;
  margin-bottom: 50px;
  color: #ffd700;
  font-size: 34px;
}

.tech-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
  max-width: 900px;
  margin: auto;
}

.tech-grid div {
  background: rgba(232, 229, 13, 0.05);
  padding: 18px 20px;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.cta {
  text-align: center;
  padding: 100px 20px;
  background: linear-gradient(135deg, #000000, #3a3a00);
}

.cta h2 {
  font-size: 40px;
  color: #ffd700;
  margin-bottom: 15px;
}

.cta p {
  color: #ddd;
  margin-bottom: 30px;
  font-size: 18px;
}

.cta-button {
  display: inline-block;
  padding: 15px 40px;
  font-weight: bold;
  border-radius: 40px;
  text-decoration: none;
  background: linear-gradient(90deg, #ffd700, #ffea70);
  color: black;
  transition: all 0.3s ease;
}

.cta-button:hover {
  transform: scale(1.08);
  box-shadow: 0 8px 30px rgba(255, 215, 0, 0.5);
}
</style>
