import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth.js";

import CreationUsager from "../pages/usager/CreationUsager.vue";
import ModifierUsager from "../pages/usager/ModifierUsager.vue";
import Home from "../pages/Home.vue";
import Cart from "../pages/Cart.vue";
import ConnexionUsager from "../pages/usager/ConnexionUsager.vue";
import ProfilUsager from "../pages/usager/ProfilUsager.vue";
import CreerCellier from "../pages/cellier/CreerCellier.vue";
import ModifierCellier from "../pages/cellier/ModifierCellier.vue";
import Dashboard from "../pages/cellier/Dashboard.vue";

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
    path: "/creer-cellier",
    component: CreerCellier,
    meta: { requiresAuth: true },
  },
  {
    path: "/modifier-cellier/:id",
    component: ModifierCellier,
    meta: { requiresAuth: true },
  },
  {
    path: "/dashboard",
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  // redirige les URL non reconnu (dans notre code) pour /connexion-usager
  {
    path: "/:pathMatch(.*)*",
    redirect: "/connexion-usager",
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Ceci fonctionnent avant chaque navigation de la prochaine page pour verifier si l'usager est connecter
// to: ou l'usager veut aller
// from: l'usager vient d'ou
// next(): pour continuer la navigation
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  //verifie si fetchUsager est en cours grace a authStore.loading, et rejoue la verification pour vraiment voir si l'usager est null ou pas
  if (authStore.loading) {
    await new Promise((resolve) => setTimeout(resolve, 50));
    return next(to);
  }

  // Utile pour rester connecter au rechargement de la page
  if (authStore.usager === null && authStore.loading === false) {
    await authStore.fetchUsager();
  }

  // verifier si un Usager est connecter: authStore contient .usager et un objet qui a un proprieter (id / courriel / mot de passe)
  const estConnecter =
    authStore.usager &&
    typeof authStore.usager === "object" &&
    Object.keys(authStore.usager).length > 0;

  // si la page a besoin d'un connexion usager et il ny a pas d'usager connecter on retourne sur la page de connexion sinon; on laisse passer
  if (to.meta.requiresAuth && estConnecter === false) {
    next("/connexion-usager");
  } else {
    next();
  }
});

export default router;
