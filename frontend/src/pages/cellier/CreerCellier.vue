<template>
  <Navbar />
  <div class="container">
    <form @submit.prevent="creerCellier" class="bloc-form">
      <h1 class="profil-titre">Création de cellier</h1>
      <div>
        <label>Choisir un nom</label>
        <input type="text" v-model="nom" placeholder="Votre nom de cellier" />
        <div v-if="erreurs.nom" class="erreur">
          {{ erreurs.nom[0] }}
        </div>
      </div>
      <button type="submit" class="signup-btn">Créer le cellier</button>
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
      nom: "",
      usager: null,
      erreurs: {},
    };
  },
  methods: {
    // recupérer les informations de l'usager connecté
    async recupererUsager() {
      try {
        const response = await api.get("/afficher-usager");
        this.usager = response.data;
      } catch (erreur) {
        this.erreur = "Une erreur est survenue";
      }
    },

    // créer un cellier pour l'usager connecté
    async creerCellier() {
      try {
        this.erreurs = {};
        const response = await api.post("/creer-cellier", {
          nom: this.nom,
          usager: this.usager,
        });
        this.cellier = response.data;
        this.$router.push("/dashboard");
      } catch (erreur) {
        this.erreurs = erreur.response.data.errors;
      }
    },
  },
  // récupérer les informations de l'usager dès que le composant est monté
  mounted() {
    this.recupererUsager();
  },
};
</script>
