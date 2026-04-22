<template>
  <Navbar />
  <!-- Formulaire de création de cellier -->
  <div>
    <form @submit.prevent="creerCellier" class="bloc-form">
      <h1 class="profil-titre">Création de cellier</h1>
      <div>
        <label for="nom-cellier">Choisir un nom</label>
        <input
          id="nom-cellier"
          type="text"
          v-model="nom"
          placeholder="Votre nom de cellier"
        />
        <div v-if="erreurs.nom" class="erreur">
          {{ erreurs.nom[0] }}
        </div>
      </div>
      <button type="submit" class="signup-btn">Créer le cellier</button>
      <!-- si il y a d'autre erreurs (surtout relier a la connexion) -->
      <p v-if="erreurConnexion" class="erreur">{{ erreurConnexion }}</p>
    </form>
  </div>
</template>

<script>
import Navbar from "../../components/Navbar.vue";
import api, { fetchCsrfToken } from "../../api";
import { useAuthStore } from "../../stores/auth";

export default {
  components: {
    Navbar,
  },
  data() {
    return {
      nom: "",
      usager: null,
      erreurs: {},
      erreurConnexion: "",
    };
  },
  methods: {
    // recupérer les informations de l'usager connecté
    async recupererUsager() {
      const authStore = useAuthStore();
      this.usager = authStore.usager;
      if (!authStore.usager) {
        this.erreurConnexion = "Vous devez être connecté pour créer un cellier";
        setTimeout(() => {
          this.$router.push("/connexion-usager");
        }, 3000);
      }
    },

    // créer un cellier pour l'usager connecté
    async creerCellier() {
      // si l'usager est pas connecter, retour a la page de connexion
      const authStore = useAuthStore();
      if (!authStore.usager) {
        this.erreurConnexion = "Vous devez être connecté pour créer un cellier";
        setTimeout(() => {
          this.$router.push("/connexion-usager");
        }, 3000);
      }

      try {
        this.erreurs = {};
        this.erreurConnexion = "";
        await fetchCsrfToken();
        const response = await api.post("/creer-cellier", {
          nom: this.nom,
          usager: this.usager,
        });
        // rediriger vers le dashboard après la création du cellier
        this.cellier = response.data;
        this.$router.push("/dashboard");
      } catch (erreur) {
        //catch les erreurs
        //si l'usager n'est pas connecter
        if (erreur.response && erreur.response.status === 401) {
          this.erreurConnexion =
            "Vous devez être connecté pour créer un cellier";
          setTimeout(() => {
            this.$router.push("/connexion-usager");
          }, 3000);
        }
        //renvoye les erreurs venu du backend
        //si il y a des erreurs de validation
        else if (
          erreur.response &&
          erreur.response.data &&
          erreur.response.data.errors
        ) {
          this.erreurs = erreur.response.data.errors;
          //si il y a des erreurs de connexion ou cretion de cellier avec un nom deja existant
        } else if (
          erreur.response &&
          erreur.response.data &&
          erreur.response.data.message
        ) {
          this.erreurConnexion = erreur.response.data.message;
          //si il y a des erreurs de connexion ou autre sans message
        } else {
          this.erreurConnexion = "Une erreur est survenue";
        }
      }
    },
  },
  // récupérer les informations de l'usager dès que le composant est monté
  mounted() {
    this.recupererUsager();
  },
};
</script>
