<template>
  <Navbar />
  <div class="profil-page">
    <div class="banniere">
      <h2 class="banniere-titre">Mon profil</h2>
    </div>
    <div class="profil-carte">
      <p v-if="erreur">{{ erreur }}</p>
      <div v-if="usager" class="profil-contenu">
        <p class="profil-data">Nom : {{ usager.nom }}</p>
        <p class="profil-data">Courriel : {{ usager.courriel }}</p>
      </div>
    </div>
    <div class="profil-action profil-lien">
      <router-link
        v-if="usager"
        :to="`/usager/modifier/${usager.id}`"
        class="profil-action-icone"
      >
        <PencilIcon class="profil-icone" />
        Modifier vos informations
      </router-link>
      <div class="profil-action-icone" @click="supprimerUsager">
        <MinusCircleIcon class="profil-icone" />Supprimer votre compte
      </div>
      <div class="profil-action-icone" @click="deconnecterUsager">
        <ArrowRightStartOnRectangleIcon class="profil-icone" />
        Se déconnecter
      </div>
    </div>
  </div>

  <!-- Affiche la modale de confirmation de suppression si afficherModale est vrai -->
  <div v-if="afficherModale">
    <div class="bloc-modale">
      <div class="bloc-modale-overlay"></div>
      <div class="bloc-modale-carte">
        <h2>Voulez-vous supprimer votre compte ?</h2>
        <div class="bloc-modale-btn">
          <button
            class="signup-btn"
            type="submit"
            @click="confirmerSuppression"
          >
            Supprimer
          </button>
          <button
            class="signup-btn btn-annuler"
            type="button"
            @click="afficherModale = false"
          >
            Annuler
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { MinusCircleIcon } from "@heroicons/vue/24/solid";
import { PencilIcon } from "@heroicons/vue/24/solid";
import { ArrowRightStartOnRectangleIcon } from "@heroicons/vue/24/solid";
</script>

<script>
import api from "../../api";
import Navbar from "../../components/Navbar.vue";

export default {
  components: {
    Navbar,
  },
  data() {
    return {
      usager: null,
      erreur: null,
      afficherModale: false,
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

    // Supprime le compte de l'usager connecté
    async supprimerUsager() {
      // todo : Ajouter une boite modale
      this.afficherModale = true;
    },

    async confirmerSuppression() {
      try {
        // suppression du compte de l'usager
        await api.delete("/supprimer-usager");
        // Redirige l'usager vers la page de connexion après la suppression
        this.$router.push("/deconnexion");
        // Redirige vers la page de connexion après la déconnexion
        this.$router.push("/connexion-usager");
      } catch (erreur) {
        this.erreur = "Erreur lors de la suppression";
      }
    },

    async deconnecterUsager() {
      try {
        //deconnexion du compte de l'usager
        await api.post("/deconnexion");
        // Redirige vers la page de connexion après la déconnexion
        this.$router.push("/connexion-usager");
      } catch (erreur) {
        this.erreur = "Erreur lors de la deconnexion";
      }
    },
  },
  // Affiche le profil de l'usager dès que le composant est monté
  mounted() {
    this.afficherProfil();
  },
};
</script>
