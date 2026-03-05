<template>
  <section class="home-wrap">
    <header class="hero-head">
      <h1 class="hero-title">Főoldal</h1>
      <p class="hero-subtitle">Ötletelő receptek, válogatott képekkel.</p>
    </header>

    <div v-if="loading" class="loading-state">Betöltés...</div>
    <div v-else-if="carouselItems.length === 0" class="empty-state">
      Jelenleg nincs képes étel a rendszerben.
    </div>

    <article v-else class="carousel-card">
      <div class="image-shell">
        <img
          :src="pictureUrl(activeItem.picture)"
          :alt="activeItem.name"
          class="hero-image"
          loading="lazy"
          @error="onImageError"
        />
        <div class="image-overlay">
          <h2>{{ activeItem.name }}</h2>
          <p>{{ activeItem.description || "Finom ötlet a következő menüdhöz." }}</p>
        </div>
      </div>

      <div class="carousel-controls">
        <button type="button" class="control-btn" @click="prevSlide">Előző</button>
        <div class="dots">
          <button
            v-for="(item, index) in carouselItems"
            :key="item.id"
            type="button"
            class="dot"
            :class="{ active: index === currentIndex }"
            :aria-label="`Ugrás a(z) ${item.name} képre`"
            @click="goToSlide(index)"
          ></button>
        </div>
        <button type="button" class="control-btn" @click="nextSlide">Következő</button>
      </div>
    </article>

    <section class="news-section">
      <h3 class="news-title">Újdonságok</h3>
      <p class="news-template">
        Template szöveg: ide kerülhet rövid tájékoztatás az oldal frissítéseiről.
      </p>
    </section>
  </section>
</template>

<script>
import recipeService from "@/api/recipeService";

export default {
  name: "HomeView",
  data() {
    return {
      loading: false,
      carouselItems: [],
      currentIndex: 0,
      rotateTimer: null,
    };
  },
  computed: {
    activeItem() {
      return this.carouselItems[this.currentIndex] ?? {};
    },
  },
  methods: {
    pictureUrl(path) {
      const apiUrl = import.meta.env.VITE_API_URL || "";
      const origin = apiUrl.replace(/\/api\/?$/, "");
      const normalized = String(path)
        .split("/")
        .map((part) => encodeURIComponent(part))
        .join("/");
      return origin ? `${origin}/${normalized}` : `/${normalized}`;
    },
    shuffleItems(items) {
      const copy = [...items];
      for (let i = copy.length - 1; i > 0; i -= 1) {
        const j = Math.floor(Math.random() * (i + 1));
        [copy[i], copy[j]] = [copy[j], copy[i]];
      }
      return copy;
    },
    nextSlide() {
      if (this.carouselItems.length < 2) return;
      this.currentIndex = (this.currentIndex + 1) % this.carouselItems.length;
    },
    prevSlide() {
      if (this.carouselItems.length < 2) return;
      this.currentIndex =
        (this.currentIndex - 1 + this.carouselItems.length) % this.carouselItems.length;
    },
    goToSlide(index) {
      this.currentIndex = index;
    },
    startAutoRotate() {
      this.stopAutoRotate();
      if (this.carouselItems.length < 2) return;
      this.rotateTimer = setInterval(() => {
        this.nextSlide();
      }, 5000);
    },
    stopAutoRotate() {
      if (this.rotateTimer) {
        clearInterval(this.rotateTimer);
        this.rotateTimer = null;
      }
    },
    onImageError(event) {
      event.target.style.opacity = "0.25";
    },
    async loadCarousel() {
      this.loading = true;
      try {
        const response = await recipeService.getAll();
        const recipes = response.data ?? [];
        const withPictures = recipes.filter((item) => item.picture);
        this.carouselItems = this.shuffleItems(withPictures).slice(0, 5);
        this.currentIndex = 0;
      } finally {
        this.loading = false;
        this.startAutoRotate();
      }
    },
  },
  async mounted() {
    await this.loadCarousel();
  },
  beforeUnmount() {
    this.stopAutoRotate();
  },
};
</script>

<style scoped>
.home-wrap {
  --line: rgba(244, 209, 74, 0.36);
  --accent: #f4d14a;
  --panel: rgba(15, 16, 18, 0.82);
  display: grid;
  gap: 1rem;
}

.hero-head {
  padding: 0.8rem 1rem;
  border: 1px solid var(--line);
  border-radius: 14px;
  background:
    radial-gradient(circle at 10% 20%, rgba(244, 209, 74, 0.22), transparent 45%),
    linear-gradient(120deg, #17181b 0%, #212329 100%);
}

.hero-title {
  margin: 0;
  color: #fff2b6;
  letter-spacing: 0.02em;
}

.hero-subtitle {
  margin: 0.35rem 0 0;
  color: #d7d7d7;
}

.loading-state,
.empty-state {
  border: 1px dashed var(--line);
  border-radius: 12px;
  padding: 1rem;
  color: var(--accent);
  font-weight: 600;
}

.carousel-card {
  border: 1px solid var(--line);
  border-radius: 16px;
  overflow: hidden;
  background: linear-gradient(140deg, rgba(21, 22, 26, 0.95), rgba(10, 11, 13, 0.95));
  max-width: 900px;
  margin: 0 auto;
}

.image-shell {
  position: relative;
  height: clamp(180px, 30vw, 300px);
}

.hero-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
  transition: transform 0.7s ease;
  image-rendering: auto;
}

.carousel-card:hover .hero-image {
  transform: scale(1.03);
}

.image-overlay {
  position: absolute;
  inset: auto 0 0 0;
  padding: 1rem;
  color: #f8f8f8;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.1));
}

.image-overlay h2 {
  margin: 0;
  font-size: clamp(1.1rem, 2vw, 1.6rem);
}

.image-overlay p {
  margin: 0.4rem 0 0;
  font-size: 0.95rem;
  max-width: 70ch;
}

.carousel-controls {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.7rem;
  padding: 0.8rem 0.9rem;
  border-top: 1px solid rgba(244, 209, 74, 0.18);
}

.control-btn {
  border: 1px solid rgba(244, 209, 74, 0.42);
  background: rgba(244, 209, 74, 0.08);
  color: #ffe38b;
  border-radius: 9px;
  font-weight: 700;
  font-size: 0.9rem;
  padding: 0.35rem 0.72rem;
}

.control-btn:hover {
  background: rgba(244, 209, 74, 0.18);
}

.dots {
  display: flex;
  align-items: center;
  gap: 0.45rem;
}

.dot {
  width: 11px;
  height: 11px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, 0.32);
}

.dot.active {
  background: var(--accent);
  box-shadow: 0 0 0 2px rgba(244, 209, 74, 0.25);
}

.news-section {
  border: 1px solid var(--line);
  border-radius: 14px;
  background: var(--panel);
  padding: 1rem;
  width: 100%;
}

.news-title {
  margin: 0;
  color: #ffe38b;
}

.news-template {
  margin: 0.6rem 0 0;
  color: #d8d8d8;
}

@media (max-width: 768px) {
  .carousel-controls {
    flex-wrap: wrap;
    justify-content: center;
  }
}
</style>
