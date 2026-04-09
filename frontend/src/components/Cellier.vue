<template>
  <div class="nom-cellier">
    <div class="cellier-item">
      <div>
        <img
          src="../../public/bouteille.png"
          alt="bouteille"
          class="cellier-img"
        />
        <span class="cellier-nom">{{ cellier.nom }}</span>
      </div>
      <div>
        <span>6 bts</span>
      </div>
    </div>

    <div class="bouton-celleir">

      <button
        class="btn btn-cellier"
        @click="$emit('ouvrir-modale', cellier.id)"
      >
        <Trash class="icons" />
      </button>
      <button class="btn btn-cellier"><PencilLine class="icons" /></button>
      <button class="btn btn-cellier" @click="modifierCellier">
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
  name: "Cellier",
  props: {
    cellier: Object,
  },

  data() {
    return {
      erreur: "",
    };
  },
  methods: {
    async modifierCellier() {
      try {
        this.$router.push(`/modifier-cellier/${this.cellier.id}`);
      } catch (erreur) {
        this.erreur = "Erreur lors de la modification";
      }
    },
  },

};
</script>
