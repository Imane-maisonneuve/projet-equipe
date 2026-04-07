import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth.js";

import CreationUsager from "../pages/usager/CreationUsager.vue";
import ModifierUsager from "../pages/usager/ModifierUsager.vue";
import Home from "../pages/Home.vue";
import Cart from "../pages/Cart.vue";
import ConnexionUsager from "../pages/usager/ConnexionUsager.vue";
import ProfilUsager from "../pages/usager/ProfilUsager.vue";

const routes = [
  {
    path: "/",
    component: ConnexionUsager,
  },
  {
    path: "/liste-achats",
    component: Cart,
    meta: { requiresAuth: true },
  },
  {
    path: "/connexion-usager",
    component: ConnexionUsager,
  },
  {
    path: "/creation-usager",
    component: CreationUsager,
  },
  {
    path: "/usager/modifier/:id",
    component: ModifierUsager,
    meta: { requiresAuth: true },
  },
  {
    path: "/profil-usager",
    component: ProfilUsager,
    meta: { requiresAuth: true },
  },
  {
    path: "/catalogue",
    component: Home,
    meta: { requiresAuth: true },
  },
  {
    path: "/:pathMatch(.*)*",
    redirect: "/connexion-usager",
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  if (authStore.loading) {
    await new Promise((resolve) => setTimeout(resolve, 50));
    return next(to);
  }

  if (authStore.usager === null && !authStore.loading) {
    await authStore.fetchUsager();
  }

  const estConnecter =
    authStore.usager &&
    typeof authStore.usager === "object" &&
    Object.keys(authStore.usager).length > 0;

  if (to.meta.requiresAuth && !estConnecter) {
    next("/connexion-usager");
  } else {
    next();
  }
});

export default router;
