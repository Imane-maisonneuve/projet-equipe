<template>
    <Navbar />

    <div class="banniere">
        <h1 class="banniere-titre">Cellier: {{ cellierNom }}</h1>
    </div>

    <VinCellierCarte
        v-for="item in vins"
        :key="item.id"
        :vin="item.vin"
        :quantite="item.quantite"
        @ouvrir-modale="ouvrirModale"
    />

</template>

<script>
import api from "../../api";
import Navbar from "../../components/Navbar.vue";
import VinCellierCarte from "../../components/VinCellierCarte.vue";
import ModalConfirmation from "../../components/ModalConfirmation.vue";

export default {
  components: {
    Navbar,
    VinCellierCarte,
    ModalConfirmation,
  },
  data() {
    return {
      cellierNom: "",
      vins: [],
      erreur: "",
      afficherModale: false,
      idASupprimer: null,
    };
  },
  methods: {
    async afficherDetailCellier() {
      try {
        const id = this.$route.params.id;
        const reponse = await api.get(`/detail-cellier/${id}`);

        this.cellierNom = reponse.data.cellier.nom;
        this.vins = reponse.data.vins;

      } catch (erreur) {
        this.erreur = "Erreur lors de l'affichage des details de ce cellier";
      }
    },
    ouvrirModale(id) {
      this.idASupprimer = id;
      this.afficherModale = true;
    },
    async confirmerSuppression() {
        try{

        }catch(err) {
            this.erreur = "Erreur lors de la suppression d'une bouteille dans ce cellier";
        }
    }
  },
  mounted() {
    this.afficherDetailCellier();
  },
};
</script>