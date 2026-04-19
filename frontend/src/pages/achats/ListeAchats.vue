<template>
  <Navbar />
  <div class="banniere">
    <h2 class="banniere-titre">Liste d'achats</h2>
  </div>
  <p class="catalogue-description">
    Ajoutez vos vins à vos celliers, consultez-les ou supprimez-les selon vos
    besoins!
  </p>
  <Achat
    v-for="vin in vins"
    :key="vin.id"
    :vin="vin.vin"
    :id="vin.id"
    @ouvrir-modale="ouvrirModale"
  />

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
    /**
     * Récupère les informations de liste_achats
     */
    async saisirListeAchats() {
      try {
        await fetchCsrfToken();
        const response = await api.get("/liste-achats");
        console.log("Structure reçue:", response.data.liste_achats[0]);
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
  mounted() {
    this.saisirListeAchats();
  },
};
</script>
