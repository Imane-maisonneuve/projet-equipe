<template>
  <!-- Affichage conditionnel des messages de succès et d'erreur -->
  <div v-if="messageSucces" class="bloc-modale-succes">
    {{ this.messageSucces }}
  </div>

  <div v-if="message" class="erreur">
    {{ this.message }}
  </div>
  <!-- Carte de vin dans le catalogue -->
  <div class="catalogue-carte">
    <div class="media">
      <div class="image-conteneur">
        <img
          :src="vin.image_url"
          class="image"
          :alt="'Le nom du vin est : ' + vin.nom"
        />
        <div class="prix">{{ prixFormate }}$</div>
      </div>

      <div class="contenu">
        <h2 class="nom">{{ vin.nom }}</h2>
        <p class="meta">{{ vin.pays }}</p>
        <p class="meta couleur">
          {{ vin.couleur }}
          <span
            class="couleur-point"
            :style="{ backgroundColor: chercherCouleur(vin.couleur) }"
          ></span>
        </p>
        <div class="catalogue-carte-actions">
          <!-- Lien pour ajouter la bouteille au cellier -->
          <router-link
            class="catalogue-carte-btn"
            :to="`/bouteille/AjouterBouteille/${vin.id}`"
          >
            Ajouter au cellier
          </router-link>
          <!-- Bouton pour ajouter la bouteille à la liste d'achats -->
          <button
            class="liste-btn catalogue-carte-btn btn-achat"
            @click="ajouterListeAchats"
            aria-label="Ajouter à la liste d'achats"
          >
            <ShoppingBasket class="icons" />
          </button>
        </div>
      </div>
    </div>
    <!-- Affichage des informations supplémentaires au survol -->
    <div class="hover-info" :class="{ active: montrerInfo }">
      <div class="info-media">
        <p>Région : {{ vin.region }}</p>
        <p>Cépage : {{ vin.cepage }}</p>
        <p>Alcool : {{ Number(vin.degre_alcool).toFixed(1) }} %</p>
        <p>Sucre : {{ vin.taux_sucre }}</p>
        <p>Millésime : {{ vin.annee }}</p>
      </div>
    </div>
    <!-- Bouton pour basculer l'affichage des informations supplémentaires -->
    <button class="info-btn" @click.stop="toggleInfo">
      {{ montrerInfo ? "↓" : "↑" }}
      <span class="info-fleche">Infos</span>
    </button>
  </div>
</template>

<script>
import { ShoppingBasket } from "lucide-vue-next";
import api from "../api";
import { useAuthStore } from "../stores/auth";

export default {
  components: {
    ShoppingBasket,
  },

  props: {
    vin: Object,
  },

  data() {
    return {
      montrerInfo: false,
      messageSucces: "",
      message: "",
    };
  },

  computed: {
    // Calcul du badge en fonction du nom du vin
    badge() {
      return this.chercherBadge(this.vin);
    },
    // Formatage du taux d'alcool
    alcoolFormatter() {
      if (!this.vin.alcohol) return "-";
      const num = parseFloat(this.vin.alcohol);
      if (isNaN(num)) return this.vin.alcohol;
      return num.toFixed(2);
    },
    // Formatage du prix
    prixFormate() {
      if (!this.vin.prix) return "0.00";
      return Number(this.vin.prix).toFixed(2);
    },
  },

  methods: {
    // Fonction pour déterminer la couleur du point en fonction de la couleur du vin
    chercherCouleur(couleur) {
      if (!couleur) return "#ccc";

      const c = couleur.toLowerCase();

      if (c.includes("rouge")) return "#8b0000";
      if (c.includes("blanc")) return "#f1c40f";
      if (c.includes("rosé") || c.includes("rose")) return "#ff6b81";
      if (c.includes("orange")) return "#e67e22";
      if (c.includes("ambré(e)") || c.includes("ambre")) return "#FFBF00";
      if (c.includes("doré(e)") || c.includes("dore")) return "#FFD700";

      return "#999";
    },
    // Fonction pour basculer l'affichage des informations supplémentaires
    toggleInfo() {
      this.montrerInfo = !this.montrerInfo;
    },
    // Fonction pour déterminer le badge en fonction du nom du vin
    chercherBadge(vin) {
      const name = vin.name.toLowerCase();

      if (name.includes("malbec")) {
        return { text: "DÉLICAT ET LÉGER", color: "#8b0000" };
      }

      if (name.includes("chardonnay")) {
        return { text: "FRUITÉ ET GÉNÉREUX", color: "#e67e22" };
      }

      if (name.includes("rosé")) {
        return { text: "FRAIS ET VIF", color: "#ff6b81" };
      }

      if (wine.country === "France") {
        return { text: "AROMATIQUE ET SOUPLE", color: "#2c3e50" };
      }

      return { text: "AROMATIQUE ET ROND", color: "#c0392b" };
    },
    // Fonction pour ajouter la bouteille à la liste d'achats
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
<style scoped>
@media (min-width: 1024px) {
  .catalogue-carte {
    border: 1px solid var(--gris-clair);
    box-shadow: 0 2px 8px var(--noir-semi-transparent);
    border-radius: 15px;
    background: var(--blanc-clair);
  }

  .info-btn,
  .fixed-btn,
  .catalogue-carte-btn,
  .liste-btn {
    border: 1px solid var(--gris-clair);
    box-shadow: 0 1px 3px var(--noir-tres-transparent);
  }

  .catalogue-carte:hover {
    border-color: var(--gris-semi-transparent);
    box-shadow: 0 4px 12px var(--noir-semi-transparent);
    transform: translateY(-2px);
  }
}
</style>
