<template>
  <Navbar />
  <!-- Formulaire pour modifier le nom du cellier -->
  <div>
    <form @submit.prevent="modifierCellier" class="bloc-form">
      <h1 class="profil-titre">Modifier le cellier</h1>
      <div>
        <label for="nom-cellier">Choisir un nouveau nom</label>
        <input
          id="nom-cellier"
          type="text"
          v-model="nom"
          placeholder="Nouveau nom de cellier"
        />
        <div v-if="erreurs.nom" class="erreur">
          {{ erreurs.nom[0] }}
        </div>
      </div>
      <button type="submit" class="signup-btn">Modifier</button>
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
      nom: "",
      erreurs: {},
      message: "",
    };
  },
  methods: {
    // Méthode pour modifier le cellier

    async modifierCellier() {
      try {
        this.erreurs = {};
        this.message = "";

        const id = this.$route.params.id;
        this.erreurs = {};
        const response = await api.put(`/modifier-cellier/${id}`, {
          nom: this.nom,
        });
        this.cellier = response.data;
        // Rediriger vers le dashboard après la modification
        this.$router.push("/dashboard");
      } catch (erreur) {
        if (erreur.response.data.errors) {
          this.erreurs = erreur.response.data.errors;
        }
        if (erreur.response.data.message) {
          this.message = erreur.response.data.message;
        }
      }
    },
  },
};
</script>
