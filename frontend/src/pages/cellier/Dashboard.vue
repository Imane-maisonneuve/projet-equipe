<template>
  <Navbar />

  <div class="banniere">
    <h1 class="banniere-titre">Mes celliers</h1>
  </div>

  <div class="entete-cellier">
    <h2></h2>
    <button class="btn btn-entete-cellier" @click="creerCellier">
      <Plus class="icon" /> <span>Nouveau</span>
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
import api, { fetchCsrfToken } from "../../api";
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
        await fetchCsrfToken();
        // on attend que la requête DELETE se termine
        await api.delete(`/supprimer-cellier/${this.idASupprimer}`);

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
