<template>
  <div class="nom-cellier">
    <!-- Afficher les détails du vin -->
    <div class="vin-cellier-carte">
      <img
        :src="vin.image_url"
        :alt="'Le nom du vin est : ' + vin.nom"
        class="cellier-img"
      />
      <div>
        <h2 class="nom">{{ vin.nom }}</h2>
        <p>{{ vin.couleur }} – {{ vin.pays }}</p>
        <p>{{ vin.prix }}$</p>
      </div>
    </div>

    <!-- Boutons d'action pour ajouter au cellier, voir les détails, ou supprimer -->
    <div class="bouton-cellier">
      <button
        class="btn btn-cellier"
        @click="ajouterAuCellier"
        aria-label="Ajouter au cellier"
      >
        <Plus class="icons" />
      </button>
      <button
        class="btn btn-cellier"
        @click="voirDetail"
        aria-label="Voir les détails"
      >
        <Eye class="icons" />
      </button>
      <button
        class="btn btn-cellier"
        @click="$emit('ouvrir-modale', id)"
        aria-label="Supprimer"
      >
        <Trash class="icons" />
      </button>
    </div>
  </div>
</template>

<script>
import { Trash, PencilLine, Eye, Plus } from "lucide-vue-next";
import api, { fetchCsrfToken } from "../api";

export default {
  components: {
    Trash,
    PencilLine,
    Eye,
    Plus,
  },
  props: {
    vin: Object,
    id: Number,
  },
  data() {
    return {
      erreur: "",
    };
  },
  methods: {
    // Redirige vers la page de détails du vin
    voirDetail() {
      this.$router.push(`/liste-achats-vin/${this.id}`);
    },

    // Redirige vers la page d'ajout de bouteille au cellier
    ajouterAuCellier() {
      this.$router.push(`/bouteille/AjouterBouteille/${this.vin.id}`);
    },
  },
};
</script>
