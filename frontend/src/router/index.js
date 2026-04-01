import { createRouter, createWebHistory } from "vue-router";

import CreationUsager from "../pages/usager/creationUsager.vue";

import Home from "../pages/Home.vue";
import Cart from "../pages/Cart.vue";

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
    path: "/creation-usager",
    component: CreationUsager,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
