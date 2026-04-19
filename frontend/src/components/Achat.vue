<template>
  <div class="nom-cellier">
    <div class="vin-cellier-carte">
      <img :src="vin.image_url" :alt="vin.nom" class="cellier-img" />
      <div>
        <h2 class="nom">{{ vin.nom }}</h2>
        <p>{{ vin.couleur }} – {{ vin.pays }}</p>
        <p>{{ vin.prix }}$</p>
      </div>
    </div>

    <div class="bouton-cellier">
      <button class="btn btn-cellier" @click="ajouterAuCellier">
        <Plus class="icons" />
      </button>
      <button class="btn btn-cellier" @click="voirDetail">
        <Eye class="icons" />
      </button>
      <button class="btn btn-cellier" @click="$emit('ouvrir-modale', id)">
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
    voirDetail() {
      this.$router.push(`/liste-achats-vin/${this.id}`);
    },

    ajouterAuCellier() {
      this.$router.push(`/bouteille/AjouterBouteille/${this.vin.id}`);
    },
  },
};
</script>
