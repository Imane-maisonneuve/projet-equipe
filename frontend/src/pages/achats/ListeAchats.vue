<template>
  <!-- Affichage de la liste d'achats -->
  <Navbar />
  <div class="banniere">
    <h2 class="banniere-titre">Liste d'achats</h2>
  </div>

  <!-- Description de la page -->
  <p class="catalogue-description">
    Ajoutez vos vins à vos celliers, consultez-les ou supprimez-les selon vos
    besoins!
  </p>
  <!-- Affiche une notification -->
  <div v-if="notifStore.message" :class="['notif', notifStore.type]">
    {{ notifStore.message }}
  </div>
  <!-- Affichage de chaque vin de la liste d'achat -->
  <Achat
    v-for="vin in vins"
    :key="vin.id"
    :vin="vin.vin"
    :id="vin.id"
    @ouvrir-modale="ouvrirModale"
  />
  <!-- Affichage de la modale de confirmation de suppression d'un vin de la liste d'achat -->
  <ModalConfirmation
    :show="afficherModale"
    message="Voulez-vous supprimer ce vin de votre liste d'achat ?"
    confirmText="Supprimer"
    cancelText="Annuler"
    @confirm="confirmerSuppression"
    @cancel="afficherModale = false"
  />
  <div class="espacement"></div>
</template>
<script>
import Navbar from "../../components/Navbar.vue";
import Achat from "../../components/Achat.vue";
import ModalConfirmation from "../../components/ModalConfirmation.vue";
import api, { fetchCsrfToken } from "../../api";
import { useNotifStore } from "../../stores/notification";

export default {
  components: {
    Navbar,
    Achat,
    ModalConfirmation,
  },
  data() {
    return {
      vins: [],
      erreur: "",
      afficherModale: false,
      idASupprimer: null,
    };
  },
  methods: {
    // Récupérer la liste d'achat de l'utilisateur connecté
    async saisirListeAchats() {
      try {
        await fetchCsrfToken();
        const response = await api.get("/liste-achats");
        this.vins = response.data.liste_achats;
      } catch (error) {
        console.error(
          "Erreur lors de la récupération de la liste d'achats:",
          error,
        );
      }
    },
    //Ouvrire la modale de suppression de bouteille de la liste d'achat
    ouvrirModale(id) {
      this.idASupprimer = id;
      this.afficherModale = true;
    },
    // Une fois qui l'utilisateur confirme la suppression d'un bouteille de la liste d'achat
    async confirmerSuppression() {
      try {
        // supprimer la bouteille de la liste d'achat
        await api.delete(`/liste-achats/${this.idASupprimer}`);

        // Supprimer localement
        this.vins = this.vins.filter((item) => item.id !== this.idASupprimer);

        // enlever l'affichage du Modale de suppression
        this.afficherModale = false;
        this.idASupprimer = null;
      } catch (err) {
        this.erreur =
          "Erreur lors de la suppression de la bouteille de la liste d'achat.";
      }
    },
  },
  setup() {
    const notifStore = useNotifStore();
    return { notifStore };
  },
  // Appel de la méthode pour récupérer les informations de la liste d'achat dès que le composant est monté
  mounted() {
    this.saisirListeAchats();
  },
};
</script>
