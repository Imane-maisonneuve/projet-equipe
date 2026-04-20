<template>
  <Navbar />

  <div class="banniere">
    <h1 class="banniere-titre">Recherche bouteille dans les celliers</h1>
  </div>

  <div class="search-container">
    <Search class="search-icon" />
    <input
      v-model="search"
      type="text"
      placeholder="Rechercher une bouteille..."
      class="search-input"
    />
  </div>

  <div class="btn-recherche">
    <button class="btn btn-entete-cellier" @click="toggleFilter">
      <ListFilter class="icon" />
      <span>Filtrer</span>
    </button>

    <button class="btn btn-entete-cellier" @click="showTri = true">
      <ArrowDownNarrowWide class="icon" />
      <span>Trier</span>
    </button>
  </div>

  <div
    class="filtre-ouvrir"
    :class="{ active: showFilter }"
    @click="toggleFilter"
  ></div>

  <aside class="filter-panel" :class="{ active: showFilter }">
    <div class="filter-header">
      <h2>Filtres</h2>
    </div>
    <button class="reset-filters" @click="reinitialiserFiltres">
      Réinitialiser les filtres
    </button>
    <ul class="filter-list">
      <ColorFilter v-model="selected.couleur" />

      <FilterSelect
        :key="selected.reinitialiser"
        label="Pays"
        :items="filters.countries"
        v-model="selected.countries"
      />

      <FilterSelect
        :key="selected.reinitialiser"
        label="Régions"
        :items="filters.regions"
        v-model="selected.regions"
      />

      <FilterSelect
        :key="selected.reinitialiser"
        label="Cépages"
        :items="filters.cepages"
        v-model="selected.cepages"
      />

      <FourchetteFiltre
        v-if="filters.prix"
        :key="selected.reinitialiser"
        v-model="selected.prix"
        :minLimit="safeNumber(filters.prix.min)"
        :maxLimit="safeNumber(filters.prix.max)"
      />

      <FourchetteFiltre
        :key="selected.reinitialiser"
        v-model="selected.format"
        :minLimit="safeNumber(filters.format.min)"
        :maxLimit="safeNumber(filters.format.max)"
        label="Format (ml)"
      />

      <FourchetteFiltre
        :key="selected.reinitialiser"
        v-model="selected.degres"
        :minLimit="safeNumber(filters.degres.min)"
        :maxLimit="safeNumber(filters.degres.max)"
        label="Degré (%)"
      />
      <AnneeFiltreSelect
        :key="selected.reinitialiser"
        :items="filters.millesimes"
        v-model="selected.millesimes"
        label="Millésimes"
      />
    </ul>
  </aside>

  <ModalTri
    :show="showTri"
    :tri="tri"
    @apply="appliquerTri"
    @close="showTri = false"
  />

  <div class="liste-bouteilles">
    <div
      v-for="bouteille in bouteilles"
      :key="bouteille.id"
      class="carte-bouteille"
    >
      <img :src="bouteille.vin.image_url" class="image-vin" />

      <div class="info">
        <h3>{{ bouteille.vin.nom }}</h3>
        <p>Cellier : {{ bouteille.cellier.nom }}</p>
        <p>Prix : {{ bouteille.vin.prix }}$</p>
        <p>Quantité : {{ bouteille.quantite }}</p>

        <button
          @click="modifierQuantiteVin(bouteille.quantite - 1, bouteille.id)"
          class="btn-qte"
          :disabled="bouteille.quantite <= 1"
        >
          <CircleMinus />
        </button>

        <button
          @click="modifierQuantiteVin(bouteille.quantite + 1, bouteille.id)"
          class="btn-qte"
        >
          <CirclePlus />
        </button>
      </div>

      <div class="bouton-cellier">
        <button @click="ouvrirModale(bouteille.id)" class="btn btn-cellier">
          <Trash />
        </button>

        <button @click="voirDetail(bouteille.id)" class="btn btn-cellier">
          <Eye />
        </button>

        <button
          class="btn btn-cellier"
          @click="ajouterListeAchats(bouteille.id)"
        >
          <ShoppingBasket />
        </button>
      </div>

      <div v-if="bouteille.messageAjout" class="bloc-modale-succes">
        {{ bouteille.messageAjout }}
      </div>

      <div v-if="bouteille.messageErreur" class="erreur">
        {{ bouteille.messageErreur }}
      </div>
    </div>
  </div>

  <ModalConfirmation
    :show="afficherModale"
    message="Voulez-vous supprimer ce vin de ce cellier ?"
    confirmText="Supprimer"
    cancelText="Annuler"
    @confirm="confirmerSuppression"
    @cancel="afficherModale = false"
  />

  <div class="espacement"></div>
</template>

<script>
import Navbar from "../../components/Navbar.vue";
import {
  Search,
  ListFilter,
  ArrowDownNarrowWide,
  Trash,
  Eye,
  CirclePlus,
  CircleMinus,
  ShoppingBasket,
} from "lucide-vue-next";

import FourchetteFiltre from "../../components/Fourchette.vue";
import FilterSelect from "../../components/FiltreSelect.vue";
import AnneeFiltreSelect from "../../components/AnneeFiltreSelect.vue";
import ColorFilter from "../../components/ColorFilter.vue";
import ModalTri from "../../components/ModalTri.vue";
import ModalConfirmation from "../../components/ModalConfirmation.vue";
import { useWineStore } from "../../stores/wineStore";

import axios from "axios";
import api, { fetchCsrfToken } from "../../api";

export default {
  components: {
    Navbar,
    Search,
    ListFilter,
    ArrowDownNarrowWide,
    Trash,
    Eye,
    CirclePlus,
    CircleMinus,
    ShoppingBasket,
    FourchetteFiltre,
    FilterSelect,
    AnneeFiltreSelect,
    ColorFilter,
    ModalTri,
    ModalConfirmation,
  },

  data() {
    return {
      search: "",
      bouteilles: [],
      showFilter: false,
      showTri: false,
      tri: 0,

      selected: {
        countries: [],
        regions: [],
        cepages: [],
        prix: { min: null, max: null },
        format: { min: null, max: null },
        degres: { min: null, max: null },
        millesimes: [],
        couleur: [],
        reinitialiser: 0,
      },

      filters: {
        countries: [],
        regions: [],
        cepages: [],
        prix: { min: 0, max: 0 },
        format: { min: 0, max: 0 },
        degres: { min: 0, max: 0 },
        millesimes: [],
        couleur: [],
      },

      afficherModale: false,
      idASupprimer: null,
    };
  },

  watch: {
    search() {
      this.fetchBouteilles();
    },

    selected: {
      handler() {
        this.fetchBouteilles();
      },
      deep: true,
    },
  },

  methods: {
    getMinMax(array) {
      if (!Array.isArray(array) || array.length === 0) {
        return { min: 0, max: 0 };
      }

      const nums = array.map((v) => Number(v)).filter((v) => !isNaN(v));

      return {
        min: Math.min(...nums),
        max: Math.max(...nums),
      };
    },
    safeNumber(val) {
      const n = Number(val);
      return isNaN(n) ? 0 : n;
    },
    toggleFilter() {
      this.showFilter = !this.showFilter;
    },

    appliquerTri(val) {
      this.tri = val;
      this.fetchBouteilles();
      this.showTri = false;
    },

    async fetchBouteilles() {
      try {
        const filters = {};

        if (this.selected.countries.length)
          filters.countries = this.selected.countries;

        if (this.selected.regions.length)
          filters.regions = this.selected.regions;

        if (this.selected.cepages.length)
          filters.cepages = this.selected.cepages;

        if (
          this.selected.prix &&
          (this.selected.prix.min != null || this.selected.prix.max != null)
        ) {
          filters.prix = this.selected.prix;
        }

        if (
          this.selected.format &&
          (this.selected.format.min != null || this.selected.format.max != null)
        ) {
          filters.format = this.selected.format;
        }

        if (
          this.selected.degres &&
          (this.selected.degres.min != null || this.selected.degres.max != null)
        ) {
          filters.degres = this.selected.degres;
        }

        if (this.selected.millesimes.length)
          filters.millesimes = this.selected.millesimes;

        if (this.selected.couleur.length)
          filters.couleur = this.selected.couleur;

        const res = await axios.get("/api/bouteilles", {
          params: {
            recherche: this.search,
            filters,
            tri: this.tri,
          },
        });

        this.bouteilles = res.data.data || [];

        if (res.data.filters) {
          this.filters = {
            countries: res.data.filters.countries || [],
            regions: res.data.filters.regions || [],
            cepages: res.data.filters.cepages || [],
            couleur: res.data.filters.couleur || [],

            prix: this.getMinMax(res.data.filters.prix),
            format: this.getMinMax(res.data.filters.formats),
            degres: this.getMinMax(res.data.filters.degres),

            millesimes: res.data.filters.millesimes || [],
          };
        }
      } catch (e) {
        console.error(e);
      }
    },

    async modifierQuantiteVin(qte, id) {
      if (qte < 1) return;

      await fetchCsrfToken();
      await api.put(`/modifier-quantite/${id}`, { quantite: qte });

      this.fetchBouteilles();
    },

    ouvrirModale(id) {
      this.idASupprimer = id;
      this.afficherModale = true;
    },

    async confirmerSuppression() {
      await api.delete(`/cellier-vins/${this.idASupprimer}`);

      this.bouteilles = this.bouteilles.filter(
        (b) => b.id !== this.idASupprimer,
      );

      this.afficherModale = false;
    },

    voirDetail(id) {
      this.$router.push(`/cellier-vin/${id}`);
    },

    reinitialiserFiltres() {
      this.selected = {
        countries: [],
        regions: [],
        cepages: [],
        prix: { min: null, max: null },
        format: { min: null, max: null },
        degres: { min: null, max: null },
        millesimes: [],
        couleur: [],
        reinitialiser: this.selected.reinitialiser + 1,
      };

      this.search = "";
      this.fetchBouteilles();
    },
  },

  mounted() {
    this.fetchBouteilles();
  },
};
</script>
