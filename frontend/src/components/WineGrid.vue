<template>
  <!-- Affiche une grille de cartes de vins. Chaque carte de vin est un composant VinCatalogueCarte. -->
  <div class="grid">
    <VinCatalogueCarte v-for="vin in vins" :key="vin.id" :vin="vin" />
  </div>
</template>

<script>
import VinCatalogueCarte from "./VinCatalogueCarte.vue";

export default {
  components: {
    VinCatalogueCarte,
  },

  props: {
    vins: Array,
  },
  data() {
    return {};
  },
  methods: {
    // Émet un événement "ajout-du-vin" avec le vin sélectionné en tant que payload.
    ajoutDuVin(vin) {
      this.$emit("ajout-du-vin", vin);
    },
  },
};
</script>
