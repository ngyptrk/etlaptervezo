<template>
  
  <div class="about-page">

    <!-- HERO SECTION -->
    <section class="hero">
      <h1>Rólunk</h1>
      <p class="hero-text">
        Egy modern, testreszabható étlaprendszer, ahol te döntöd el,
        mi kerüljön a tányérodra.
      </p>
    </section>

    <!-- STORY SECTION -->
    <section class="story">
      <div class="content">
        <h2>Miért hoztuk létre?</h2>
        <p>
          Ez a webalkalmazás szakmai vizsga keretében készült.
          Célunk egy olyan interaktív rendszer megvalósítása volt,
          amelyben a felhasználók saját ízlésük szerint állíthatják
          össze ételeiket.
        </p>
        <p>
          Válassz alapot, adj hozzá feltéteket, és a rendszer
          automatikusan kiszámolja az árat — egyszerűen,
          gyorsan és átláthatóan.
        </p>
      </div>
    </section>

    <!-- FEATURES -->
    <section class="features">
      <h2>Főbb funkciók</h2>

      <div class="cards">
        <div class="card">
          <h3>🍕 Alapételek</h3>
          <p>Széles választék különböző alapokból.</p>
        </div>

        <div class="card">
          <h3>🧀 Egyedi feltétek</h3>
          <p>Szabadon kombinálható hozzávalók.</p>
        </div>

        <div class="card">
          <h3>💰 Ár kalkuláció</h3>
          <p>Valós idejű árfrissítés minden választás után.</p>
        </div>

        <div class="card">
          <h3>📱 Reszponzív design</h3>
          <p>Mobilon és asztali gépen is tökéletes élmény.</p>
        </div>
      </div>
    </section>

    <!-- TECH INFO -->
    <section class="tech">
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

  </div>
</template>

<script>
export default {
  data() {
    return {
      dev: import.meta.env.DEV,
      prod: import.meta.env.PROD,
      ssr: import.meta.env.SSR,
      mode: import.meta.env.MODE,
      title: import.meta.env.VITE_APP_TITLE,
      ver: import.meta.env.VITE_APP_VER,
      apiUrl: import.meta.env.VITE_API_URL
    }
  }
}
</script>

<style scoped>

.about-page {
  background: none; /* nincs háttér */
  color: #000000; /* szöveg fekete lesz, mert sötét háttér már nincs */
  min-height: 100vh;
  font-family: 'Segoe UI', sans-serif;
}

/* HERO */
.hero {
  text-align: center;
  padding: 100px 20px 60px 20px;
}

.hero h1 {
  font-size: 60px;
  font-weight: 800;
  margin-bottom: 20px;
  color: #ffd700;
  text-shadow: 0 0 25px rgba(255, 215, 0, 0.4);
}

.hero-text {
  font-size: 20px;
  color: #ffd500;
  max-width: 700px;
  margin: auto;
}

/* STORY */
.story {
  padding: 60px 20px;
  max-width: 900px;
  margin: auto;
}

.story h2 {
  font-size: 32px;
  margin-bottom: 20px;
  color: #ffd700;
}

.story p {
  color: #ffffff;
  line-height: 1.7;
  margin-bottom: 15px;
}

/* FEATURES */
.features {
  padding: 60px 20px;
  text-align: center;
}

.features h2 {
  font-size: 32px;
  margin-bottom: 40px;
  color: #ffd700;
}

.cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 25px;
  max-width: 1000px;
  margin: auto;
}

.card {
  background: linear-gradient(145deg, #3e3e00, #000000);
  padding: 30px;
  border-radius: 14px;
  transition: 0.3s ease;
  cursor: pointer;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.card:hover {
  transform: translateY(-8px);
  box-shadow: 0 0 25px rgba(255, 217, 0, 0.3);
  border: 1px solid rgba(255, 215, 0, 0.5);
}

.card h3 {
  margin-bottom: 10px;
  color: #ffffff;
}

.card p {
  color: #f5f5f5; 
}

/* TECH */
.tech {
  padding: 60px 20px;
  background: linear-gradient(180deg, #b48702, #000000);
}

.tech h2 {
  text-align: center;
  margin-bottom: 40px;
  color: #ffd700;
}

.tech-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 15px;
  max-width: 900px;
  margin: auto;
  color: #000000;
}

</style>