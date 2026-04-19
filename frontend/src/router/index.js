import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth.js";
import CreationUsager from "../pages/usager/CreationUsager.vue";
import ModifierUsager from "../pages/usager/ModifierUsager.vue";
import Home from "../pages/Home.vue";
import ConnexionUsager from "../pages/usager/ConnexionUsager.vue";
import ProfilUsager from "../pages/usager/ProfilUsager.vue";
import CreerCellier from "../pages/cellier/CreerCellier.vue";
import ModifierCellier from "../pages/cellier/ModifierCellier.vue";
import Dashboard from "../pages/cellier/Dashboard.vue";
import DetailBouteille from "../pages/bouteille/DetailBouteille.vue";
import DetailCellier from "../pages/cellierVin/DetailCellier.vue";
import AjouterBouteille from "../pages/bouteille/AjouterBouteille.vue";
import AjouterBouteillePerso from "../pages/bouteille/AjouterBouteillePerso.vue";
import RechercheBouteilleCellier from "../pages/cellier/RechercheBouteilleCellier.vue";
import ListeAchats from "../pages/achats/ListeAchats.vue";
import ModifierBouteillePerso from "../pages/bouteille/ModifierBouteillePerso.vue";
import ListeAchatBouteille from "../pages/achats/ListeAchatBouteille.vue";

const routes = [
  {
    path: "/",
    component: ConnexionUsager,
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
  {
    path: "/cellier-vin/:id",
    component: DetailBouteille,
  },
  {
    path: "/bouteille/AjouterBouteille/:id",
    component: AjouterBouteille,
    meta: { requiresAuth: true },
  },
  {
    path: "/detail-cellier/:id",
    component: DetailCellier,
    meta: { requiresAuth: true },
  },
  {
    path: "/bouteille/AjouterBouteillePerso/:id",
    component: AjouterBouteillePerso,
    meta: { requiresAuth: true },
  },

  {
    path: "/recherche-bouteille-cellier",
    component: RechercheBouteilleCellier,
    meta: { requiresAuth: true },
  },

  {
    path: "/bouteille/ModifierBouteillePerso/:sku,:cellier_id",
    component: ModifierBouteillePerso,
    meta: { requiresAuth: true },
  },

  {
    path: "/bouteille/ModifierBouteillePerso/:sku",
    component: ModifierBouteillePerso,
    meta: { requiresAuth: true },
  },

  {
    path: "/liste-achats",
    component: ListeAchats,
    meta: { requiresAuth: true },
  },
  {
    path: "/liste-achats-vin/:id",
    component: ListeAchatBouteille,
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

  //verifie si fetchUsager est en cours grace a authStore.loading
  if (authStore.loading) {
    await new Promise((resolve) => {
      const stop = authStore.$subscribe((mutation, state) => {
        if (!state.loading) {
          stop();
          resolve();
        }
      });
    });
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
