<template>
    <div class="container">
      <div class="bloc-img">
        <img src="../../assets/img/image.png" />
        <div class="bloc-img-secondaire">Vino</div>
      </div>
        <form class="bloc-form" @submit.prevent="connexion">
            <h2>Connectez Vous</h2>
            <p class="already-txt">Pas de compte ?
              <router-link to="/creation-usager">
                S'inscrire
              </router-link>
            </p>
            <div>
                <label for="courriel">Courriel</label>
                <input v-model="courriel" id="courriel" type="email" placeholder="exemple@email.com" required>
            </div>
            <div >
                <label for="mot_de_passe">Mot de passe</label>
                <input v-model="mot_de_passe" id="mot_de_passe" type="password" placeholder="6 caractères au minimum" required>
            </div>
            <button class="signup-btn" type="submit" :disabled="loading">
                {{ loading ? 'Connexion...' : 'Se Connecter' }}
            </button>
            <p v-if="erreur" class="erreur">{{ erreur }}</p>
        </form>
    </div>
</template>

<script>
import api, { fetchCsrfToken } from "../../api";
import { useAuthStore } from "../../stores/auth";

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

        // Mise à jour du store utilisateur
        const authStore = useAuthStore();
        await authStore.fetchUsager();

        // Redirection vers le catalogue
        this.$router.push("/catalogue");

        // Catch les erreurs
      } catch (err) {
        if (err.response) {
          this.erreur = "Erreur de connexion";
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
};
</script>