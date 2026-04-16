<template>
  <div class="container">
    <div class="bloc-img">
      <img src="../../assets/img/image.png" />
      <div class="bloc-img-secondaire">Vino</div>
    </div>
    <form @submit.prevent="gererSoumission" class="bloc-form">
      <h2>Créer un compte</h2>
      <p class="already-txt">Déjà membre ?
        <router-link to="/connexion-usager">
          Se Connecter
        </router-link>
      </p>
      <!-- Nom -->
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

      <button type="submit" class="signup-btn">Créer un compte</button>
    </form>
  </div>
</template>
<script>
import axios from "axios";
import api, { fetchCsrfToken } from "../../api";
import { useAuthStore } from "../../stores/auth";

export default {
  data() {
    return {
      nom: "",
      courriel: "",
      mot_de_passe: "",
      erreurs: {},
    };
  },
  methods: {
    async gererSoumission() {
      await this.creerUsager();
      await this.connexion();
    },
    // creer l'usager quand il pese sur "creer un compte"
    async creerUsager() {
      console.log("Méthode creerUsager appelée !");
      try {
        const response = await axios.post("http://localhost:8000/api/usagers", {
          nom: this.nom,
          courriel: this.courriel,
          mot_de_passe: this.mot_de_passe,
        });

        console.log(response.data);
      } catch (erreur) {
        if (erreur.response && erreur.response.status === 422) {
          this.erreurs = erreur.response.data.erreurs;
        }
      }
    },
    //Connecte l'usager quand il pese sur "creer un compte"
    async connexion() {

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
      }
    },
  },
};
</script>
