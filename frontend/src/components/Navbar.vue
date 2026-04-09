<template>
  <nav class="mobile-navbar">
    <router-link to="/catalogue" class="nav-item">
      <Home class="icon" /> <span>Accueil</span>
    </router-link>
    <router-link to="/dashboard" class="nav-item">
      <Wine class="icon" /><span>Celliers</span>
    </router-link>
    <router-link to="/liste-achats" class="nav-item">
      <ShoppingBasket class="icon" /><span>Achats</span>
    </router-link>
    <router-link to="/profil-usager" class="nav-item">
      <User class="icon" /><span>Profil</span>
    </router-link>
  </nav>
</template>
<script setup>
import { Home, Wine, ShoppingBasket, User } from "lucide-vue-next";
</script>
