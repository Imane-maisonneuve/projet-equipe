<template>
  <div class="container">
    <div class="bloc-img">
      <img src="../../assets/img/logo3.svg" alt="Logo de l'application" />
    </div>
    <!-- Formulaire de création de compte -->
    <form @submit.prevent="gererSoumission" class="bloc-form">
      <p class="already-txt">
        Déjà membre ?
        <router-link to="/connexion-usager"> Se Connecter </router-link>
      </p>
      <!-- Nom -->
      <div>
        <label>Nom :</label>
        <input type="text" v-model="nom" placeholder="Votre nom" />
        <div v-if="erreurs.nom" class="erreur">
          {{ erreurs.nom[0] }}
        </div>
      </div>
      <!-- Courriel -->
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
      <!-- Mot de passe -->
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

      <button type="submit" class="signup-btn">Créer un compte</button>
      <p v-if="erreur" class="erreur">{{ erreur }}</p>
    </form>
  </div>
</template>
<script>
import axios from "axios";
import api, { fetchCsrfToken } from "../../api";
import { useAuthStore } from "../../stores/auth";
import { useNotifStore } from '../../stores/notification';

export default {
  data() {
    return {
      nom: "",
      courriel: "",
      mot_de_passe: "",
      erreurs: {},
      erreur: "",
      creationReussie: false,
    };
  },
  methods: {
    // Gère la soumission du formulaire : crée l'usager et la connexion
    async gererSoumission() {
      // Reset les erreurs
      this.erreurs = {};
      this.erreur = "";

      const creationSuccess = await this.creerUsager();

      if (creationSuccess) {
        await this.connexion();
      }
    },
    // creer l'usager quand il pese sur "creer un compte"
    async creerUsager() {
      try {
        const response = await axios.post("http://localhost:8000/api/usagers", {
          nom: this.nom,
          courriel: this.courriel,
          mot_de_passe: this.mot_de_passe,
        });

        //si l'usager a bien ete creer
        if (response.status === 201 || response.status === 200) {
          this.creationReussie = true;
          return true;
        }
        return false;
      } catch (erreur) {
        if (erreur.response && erreur.response.status === 422) {
          this.erreurs = erreur.response.data.erreurs;
        } else if (erreur.response) {
          this.erreur =
            erreur.response.data.message ||
            "Erreur lors de la création du compte";
        } else if (erreur.request) {
          this.erreur =
            "Impossible de joindre le serveur. Vérifiez votre connexion.";
        } else {
          this.erreur = "Une erreur est survenue lors de la création du compte";
        }
        this.creationReussie = false;
        return false;
      }
    },
    //Connecte l'usager quand il pese sur "creer un compte"
    async connexion() {
      this.erreur = "";

      try {
        // Récupération du token CSRF
        await fetchCsrfToken();

        // Appel à l'API
        const response = await api.post("/", {
          courriel: this.courriel,
          mot_de_passe: this.mot_de_passe,
        });

        //si la connexion a bien reussi
        if (response.status === 200) {
          // Mise à jour du store utilisateur
          const authStore = useAuthStore();
          await authStore.fetchUsager();

          //ajout d'une notification pour le catalogue
          const notif = useNotifStore();
          notif.montreMessage('Vous avez été connecté avec succès!', 'bloc-modale-succes');

          // Redirection vers le catalogue
          this.$router.push("/catalogue");
        }
      } catch (err) {
        // Catch les erreurs
        if (err.response && err.response.status === 422) {
          const errors = err.response.data.errors;
          if (errors) {
            this.erreur = Object.values(errors)[0][0];
          } else {
            this.erreur = err.response.data.message || "Erreur de validation";
          }
        } else if (err.response && err.response.status === 401) {
          this.erreur =
            err.response.data.message || "Courriel ou mot de passe incorrect";
        } else if (err.response) {
          this.erreur = err.response.data.message || "Erreur de connexion";
        } else if (err.request) {
          this.erreur = "Impossible de joindre le serveur";
        } else {
          this.erreur = "Une erreur est survenue";
        }
        // arreter l'affichage de 'Connexion'
      }
    },
  },
};
</script>
<<<<<<< HEAD
=======
<style scoped>
@media (min-width: 1024px) {
  .container {
    display: flex !important;
    flex-direction: row !important;
    align-items: center;
    justify-content: center;
    gap: 60px;
    max-width: 1200px;
    margin: 5rem auto;
  }

  .bloc-img {
    flex: 1;
    display: flex;
    justify-content: flex-end;
    margin: 0;
    max-height: 400px;
  }

  .bloc-img img {
    max-width: 350px;
    height: auto;
  }

  .bloc-form {
    flex: 1;
    max-width: 450px;
    margin: 0;
    text-align: left;
  }

  .signup-btn {
    width: 100%;
    margin-top: 1.5rem;
  }

  .already-txt {
    text-align: center;
    margin-top: 1rem;
  }
}
</style>
>>>>>>> refs/remotes/origin/main
