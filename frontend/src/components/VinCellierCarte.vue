<template>
<div class="nom-cellier">
    <div class="vin-cellier-carte">
        <img
          :src="vin.image_url"
          :alt="vin.nom"
          class="cellier-img"
        />
        <div>
            <h2 class="nom">{{ vin.nom }}</h2>
            <p class="meta">Quantité: {{ quantite }}</p>
        </div>
    </div>

    <div class="bouton-celleir">
      <button
        class="btn btn-cellier"
        @click="$emit('ouvrir-modale', vin.id)"
      >
        <Trash class="icons" />
      </button>

      <button class="btn btn-cellier">
        <PencilLine class="icons" />
      </button>
    </div>
  </div>
</template>

<script>
import { Trash, PencilLine } from "lucide-vue-next";

export default {
  components: {
    Trash,
    PencilLine,
  },
  props: {
    vin: Object,
    quantite: Number,
  },
  data() {
    return {
      erreur: "",
    };
  },
  methods: {
    async modifierQuantiteVin() {
      try {

      } catch (erreur) {

      }
    },
  },
};
</script>