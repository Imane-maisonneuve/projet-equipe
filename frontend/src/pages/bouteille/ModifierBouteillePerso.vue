<template>
  <Navbar />

  <div v-if="messageSucces" class="bloc-modale-succes">
    {{ messageSucces }}
  </div>

  <div>
    <form class="bloc-form" @submit.prevent="modifierVin">
      <h1 class="profil-titre">Modifier une bouteille personnalisée</h1>

      <label>Nom</label>
      <input class="form-input" type="text" v-model="nom" />
      <div v-if="erreurs.nom" class="erreur">
        {{ erreurs.nom[0] }}
      </div>

      <label>Prix</label>
      <input
        class="form-input"
        type="number"
        v-model="prix"
        placeholder="Ex: 24.99"
      />
      <div v-if="erreurs.prix" class="erreur">
        {{ erreurs.prix[0] }}
      </div>

      <label>Pays</label>
      <select v-model="pays">
        <option disabled value="">Choisir un pays</option>
        <option v-for="pays in listePays" :key="pays" :value="pays">
          {{ pays }}
        </option>
      </select>
      <div v-if="erreurs.pays" class="erreur">
        {{ erreurs.pays[0] }}
      </div>

      <label>Région</label>
      <input class="form-input" type="text" v-model="region" />
      <div v-if="erreurs.region" class="erreur">
        {{ erreurs.region[0] }}
      </div>

      <label>Cépage</label>
      <input class="form-input" type="text" v-model="cepage" />
      <div v-if="erreurs.cepage" class="erreur">
        {{ erreurs.cepage[0] }}
      </div>

      <label>Degré d'alcool</label>
      <input
        class="form-input"
        type="text"
        v-model="degre_alcool"
        placeholder="Ex: 13.5"
      />
      <div v-if="erreurs.degre_alcool" class="erreur">
        {{ erreurs.degre_alcool[0] }}
      </div>

      <label>Taux de sucre</label>
      <input
        class="form-input"
        type="text"
        v-model="taux_sucre"
        placeholder="Ex: 3.2"
      />
      <div v-if="erreurs.taux_sucre" class="erreur">
        {{ erreurs.taux_sucre[0] }}
      </div>

      <label>Format (en ml)</label>
      <input
        class="form-input"
        type="text"
        v-model="format"
        placeholder="Ex: 750"
      />
      <div v-if="erreurs.format" class="erreur">
        {{ erreurs.format[0] }}
      </div>

      <label>Année</label>
      <input
        class="form-input"
        type="text"
        v-model="annee"
        placeholder="Ex: 2020"
      />
      <div v-if="erreurs.annee" class="erreur">
        {{ erreurs.annee[0] }}
      </div>

      <label>Couleur</label>
      <select class="form-input" v-model="couleur">
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

      <button type="submit" class="signup-btn btn">Modifier</button>

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
      listePays: [],
      erreurs: {},
      message: "",
      messageSucces: "",
    };
  },

  methods: {
    // Modifier une bouteille personnalisée
    async modifierVin() {
      this.erreurs = {};
      this.message = "";
      this.messageSucces = "";

      try {
        // Récupérer le SKU et l'ID du cellier depuis les paramètres de l'URL
        const sku = this.$route.params.sku;

        const cellier_id = null;

        if(this.$route.params.cellier_id){
          cellier_id = this.$route.params.cellier_id;
        }

        // envoyer la requête put pour modifier la bouteille
        const response = await api.put(`/modifier-bouteille-perso/${sku}`, {
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
        });

        // réinitialiser les champs du formulaire après la modification
        this.messageSucces = "La bouteille a bien été modifiée.";

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

        // afficher un message de succès et rediriger vers le catalogue après 2 secondes
        this.messageSucces = "Votre bouteille a été modifiée avec succès !";

        if(this.$route.params.cellier_id){
          setTimeout(() => {
            this.messageSucces = "";
            // rediriger vers la page de détail du cellier
            this.$router.push(`/detail-cellier/${cellier_id}`);
          }, 3000);
        }

        // gestion des erreurs de validation et autres erreurs
      } catch (erreur) {
        this.erreurs = erreur.response.data.errors;
        if (erreur.response.data.message) {
          this.message = erreur.response.data.message;
        }
      }
    },

    // méthode pour récupérer les détails du vin à modifier avec le SKU passé dans l'URL
    async recuperervin() {
      try {
        // Récupérer le SKU de la bouteille à modifier depuis les paramètres de l'URL
        const sku = this.$route.params.sku;
        const response = await api.get(`/vin/${sku}`);

        // Pré-remplir les champs du formulaire avec les données du vin récupéré
        const vin = response.data;
        this.nom = vin.nom;
        this.prix = vin.prix;
        this.pays = vin.pays;
        this.region = vin.region;
        this.cepage = vin.cepage;
        this.degre_alcool = vin.degre_alcool;
        this.taux_sucre = vin.taux_sucre;
        this.format = vin.format;
        this.annee = vin.annee;
        this.couleur = vin.couleur;
      } catch (erreur) {
        console.error("Erreur lors de la récupération du vin:", erreur);
      }
    },

    // méthode pour récupérer la liste des pays depuis le backend
    async recupererPays() {
      try {
        // Envoyer une requête GET à l'API pour récupérer la liste des pays
        const response = await api.get("/pays");
        this.listePays = response.data.listePays || [];
      } catch (erreur) {
        this.message = erreur.response.data.message;
      }
    },
  },
  mounted() {
    // Appeler les méthodes pour récupérer les détails du vin et la liste des pays lorsque le composant est monté
    this.recuperervin();
    this.recupererPays();
  },
};
</script>
