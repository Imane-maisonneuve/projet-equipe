<template>
  <!-- Affichage de la barre de navigation -->
  <Navbar />
  <div v-if="messageSucces" class="bloc-modale-succes">
    {{ messageSucces }}
  </div>
  <!-- Formulaire pour ajouter une bouteille au cellier -->
  <div>
    <form class="bloc-form" @submit.prevent="ajouterBouteille">
      <h1 class="profil-titre">Ajouter la bouteille au cellier</h1>
      <label>Nom du cellier </label>
      <!-- Affichage d'un menu déroulant pour sélectionner le cellier où ajouter la bouteille -->
      <select v-model="cellier_id" aria-label="Choisir un cellier">
        <option disabled value="">Choisir un cellier</option>
        <option
          v-for="cellier in celliers"
          :key="cellier.id"
          :value="cellier.id"
        >
          {{ cellier.nom }}
        </option>
      </select>
      <div v-if="erreurs.cellier_id" class="erreur">
        {{ erreurs.cellier_id[0] }}
      </div>
      <!-- Champ pour saisir la quantité de bouteilles à ajouter -->
      <label for="quantite">Quantité de bouteilles </label>
      <input
        id="quantite"
        class="form-input"
        type="number"
        v-model.number="quantite"
        min="1"
        step="1"
        @keydown="bloquerNegatif"
        @input="corrigerValeur"
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
import { useNotifStore } from "../../stores/notification";

export default {
  components: {
    Navbar,
  },
  data() {
    return {
      vin: null,
      celliers: [],
      erreurs: {},
      cellier_id: "",
      quantite: "",
      vin_id: 0,
      message: "",
      messageSucces: "",
    };
  },
  methods: {
    // méthode pour récupérer les celliers de l'utilisateur
    async recupererCelliers() {
      try {
        const response = await api.get("/celliers");
        this.celliers = response.data.data;
      } catch (erreur) {
        this.message = "Une erreur est survenue";
      }
    },
    // méthode pour ajouter une bouteille au cellier
    async ajouterBouteille() {
      try {
        this.message = "";
        this.erreurs = {};
        this.messageSucces = "";

        // récupérer l'id de la bouteille à partir de l'URL
        this.vin_id = this.$route.params.id;

        // envoyer la requête POST pour ajouter la bouteille au cellier
        const response = await api.post("/ajouter-bouteille", {
          cellier_id: this.cellier_id,
          vin_id: this.vin_id,
          quantite: this.quantite,
        });

        this.cellierVin = response.data;

        //envoy une notification au catalogue, une fois qu'on y retourne
        const notif = useNotifStore();
        notif.montreMessage(
          "Votre bouteille a été ajoutée au cellier avec succès!",
          "bloc-modale-succes"
        );

        this.$router.back();
      } catch (erreur) {
        if (erreur.response.data.errors) {
          this.erreurs = erreur.response.data.errors;
        } else {
          if (erreur.response.data.message) {
            this.message = erreur.response.data.message;

            //envoyer une notification d'erreurs a la page precedante, une fois qu'on y retourne
            const notif = useNotifStore();
            notif.montreMessage(this.message, "erreur");

            this.$router.push("/catalogue");
            this.$router.back();
          }
        }
      }
    },
    // bloque nombre négatif
    bloquerNegatif(e) {
      if (e.key === "-" || e.key === "e") {
        e.preventDefault();
      }
    },
    // corrige nombre négatif par positif
    corrigerValeur() {
      if (this.quantite < 0) {
        this.quantite = 0;
      }
    },
  },
  // récupérer les informations de la bouteille dès que le composant est monté
  mounted() {
    this.recupererCelliers();
  },
};
</script>
