<template>
  <div v-if="messageSucces" class="bloc-succes">
    {{ messageSucces }}
  </div>
  <div class="container">
    <div class="bloc-img">
      <img src="../../assets/img/image.png" />
      <div class="bloc-img-secondaire">Vino</div>
    </div>
    <form @submit.prevent="updateUsager" class="bloc-form">
      <h2>Compte à modifier</h2>
      <p class="already-txt">
        Retourner ?
        <router-link to="/connexion-usager"> Se Connecter </router-link>
      </p>

      <div>
        <label>Nom :</label>
        <input type="text" v-model="nom" placeholder="Votre nom" />
        <div v-if="erreurs.nom" class="erreur">
          {{ erreurs.nom[0] }}
        </div>
      </div>

      <div>
        <label>Courriel :</label>
        <input
          type="email"
          v-model="courriel"
          placeholder="exemple@email.com"
        />
        <div v-if="erreurs.courriel" class="erreur">
          {{ erreurs.courriel[0] }}
        </div>
      </div>

      <div>
        <label>Mot de passe :</label>
        <input
          type="password"
          v-model="mot_de_passe"
          placeholder="6 caractères au minimum"
        />
        <div v-if="erreurs.mot_de_passe" class="erreur">
          {{ erreurs.mot_de_passe[0] }}
        </div>
      </div>
      <button type="submit" class="signup-btn">Modifier votre compte</button>
    </form>
  </div>
</template>
<script>
import axios from "axios";

export default {
  data() {
    return {
      nom: "",
      courriel: "",
      mot_de_passe: "",
      erreurs: {},
      messageSucces: "",
    };
  },
  mounted() {
    this.getUsager();
  },
  methods: {
    /**
     * Récupère les informations d’un usager à partir de son ID
     * présent dans l’URL (route params).
     * Remplit les champs du formulaire (nom, courriel).
     */
    async getUsager() {
      try {
        const id = this.$route.params.id;
        const response = await axios.get(
          `http://localhost:8000/api/usagers/${id}`
        );

        this.nom = response.data.data.nom;
        this.courriel = response.data.data.courriel;
      } catch (error) {
        console.error("Erreur getUsager:", error);
      }
    },

    /**
     * Met à jour les informations d’un usager.
     * Envoie les données modifiées au backend via une requête PUT.
     * Affiche un message de succès ou les erreurs de validation.
     */
    async updateUsager() {
      // Vider les erreurs avant la requête
      this.erreurs = {};
      this.messageSucces = "";
      try {
        const id = this.$route.params.id; // récupère l'id dans l'URL
        const response = await axios.put(
          `http://localhost:8000/api/usagers/${id}`,
          {
            nom: this.nom,
            courriel: this.courriel,
            mot_de_passe: this.mot_de_passe,
          }
        );
        this.messageSucces = "Votre compte a été mis à jour avec succès !";
        setTimeout(() => {
          this.messageSucces = "";
        }, 3000);
      } catch (erreur) {
        if (erreur.response && erreur.response.status === 422) {
          this.erreurs = erreur.response.data.erreurs;
        }
      }
    },
  },
};
</script>
