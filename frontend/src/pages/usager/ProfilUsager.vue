<template>
  <div class="container">
    <p v-if="erreur">{{ erreur }}</p>

    <div v-if="usager">
      <p>Nom : {{ usager.nom }}</p>
      <!-- <p>Prénpm : {{ usager.prenom }}</p> -->
      <p>Courriel : {{ usager.courriel }}</p>
    </div>

    <button type="button" class="signup-btn" @click="supprimerUsager">
      Supprimer le compte
    </button>
  </div>
</template>

<script>
import api from "../../api";

export default {
  data() {
    return {
      usager: null,
      erreur: null,
    };
  },

  methods: {
    // Récupère les informations du profil de l'usager connecté
    async afficherProfil() {
      try {
        const response = await api.get("/afficher-usager");
        this.usager = response.data;
      } catch (erreur) {
        this.erreur = "Une erreur est survenue";
      }
    },
  },
  // Affiche le profil de l'usager dès que le composant est monté
  mounted() {
    this.afficherProfil();
  },
};
</script>
