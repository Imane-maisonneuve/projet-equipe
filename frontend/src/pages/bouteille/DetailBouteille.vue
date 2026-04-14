<template>
  <Navbar />
  <div class="page-vinCarte">
    <VinCarte
      v-if="store.bouteilleVin"
      :bouteilleVin="store.bouteilleVin"
      :cellierNom="store.bouteilleVin.cellier_nom"
    />
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useRoute } from "vue-router";
import { useCellierStore } from "../../stores/detailBouteille";
import VinCarte from "../../components/VinCarte.vue";
import Navbar from "../../components/Navbar.vue";

const store = useCellierStore();
const route = useRoute();

onMounted(() => {
  store.fetchCellier(route.params.id);
});
</script>
