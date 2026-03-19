<template>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb m-0">
      <li class="breadcrumb-item breadcrumb-link">
        <router-link to="/">Főoldal</router-link>
      </li>

      <li
        v-for="(crumb, index) in breadcrumbs"
        :key="index"
        class="breadcrumb-item breadcrumb-link"
        :class="{ active: index === breadcrumbs.length - 1 }"
      >
        <router-link v-if="index < breadcrumbs.length - 1 && !crumb.disabled" :to="crumb.path">
          {{ crumb.label }}
        </router-link>

        <span v-else :class="{ 'breadcrumb-disabled': crumb.disabled }">
          {{ crumb.label }}
        </span>
      </li>
    </ol>
  </nav>
</template>

<script>
export default {
  computed: {
    breadcrumbs() {
      const matchedRoutes = this.$route.matched.filter(
        (route) => route.meta && route.meta.breadcrumb,
      );

      if (
        matchedRoutes.length > 0 &&
        (matchedRoutes[0].path === "/" || matchedRoutes[0].path === "")
      ) {
        matchedRoutes.shift();
      }

      return matchedRoutes.map((route) => ({
        label: route.meta.breadcrumb,
        path: route.path,
        disabled: route.meta.disabled,
      }));
    },
  },
};
</script>

<style scoped>
.breadcrumb {
  background-color: transparent;
  padding: 0.75rem 0;
}

.breadcrumb-link,
.breadcrumb-link a,
.breadcrumb-link span {
  color: #ffd43b;
  font-weight: 700;
  text-decoration: none;
}

.breadcrumb-link a:hover {
  color: #ffe680;
}

.breadcrumb-item + .breadcrumb-item::before {
  color: rgba(160, 160, 160, 0.8);
}

.breadcrumb-disabled {
  color: #ffd43b !important;
  opacity: 1;
}

.breadcrumb-item.active span {
  color: #ffe680;
}
</style>
