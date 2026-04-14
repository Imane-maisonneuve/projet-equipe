<template>
  <Navbar />
  <div>
    <form class="bloc-form" @submit.prevent="ajouterBouteille">
      <h1 class="profil-titre">Ajouter la bouteille au cellier</h1>
      <label>Nom du cellier </label>
      <select class="form-input" v-model="cellierVin">
        <option disabled value="">Choisir un cellier</option>
        <option v-for="cellier in celliers" :key="cellier.id" :value="cellier">
          {{ cellier.nom }}
        </option>
      </select>
      <div v-if="erreurs.id" class="erreur">
        {{ erreurs.nom[0] }}
      </div>
      <label>Quantité de bouteilles </label>
      <input
        class="form-input"
        type="number"
        v-model.number="quantite"
        placeholder="0"
      />
      <div v-if="erreurs.quantite" class="erreur">
        {{ erreurs.nom[0] }}
      </div>
      <button type="submit" class="signup-btn btn">Enregistrer</button>
      <div v-if="message" class="erreur">
        {{ message }}
      </div>
    </form>
  </div>
</template>

<script>
import Navbar from "../../components/Navbar.vue";
import api from "../../api";

export default {
  components: {
    Navbar,
  },
  data() {
    return {
      vin: null,
      celliers: [],
      erreurs: {},
      cellierVin: "",
    };
  },
  methods: {
    async recupererCelliers() {
      try {
        const response = await api.get("/celliers");
        this.celliers = response.data.data;
      } catch (erreur) {
        this.erreur = "Une erreur est survenue";
      }
    },
    async ajouterBouteille() {},
  },
  // récupérer les informations de la bouteille dès que le composant est monté
  mounted() {
    this.recupererCelliers();
  },
};
</script>
