<template>
  <section>
    <div
      v-for="day in groupedDays"
      :key="`${day.week}-${day.weekdayId}`"
      class="day-block mb-4"
    >
      <div class="day-header d-flex justify-content-between align-items-center">
        <h4 class="m-0">{{ day.weekdayName }}</h4>
        <span class="week-badge">{{ day.week }}. hĂ©t</span>
      </div>

      <div class="row row-cols-4">
        <div
          v-for="meal in day.meals"
          :key="meal.id"
          class="col-12 col-sm-6 col-md-4 col-xl-3"
        >
          <article class="recipe-card mb-4">
            <div class="recipe-top">
              <img
                v-if="meal.recipePicture"
                class="recipe-image"
                :src="buildImageUrl(meal.recipePicture)"
                :alt="meal.recipeName"
                loading="lazy"
                @error="onImageError"
              />
              <div class="recipe-meta">
                <div class="recipe-type">
                  {{ meal.mealPeriod }} - {{ meal.mealType }}
                </div>
                <div class="recipe-name">{{ meal.recipeName }}</div>
              </div>
            </div>

            <div class="recipe-desc">{{ meal.recipeDescription }}</div>

            <div class="ingredients-block mt-2">
              <div class="ingredients-title">
                <Button @click="toggleIngredients(meal.id)"
                  >HozzĂˇvalĂłk <i class="bi bi-basket"></i>
                </Button>
              </div>
              <div v-if="openMeals[meal.id]" class="ingredients-content">
                <ul v-if="meal.ingredients?.length" class="ingredients-list">
                  <li
                    v-for="(ing, idx) in meal.ingredients"
                    :key="`${meal.id}-${idx}`"
                  >
                    {{ ing.amount }} {{ ing.unit }} - {{ ing.name }}
                  </li>
                </ul>
                <div v-else class="ingredients-empty">
                  Ehhez a recepthez nincs hozzĂˇvalĂł megadva.
                </div>
              </div>
            </div>
          </article>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "WeeklyPlanBoard",
  props: {
    groupedDays: { type: Array, default: () => [] },
  },
  data() {
    return {
      openMeals: {},
    };
  },
  methods: {
    buildImageUrl(picturePath) {
      const apiUrl = import.meta.env.VITE_API_URL || "";
      const origin = apiUrl.replace(/\/api\/?$/, "");
      const normalized = String(picturePath || "")
        .split("/")
        .map((segment) => encodeURIComponent(segment))
        .join("/");

      return origin ? `${origin}/${normalized}` : `/${normalized}`;
    },
    toggleIngredients(mealId) {
      this.openMeals[mealId] = !this.openMeals[mealId];
    },
    onImageError(event) {
      event.target.style.display = "none";
    },
  },
};
</script>

<style scoped>
.day-block {
  border: 1px solid rgba(244, 209, 74, 0.28);
  border-radius: 12px;
  padding: 0.8rem;
  background: rgba(15, 15, 16, 0.75);
}

.day-header h4 {
  color: #ffd84f;
  font-weight: 700;
}

.week-badge {
  color: #111;
  background: #f4d14a;
  border-radius: 999px;
  padding: 0.2rem 0.7rem;
  font-size: 0.82rem;
  font-weight: 700;
}

.recipe-card {
  border: 1px solid rgba(180, 180, 180, 0.35);
  border-radius: 10px;
  background: linear-gradient(180deg, #d8d8da, #ceced1);
  padding: 0.75rem;
  color: #151515;
  min-height: 145px;
}

.recipe-top {
  display: flex;
  gap: 0.7rem;
}

.recipe-image {
  width: 84px;
  height: 84px;
  border-radius: 8px;
  object-fit: cover;
  border: 1px solid rgba(0, 0, 0, 0.2);
  flex-shrink: 0;
}

.recipe-meta {
  min-width: 0;
}

.recipe-type {
  font-size: 0.86rem;
  font-weight: 700;
  color: #333;
}

.recipe-name {
  margin-top: 0.25rem;
  font-size: 1.02rem;
  font-weight: 700;
}

.recipe-desc {
  margin-top: 0.55rem;
  font-size: 0.9rem;
  color: #34383d;
}

.ingredients-block {
  border-top: 1px dashed rgba(40, 40, 40, 0.35);
  padding-top: 0.45rem;
}

.ingredients-title {
  font-size: 0.9rem;
  font-weight: 700;
  color: #2c2c2c;
  margin-bottom: 0.2rem;
}

.ingredients-list {
  margin: 0;
  padding-left: 1rem;
  font-size: 0.86rem;
}

.ingredients-empty {
  font-size: 0.84rem;
  color: #5e5e5e;
}
</style>
