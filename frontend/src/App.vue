<script setup>
import { RouterView } from "vue-router";
//import Navbar from "./components/Navbar.vue";

import { useAuthStore } from './stores/auth';
const authStore = useAuthStore();
authStore.fetchUsager();

</script>

<template>
  <!-- <navbar /> -->
  <router-view />
</template>
