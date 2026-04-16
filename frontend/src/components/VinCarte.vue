<template>
  <div class="entete"></div>
  <div class="vin-carte">
    <div class="vin-entete">
      <div class="vin-bg"></div>
      <div class="vin-back" @click="retour">←</div>
      <img :src="bouteilleVin.image" class="vin-image" />
    </div>
    <div class="vin-contenu">
      <h2 class="vin-titre">
        {{ bouteilleVin.nom || "Nom inconnu" }}
      </h2>
      <div class="vin-footer">
        <div class="vin-stock">
          {{ bouteilleVin.quantite }} en stock • {{ cellierNom }}
        </div>

        <div class="vin-prix">
          {{ bouteilleVin.prix ? formatPrice(bouteilleVin.prix) : "—" }}
        </div>
      </div>
    </div>
  </div>
  <div class="vin-card vin-infos">
    <h2 class="vin-titre">Détails :</h2>
    <p class="vin-sous-titre">Pays : {{ bouteilleVin.pays }}</p>
    <p class="vin-infos-parag">Region : {{ bouteilleVin.region }}</p>
    <p class="vin-infos-parag">Cepage : {{ bouteilleVin.cepage }}</p>
    <p class="vin-details">Format : {{ bouteilleVin.format }} ml</p>

    <p class="vin-infos-parag">
      Alcool : {{ Number(bouteilleVin.degre_alcool).toFixed(1) }}
    </p>
    <p class="vin-infos-parag">Taux sucre : {{ bouteilleVin.taux_sucre }}</p>
    <p class="vin-infos-parag">Année : {{ bouteilleVin.annee }}</p>
    <p class="vin-infos-parag">Couleur : {{ bouteilleVin.couleur }}</p>
  </div>

  <!-- Affiche les boutons de modification et de suppression uniquement pour les
  bouteilles personnalisées -->
  <div class="vinCarte-actions" v-if="isBouteillePersonalisee()">
    <button class="signup-btn" @click="SupprimerBouteille">Supprimer</button>
    <button class="signup-btn" @click="ModifierBouteille">Modifier</button>
  </div>
</template>

<script>
export default {
  props: {
    bouteilleVin: Object,
    cellierNom: String,
  },

  methods: {
    formatPrice(prix) {
      return new Intl.NumberFormat("fr-CA", {
        style: "currency",
        currency: "CAD",
      }).format(prix);
    },

    // Vérifie si la bouteille est personnalisée en se basant sur le SKU
    isBouteillePersonalisee() {
      return (
        this.bouteilleVin.sku && this.bouteilleVin.sku.startsWith("PERSO-")
      );
    },

    // Émet un événement pour la suppression et la modification
    SupprimerBouteille() {
      this.$emit("supprimer-bouteille", this.bouteilleVin.id);
    },

    // Émet un événement pour la suppression et la modification
    ModifierBouteille() {
      this.$emit("modifier-bouteille", this.bouteilleVin);
    },

    retour() {
      this.$router.push(`/detail-cellier/${this.bouteilleVin.cellier_id}`);
    },
  },
};
</script>
