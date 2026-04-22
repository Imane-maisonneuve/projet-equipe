<template>
  <!-- Page dedie à la connexion des usagers -->
  <div class="container">
    <div class="bloc-img">
      <img src="../../assets/img/logo3.svg" alt="Logo de l'application" />
    </div>

    <!-- Affiche une notification -->
    <div v-if="notifStore.message" :class="['notif', notifStore.type]">
      {{ notifStore.message }}
    </div>

    <!-- Formulaire de connexion -->
    <form class="bloc-form" @submit.prevent="connexion">
      <div>
        <label for="courriel">Courriel</label>
        <input
          v-model="courriel"
          id="courriel"
          type="email"
          placeholder="exemple@email.com"
          required
        />
      </div>
      <!-- Mot de passe -->
      <div>
        <label for="mot_de_passe">Mot de passe</label>
        <input
          v-model="mot_de_passe"
          id="mot_de_passe"
          type="password"
          placeholder="6 caractères au minimum"
          required
        />
      </div>
      <!-- Bouton de soumission -->
      <button class="signup-btn" type="submit" :disabled="loading">
        {{ loading ? "Connexion..." : "Se Connecter" }}
      </button>
      <p v-if="erreur" class="erreur">{{ erreur }}</p>
      <p class="already-txt">
        Pas de compte ?
        <router-link to="/creation-usager"> S'inscrire </router-link>
      </p>
    </form>
  </div>
</template>

<script>
import api, { fetchCsrfToken } from "../../api";
import { useAuthStore } from "../../stores/auth";
import { useNotifStore } from '../../stores/notification';

export default {
  data() {
    return {
      courriel: "",
      mot_de_passe: "",
      erreur: "",
      loading: false,
    };
  },
  methods: {
    // Quand l'usager pese sur 'Se Connecter'
    async connexion() {
      this.loading = true;
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
      } finally {
        this.loading = false;
      }
    },
  },
  setup() {
    const notifStore = useNotifStore();
    return { notifStore };
  }
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
