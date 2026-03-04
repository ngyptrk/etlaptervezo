import { createRouter, createWebHistory } from "vue-router";
import HomeView from "@/views/HomeView.vue";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import { useToastStore } from "@/stores/toastStore";
import { useGlobalLoadingStore } from "@/stores/globalLoadingStore";

function checkIfNotLogged() {
  const storeAuth = useUserLoginLogoutStore();
  if (!storeAuth.isLoggedIn) {
    return "/login";
  }
}

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: HomeView,
      meta: { title: () => "Főoldal", breadcrumb: "Főoldal" },
    },
    {
      path: "/about",
      name: "about",
      component: () => import("@/views/AboutView.vue"),
      meta: { title: () => "Rólunk", breadcrumb: "Rólunk" },
    },
    {
      path: "/userprofil",
      name: "userprofil",
      component: () => import("@/views/UserProfileView.vue"),
      beforeEnter: [checkIfNotLogged],
      meta: { title: () => "Saját fiók", breadcrumb: "Saját fiók" },
    },
    {
      path: "/adatok",
      name: "adatok",
      component: () => import("@/views/EmptyWrapperView.vue"),
      meta: { breadcrumb: "Adatok", disabled: true, roles: [1, 2] },
      children: [
        {
          path: "unit",
          name: "unit",
          component: () => import("@/views/UnitView.vue"),
          beforeEnter: [checkIfNotLogged],
          meta: { title: () => "Mértékegységek", breadcrumb: "Mértékegységek", roles: [1] },
        },
        {
          path: "meal",
          name: "meal",
          component: () => import("@/views/MealView.vue"),
          beforeEnter: [checkIfNotLogged],
          meta: { title: () => "Étkezések", breadcrumb: "Étkezések", roles: [1] },
        },
        {
          path: "ingredient",
          name: "ingredient",
          component: () => import("@/views/IngredientView.vue"),
          beforeEnter: [checkIfNotLogged],
          meta: { title: () => "Hozzávalók", breadcrumb: "Hozzávalók", roles: [1] },
        },
        {
          path: "weekday",
          name: "weekday",
          component: () => import("@/views/WeekdayView.vue"),
          beforeEnter: [checkIfNotLogged],
          meta: { title: () => "Hét napjai", breadcrumb: "Hét napjai", roles: [1] },
        },
        {
          path: "day",
          name: "day",
          component: () => import("@/views/DayView.vue"),
          beforeEnter: [checkIfNotLogged],
          meta: { title: () => "Napok", breadcrumb: "Napok", roles: [1, 2] },
        },
        {
          path: "mealofday",
          name: "mealofday",
          component: () => import("@/views/MealOfDayView.vue"),
          beforeEnter: [checkIfNotLogged],
          meta: { title: () => "Napi étkezések", breadcrumb: "Napi étkezések", roles: [1] },
        },
        {
          path: "mealreq",
          name: "mealreq",
          component: () => import("@/views/MealRequirementView.vue"),
          beforeEnter: [checkIfNotLogged],
          meta: { title: () => "Étkezés elvárások", breadcrumb: "Étkezés elvárások", roles: [1] },
        },
        {
          path: "rawingredient",
          name: "rawingredient",
          component: () => import("@/views/RawIngredientView.vue"),
          beforeEnter: [checkIfNotLogged],
          meta: { title: () => "Nyers hozzávalók", breadcrumb: "Nyers hozzávalók", roles: [1] },
        },
        {
          path: "recipe",
          name: "recipe",
          component: () => import("@/views/RecipeView.vue"),
          beforeEnter: [checkIfNotLogged],
          meta: { title: () => "Receptek", breadcrumb: "Receptek", roles: [1] },
        },
        {
          path: "users",
          name: "users",
          component: () => import("@/views/UsersView.vue"),
          beforeEnter: [checkIfNotLogged],
          meta: { title: () => "Felhasználók", breadcrumb: "Felhasználók", roles: [1] },
        },
      ],
    },
    {
      path: "/login",
      name: "login",
      component: () => import("@/views/LoginView.vue"),
      meta: { title: () => "Bejelentkezés", breadcrumb: "Bejelentkezés" },
    },
    {
      path: "/registration",
      name: "registration",
      component: () => import("@/views/RegistrationView.vue"),
      meta: { title: () => "Regisztráció", breadcrumb: "Regisztráció" },
    },
    {
      path: "/:pathMatch(.*)*",
      name: "NotFound",
      component: () => import("@/views/404.vue"),
      meta: { title: () => "404", breadcrumb: "" },
    },
  ],
});

router.beforeEach((to, from, next) => {
  useGlobalLoadingStore().setRouteLoading(true);

  const titleMeta = to.meta?.title;
  const pageTitle = typeof titleMeta === "function" ? titleMeta(to) : titleMeta || "Oldal";
  document.title = `Iskola - ${pageTitle}`;

  const requiredRoles = to.meta.roles;
  const userStore = useUserLoginLogoutStore();

  if (userStore.canAccess(requiredRoles)) {
    next();
  } else if (!userStore.isLoggedIn) {
    next({ path: "/login" });
  } else {
    useToastStore().messages.push("Ehhez az oldalhoz nincs jogod!");
    useToastStore().show("Error");
    next("/");
  }
});

router.afterEach(() => {
  useGlobalLoadingStore().setRouteLoading(false);
});

router.onError(() => {
  useGlobalLoadingStore().setRouteLoading(false);
});

export default router;
