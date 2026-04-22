<template>
  <!-- Affichage de la barre de navigation -->
  <Navbar />

  <div v-if="messageSucces" class="bloc-modale-succes">
    {{ messageSucces }}
  </div>
  <!-- Formulaire d'ajout de bouteille personnalisée -->
  <div>
    <form class="bloc-form" @submit.prevent="insererVin">
      <h1 class="profil-titre">Ajouter une bouteille</h1>

      <label for="nom">Nom *</label>
      <input
        id="nom"
        class="form-input"
        type="text"
        v-model="nom"
        aria-label="nom"
      />
      <div v-if="erreurs.nom" class="erreur">
        {{ erreurs.nom[0] }}
      </div>

      <label for="prix">Prix *</label>
      <input
        id="prix"
        class="form-input"
        type="number"
        v-model="prix"
        placeholder="Ex: 24.99"
        aria-label="prix"
      />
      <div v-if="erreurs.prix" class="erreur">
        {{ erreurs.prix[0] }}
      </div>

      <label for="pays">Pays</label>
      <select id="pays" v-model="pays" aria-label="Choisir un pays">
        <option disabled value="">Choisir un pays</option>
        <option v-for="pays in listePays" :key="pays" :value="pays">
          {{ pays }}
        </option>
      </select>
      <div v-if="erreurs.pays" class="erreur">
        {{ erreurs.pays[0] }}
      </div>

      <label for="region">Région</label>
      <input
        id="region"
        class="form-input"
        type="text"
        v-model="region"
        aria-label="Région"
      />
      <div v-if="erreurs.region" class="erreur">
        {{ erreurs.region[0] }}
      </div>

      <label for="cepage">Cépage</label>
      <input
        id="cepage"
        class="form-input"
        type="text"
        v-model="cepage"
        aria-label="Cépage"
      />
      <div v-if="erreurs.cepage" class="erreur">
        {{ erreurs.cepage[0] }}
      </div>

      <label for="degre_alcool">Degré d'alcool</label>
      <input
        id="degre_alcool"
        class="form-input"
        type="text"
        v-model="degre_alcool"
        placeholder="Ex: 13.5"
        aria-label="Degré d'alcool"
      />
      <div v-if="erreurs.degre_alcool" class="erreur">
        {{ erreurs.degre_alcool[0] }}
      </div>

      <label for="taux_sucre">Taux de sucre</label>
      <input
        id="taux_sucre"
        class="form-input"
        type="text"
        v-model="taux_sucre"
        placeholder="Ex: 3.2"
        aria-label="Taux de sucre"
      />
      <div v-if="erreurs.taux_sucre" class="erreur">
        {{ erreurs.taux_sucre[0] }}
      </div>

      <label for="format">Format (en ml)</label>
      <input
        id="format"
        class="form-input"
        type="text"
        v-model="format"
        placeholder="Ex: 750"
        aria-label="Format en ml"
      />
      <div v-if="erreurs.format" class="erreur">
        {{ erreurs.format[0] }}
      </div>

      <label for="annee">Année</label>
      <input
        id="annee"
        class="form-input"
        type="text"
        v-model="annee"
        placeholder="Ex: 2020"
        aria-label="Année de production"
      />
      <div v-if="erreurs.annee" class="erreur">
        {{ erreurs.annee[0] }}
      </div>

      <label for="couleur">Couleur</label>
      <select
        id="couleur"
        class="form-input"
        v-model="couleur"
        aria-label="Choisir une couleur"
      >
        <option disabled value="">Choisir une couleur</option>
        <option value="Rouge">Rouge</option>
        <option value="Blanc">Blanc</option>
        <option value="Rosé">Rosé</option>
        <option value="Orange">Orange</option>
        <option value="Ambré(e)">Ambré(e)</option>
        <option value="Brun(e)">Brun(e)</option>
      </select>
      <div v-if="erreurs.couleur" class="erreur">
        {{ erreurs.couleur[0] }}
      </div>

      <label for="quantite">Quantité de bouteilles *</label>
      <input
        id="quantite"
        class="form-input"
        type="number"
        v-model.number="quantite"
        placeholder="0"
        aria-label="Quantité de bouteilles"
      />
      <div v-if="erreurs.quantite" class="erreur">
        {{ erreurs.quantite[0] }}
      </div>
      <button type="submit" class="signup-btn btn">Enregistrer</button>

      <div v-if="message" class="erreur">
        {{ message }}
      </div>
    </form>
  </div>
</template>

<script>
import Navbar from "../../components/Navbar.vue";
import api from "../../api";
import { useNotifStore } from '../../stores/notification';

export default {
  components: {
    Navbar,
  },

  data() {
    return {
      cellier_id: "",
      nom: "",
      prix: "",
      pays: "",
      region: "",
      cepage: "",
      degre_alcool: "",
      taux_sucre: "",
      format: "",
      annee: "",
      couleur: "",
      quantite: "",
      listePays: [],
      erreurs: {},
      message: "",
      messageSucces: "",
    };
  },

  methods: {
    async insererVin() {
      this.erreurs = {};
      this.message = "";
      this.messageSucces = "";

      try {
        // récupérer l'id du cellier à partir de l'URL
        this.cellier_id = this.$route.params.id;

        // envoyer la requête POST pour creer la bouteille et l'ajouter au cellier
        const response = await api.post("/creer-bouteille-perso", {
          nom: this.nom,
          prix: this.prix,
          pays: this.pays,
          region: this.region,
          cepage: this.cepage,
          degre_alcool: this.degre_alcool,
          taux_sucre: this.taux_sucre,
          format: this.format,
          annee: this.annee,
          couleur: this.couleur,
          quantite: this.quantite,
          cellier_id: this.cellier_id,
        });

        // réinitialiser les champs du formulaire après l'ajout réussi
        this.messageSucces = "La bouteille a bien été ajoutée.";

        this.nom = "";
        this.prix = "";
        this.pays = "";
        this.region = "";
        this.cepage = "";
        this.degre_alcool = "";
        this.taux_sucre = "";
        this.format = "";
        this.annee = "";
        this.couleur = "";
        this.quantite = "";

        // afficher un message de succès et rediriger
        //ajout d'une notification pour le catalogue
        const notif = useNotifStore();
        notif.montreMessage('Votre bouteille a été ajoutée au cellier avec succès!', 'bloc-modale-succes');

        // rediriger vers la page de détail du cellier
        this.$router.push(`/detail-cellier/${this.cellier_id}`);

        // gestion des erreurs de validation et autres erreurs
      } catch (erreur) {
        this.erreurs = erreur.response.data.errors;
        if (erreur.response.data.message) {
          this.message = erreur.response.data.message;
        }
      }
    },
    // méthode pour récupérer la liste des pays depuis le backend
    async recupererPays() {
      try {
        const response = await api.get("/pays");
        this.listePays = response.data.listePays || [];
      } catch (erreur) {
        this.message = erreur.response.data.message;
      }
    },
  },
  // récupérer la liste des pays lorsque le composant est monté
  mounted() {
    this.recupererPays();
  },
};
</script>
