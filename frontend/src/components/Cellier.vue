<template>
  <div class="nom-cellier">
    <!-- Afficher le nom du cellier -->
    <div class="cellier-item">
      <div class="vin-cellier-carte">
        <img
          src="../assets/img/cellier1.svg"
          alt="bouteille"
          class="cellier-img"
        />
        <div class="cellier-nom">{{ cellier.nom }}</div>
      </div>
    </div>
    <!-- Boutons d'action pour voir les détails, modifier ou supprimer un cellier -->
    <div class="bouton-cellier">
      <button
        class="btn btn-cellier"
        @click="voirDetailVinCellier"
        aria-label="Voir les détails"
      >
        <Eye class="icons" />
      </button>

      <button
        class="btn btn-cellier"
        @click="modifierCellier"
        aria-label="Modifier"
      >
        <PencilLine class="icons" />
      </button>

      <button
        class="btn btn-cellier"
        @click="$emit('ouvrir-modale', cellier.id)"
        aria-label="Supprimer"
      >
        <Trash class="icons" />
      </button>
    </div>
  </div>
</template>

<script>
import { Trash, PencilLine, Eye } from "lucide-vue-next";
export default {
  components: {
    Trash,
    PencilLine,
    Eye,
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
    // Naviguer vers la page de détails du cellier
    voirDetailVinCellier() {
      this.$router.push(`/detail-cellier/${this.cellier.id}`);
    },
    // Naviguer vers la page de modification du cellier
    async modifierCellier() {
      try {
        // rediriger vers la page de modification du cellier
        this.$router.push(`/modifier-cellier/${this.cellier.id}`);
      } catch (erreur) {
        this.erreur = "Erreur lors de la modification";
      }
    },
  },
};
</script>
