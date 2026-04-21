<template>
  <Navbar />
  <!-- Affichage de la carte de vin si les données sont disponibles -->
  <div class="page-vinCarte">
    <VinCarte
      v-if="store.bouteilleVin"
      :bouteilleVin="store.bouteilleVin"
      :cellierNom="store.bouteilleVin.cellier_nom"
      @supprimer-bouteille="ouvrirModale"
      @modifier-bouteille="modifierBouteille"
    />

    <Review 
			v-if="store.bouteilleVin"
			:initialRating="store.review?.rating"
			:initialComment="store.review?.comment"
			@save="handleSaveReview"
		/>    
    <!-- modal avec message de confirmation -->
    <ModalConfirmation
      :show="afficherModale"
      message="Voulez-vous supprimer cette bouteille, la suppression est définitive ?"
      confirmText="Supprimer"
      cancelText="Annuler"
      @confirm="supprimerBouteille"
      @cancel="afficherModale = false"
    />
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useCellierStore } from "../../stores/detailBouteille";
import VinCarte from "../../components/VinCarte.vue";
import Review from "../../components/Review.vue";
import ModalConfirmation from "../../components/ModalConfirmation.vue";
import Navbar from "../../components/Navbar.vue";
import api from "../../api";

const store = useCellierStore();
const route = useRoute();
const router = useRouter();
const afficherModale = ref(false);
// Fonction pour ouvrir la modale de confirmation
function ouvrirModale() {
  afficherModale.value = true;
}
// Fonction pour supprimer la bouteille
async function supprimerBouteille() {
  try {
    // Récupérer le SKU de la bouteille à supprimer depuis le store
    const bouteilleSKU = store.bouteilleVin.sku;

    // Envoyer une requête DELETE à l'API pour supprimer la bouteille personnalisée
    const response = await api.delete(`/supprimer-bouteille/${bouteilleSKU}`);

    // Après la suppression, réinitialiser la bouteille dans le store et rediriger vers la page du cellier
    store.bouteilleVin = null;

    afficherModale.value = false;
    router.back();

    // Afficher un message d'erreur si la suppression a échoué
  } catch (erreur) {
    console.error("Erreur lors de la suppression de la bouteille:", erreur);
  }
}
// Fonction pour rediriger vers la page de modification de la bouteille
function modifierBouteille() {
  router.push(
    `/bouteille/ModifierBouteillePerso/${store.bouteilleVin.sku},${store.bouteilleVin.cellier_id}`,
  );
}

const handleSaveReview = async (data) => {
	try {
		await api.post('/reviews', {
			vin_id: store.bouteilleVin.vin_id,
			usager_id: store.bouteilleVin.usager_id,
			rating: data.rating,
			comment: data.comment
		});
		
		store.review = { ...data };
		
	} catch (error) {
		console.error(error);
	}
};

// Charger les données de la bouteille lors du montage du composant
onMounted(() => {
  store.fetchCellier(route.params.id);
});
</script>
