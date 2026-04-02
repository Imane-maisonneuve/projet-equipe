import { createRouter, createWebHistory } from "vue-router";

import CreationUsager from "../pages/usager/CreationUsager.vue";
import Home from "../pages/Home.vue";
import Cart from "../pages/Cart.vue";
import ConnexionUsager from "../pages/usager/ConnexionUsager.vue";

const routes = [
  {
    path: "/",
    component: Home,
  },
  {
    path: "/cart",
    component: Cart,
  },
  {
    path: "/connexion-usager",
    component: ConnexionUsager,
  },
  {
    path: "/creation-usager",
    component: CreationUsager,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
