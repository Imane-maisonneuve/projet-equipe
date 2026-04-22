<template>
  <!-- Affichage des messages de succès et d'erreur -->
  <div v-if="messageSucces" class="bloc-modale-succes">
    {{ this.messageSucces }}
  </div>
  <!-- Affichage du message d'erreur -->
  <div v-if="message" class="erreur">
    {{ this.message }}
  </div>
  <!-- Affichage de la carte du vin dans le cellier -->
  <div class="nom-cellier">
    <div class="vin-cellier-carte">
      <img
        :src="vin.image_url"
        :alt="'Le nom du vin est : ' + vin.nom"
        class="cellier-img"
      />
      <div>
        <h2 class="nom">{{ vin.nom }}</h2>
        <p class="meta">Quantité: {{ quantite }}</p>
        <!-- Boutons pour modifier la quantité de bouteilles -->
        <button
          @click="modifierQuantiteVin(quantite - 1)"
          :disabled="quantite <= 1"
          class="btn-qte"
          aria-label="Réduire la quantité"
        >
          <CircleMinus />
        </button>
        <button
          @click="modifierQuantiteVin(quantite + 1)"
          class="btn-qte"
          aria-label="Augmenter la quantité"
        >
          <CirclePlus />
        </button>
      </div>
    </div>
    <!-- Boutons pour voir les détails, ajouter à la liste d'achats et supprimer le vin du cellier -->
    <div class="bouton-cellier">
      <button
        class="btn btn-cellier"
        @click="voirDetail"
        aria-label="Voir les détails"
      >
        <Eye class="icons" />
      </button>
      <!-- Bouton pour ajouter le vin à la liste d'achats -->
      <button
        class="btn btn-cellier"
        @click="ajouterListeAchats"
        aria-label="Ajouter à la liste d'achats"
      >
        <ShoppingBasket class="icons" />
      </button>
      <!-- Bouton pour supprimer le vin du cellier, émet un événement vers le parent pour ouvrir une modale de confirmation -->
      <button
        class="btn btn-cellier"
        @click="$emit('ouvrir-modale', id)"
        aria-label="Supprimer du cellier"
      >
        <Trash class="icons" />
      </button>
    </div>
  </div>
</template>

<script>
import {
  Trash,
  PencilLine,
  Eye,
  CirclePlus,
  CircleMinus,
  ShoppingBasket,
} from "lucide-vue-next";
import api, { fetchCsrfToken } from "../api";
import { useAuthStore } from "../stores/auth";

export default {
  components: {
    Trash,
    PencilLine,
    Eye,
    CirclePlus,
    CircleMinus,
    ShoppingBasket,
  },
  props: {
    vin: Object,
    quantite: Number,
    id: Number,
  },
  emits: ["ouvrir-modale", "update-quantite"],
  data() {
    return {
      erreur: "",
      messageSucces: "",
      message: "",
    };
  },
  methods: {
    // Navigation vers la page de détails du vin
    voirDetail() {
      this.$router.push(`/cellier-vin/${this.id}`);
    },
    // Envoie de requete pour modifier le nombre des bouteilles
    async modifierQuantiteVin(nouvelleQuantite) {
      if (nouvelleQuantite < 1) return;

      try {
        await fetchCsrfToken();
        //appel api pour modifier la quantité dans la BD
        await api.put(`/modifier-quantite/${this.id}`, {
          quantite: nouvelleQuantite,
        });

        // envoyer au parent
        this.$emit("update-quantite", {
          id: this.id,
          quantite: nouvelleQuantite,
        });
      } catch (erreur) {
        console.error(erreur);
      }
    },
    // Envoie de requete pour ajouter le vin à la liste d'achats
    async ajouterListeAchats() {
      try {
        this.message = "";
        this.messageSucces = "";

        // Récupérer l'utilisateur connecté
        const authStore = useAuthStore();
        await authStore.fetchUsager();
        const usagerId = authStore.usager.id;

        // Récupérer l'id du vin
        const vinId = this.vin.id;

        //appel api pour ajouter a la BD
        const response = await api.post("/ajouter-bouteille-liste", {
          usager_id: usagerId,
          vin_id: vinId,
        });

        // afficher un message de succès
        this.messageSucces =
          "Votre bouteille a été ajoutée a la liste d'achat avec succès !";
        setTimeout(() => {
          this.messageSucces = "";
        }, 2000);
      } catch (erreur) {
        // afficher message d'erreur
        this.message = "Cette bouteille est déjà dans votre liste d’achats";
        setTimeout(() => {
          this.message = "";
        }, 4000);
      }
    },
  },
};
</script>
