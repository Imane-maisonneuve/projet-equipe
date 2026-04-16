<template>
  <Navbar />
  <div class="page-vinCarte">
    <VinCarte
      v-if="store.bouteilleVin"
      :bouteilleVin="store.bouteilleVin"
      :cellierNom="store.bouteilleVin.cellier_nom"
      @supprimer-bouteille="supprimerBouteille"
      @modifier-bouteille="modifierBouteille"
    />
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useCellierStore } from "../../stores/detailBouteille";
import VinCarte from "../../components/VinCarte.vue";
import Navbar from "../../components/Navbar.vue";
import api from "../../api";

const store = useCellierStore();
const route = useRoute();
const router = useRouter();

async function supprimerBouteille() {
  try {
    // Récupérer le SKU de la bouteille à supprimer depuis le store
    const bouteilleSKU = store.bouteilleVin.sku;

    // Envoyer une requête DELETE à l'API pour supprimer la bouteille personnalisée
    const response = await api.delete(`/supprimer-bouteille/${bouteilleSKU}`);

    // Après la suppression, réinitialiser la bouteille dans le store et rediriger vers la page du cellier
    store.bouteilleVin = null;

    router.back();

    // Afficher un message d'erreur si la suppression a échoué
  } catch (erreur) {
    console.error("Erreur lors de la suppression de la bouteille:", erreur);
  }
}
async function modifierBouteille() {}

onMounted(() => {
  store.fetchCellier(route.params.id);
});
</script>
