<template>
    <Navbar />
    <div class="page-vinCarte">
      <VinCarte
        v-if="store.bouteilleVin"
        :bouteilleVin="store.bouteilleVin"
        @supprimer-bouteille="ouvrirModale"
        @modifier-bouteille="modifierBouteille"
      />
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

<script>
import { useListeStore } from "../../stores/listeAchatBouteille.js";
import Navbar from "../../components/Navbar.vue";
import api from "../../api";
import VinCarte from "../../components/VinCarte.vue";
import ModalConfirmation from "../../components/ModalConfirmation.vue";

export default {
  components: {
    Navbar,
    VinCarte,
    ModalConfirmation
  },

  data() {
    return {
      afficherModale: false,
    };
  },

  computed: {
    store() {
      return useListeStore();
    },
  },

  mounted() {
    this.store.fetchVin(this.$route.params.id);
  },

  methods: {
    ouvrirModale() {
      this.afficherModale = true;
    },

    async supprimerBouteille() {
      try {
        const bouteilleId = this.store.bouteilleVin.id;
        await api.delete(`/liste-achats/${bouteilleId}`);
        this.store.bouteilleVin = null;
        this.afficherModale = false;
        this.$router.back();
      } catch (erreur) {
        console.error("Erreur lors de la suppression de la bouteille:", erreur);
      }
    },

    modifierBouteille() {
      this.$router.push(
        `/bouteille/ModifierBouteillePerso/${this.store.bouteilleVin.sku}`
      );
    },

  },
};
</script>