<template>
  <Navbar />
  <div class="hero-container">
    <img src="../../assets/img/cellier.jpg" class="hero-image" />
    <img src="../../assets/img/bouteille.png" class="bouteille" />
  </div>

  <div class="infos">
    <div class="infos-stock">
      <img
        src="../../assets/img/bouteille.png"
        alt="bouteille vin"
        class="bouteille-total"
      />
      <p>En stock:<strong>13</strong></p>
    </div>
    <div class="infos-valeur">
      <img
        src="../../assets/img/valeur.png"
        alt="image monnaie"
        class="valeur-img"
      />

      <p>Valeur :<strong>256 $</strong></p>
    </div>
  </div>
  <div class="entete-cellier">
    <h2>Vos celliers</h2>
    <button class="btn btn-entete-cellier" @click="creerCellier">
      <Plus class="icon" /> <span>Créer cellier</span>
    </button>
  </div>
  <Cellier
    v-for="cellier in celliers"
    :key="cellier.id"
    :cellier="cellier"
    @ouvrir-modale="ouvrirModale"
  />
  <ModalConfirmation
    :show="afficherModale"
    message="Voulez-vous supprimer ce cellier ?"
    confirmText="Supprimer"
    cancelText="Annuler"
    @confirm="confirmerSuppression"
    @cancel="afficherModale = false"
  />
</template>

<script>
import axios from "axios";
import Cellier from "../../components/Cellier.vue";
import { Plus } from "lucide-vue-next";
import Navbar from "../../components/Navbar.vue";
import ModalConfirmation from "../../components/ModalConfirmation.vue";
export default {
  components: {
    Cellier,
    Plus,
    Navbar,
    ModalConfirmation,
  },

  data() {
    return {
      celliers: [],
      afficherModale: false,
      idASupprimer: null,
    };
  },

  mounted() {
    this.getCelliers();
  },

  methods: {
    async getCelliers() {
      try {
        const response = await axios.get("/api/celliers");
        this.celliers = response.data.data;
      } catch (error) {
        console.error(error);
      }
    },
    creerCellier() {
      this.$router.push("/creer-cellier");
    },
    ouvrirModale(id) {
      this.idASupprimer = id;
      this.afficherModale = true;
    },
    async confirmerSuppression() {
      try {
        // on attend que la requête DELETE se termine
        await axios.delete(
          `http://127.0.0.1:8000/api/supprimer-cellier/${this.idASupprimer}`
        );

        // mettre à jour la liste des celliers
        this.celliers = this.celliers.filter((c) => c.id !== this.idASupprimer);

        // fermer la modale
        this.afficherModale = false;
      } catch (err) {
        console.error(err);
      }
    },
    supprimerDansListe(id) {
      this.celliers = this.celliers.filter((c) => c.id !== id);
    },
  },
};
</script>

<style scoped></style>
