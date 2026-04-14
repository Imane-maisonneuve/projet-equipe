<template>
  <div class="entete">
    <div class="image-titre">
      <img src="../assets/img/raisin.png" class="logo" />
      <h1 class="titre">Vino</h1>
    </div>
  </div>
  <div class="vin-carte">
    <div class="vin-entete">
      <div class="vin-bg"></div>
      <div class="vin-back" @click="retour">←</div>
      <img :src="bouteilleVin.image" class="vin-image" />
      <div class="vin-wave"></div>
    </div>

    <div class="vin-contenu">
      <h2 class="vin-titre">
        {{ bouteilleVin.nom || "Nom inconnu" }}
      </h2>

      <p class="vin-sous-titre">
        {{ bouteilleVin.annee }} • {{ bouteilleVin.pays }}
      </p>

      <p class="vin-details">
        {{ bouteilleVin.region }} • {{ bouteilleVin.format }} ml
      </p>

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
    <p class="vin-infos-parag">Region : {{ bouteilleVin.region }}</p>
    <p class="vin-infos-parag">Cepage : {{ bouteilleVin.cepage }}</p>
    <p class="vin-infos-parag">
      Alcool : {{ Number(bouteilleVin.degre_alcool).toFixed(1) }}
    </p>
    <p class="vin-infos-parag">Taux sucre : {{ bouteilleVin.taux_sucre }}</p>
    <p class="vin-infos-parag">Année : {{ bouteilleVin.annee }}</p>
    <p class="vin-infos-parag">Couleur : {{ bouteilleVin.couleur }}</p>
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

    retour() {
      this.$router.push("/dashboard/");
    },
  },
};
</script>
