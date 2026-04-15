<template>
  <Navbar />
  <div class="home">
    <div class="banniere">
      <h2 class="banniere-titre">Catalogue des vins</h2>
    </div>
    <div class="search-container">
      <Search class="search-icon" />
      <input
        type="text"
        v-model="termeDeRecherche"
        placeholder="Rechercher un vin par nom..."
        @input="rechercherVins"
        class="search-input"
      />
    </div>
    <div class="filtre">
      <div class="btn-recherche catalogue">
        <button class="btn btn-entete-cellier" @click="toggleFilter">
          <ListFilter class="icon" /><span>Filtrer </span>
        </button>
        <button class="btn btn-entete-cellier">
          <ArrowDownUp class="icon" /><span>Trier </span>
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

        <ul class="filter-list">
          <ColorFilter v-model="selected.couleur" />
          <FilterSection
            title="Pays"
            :items="countries"
            v-model="selected.countries"
            clearable
          />
          <FilterSection
            title="Régions"
            :items="regions"
            v-model="selected.regions"
            clearable
          />
          <FilterSection
            title="Cépages"
            :items="cepages"
            v-model="selected.cepages"
            clearable
          />
          <FilterSection
            title="Prix ($)"
            :items="prix"
            v-model="selected.prix"
            clearable
          />
          <FilterSection
            title="Format (ml)"
            :items="formats"
            v-model="selected.formats"
            clearable
          />
          <FilterSection
            title="Degré (%)"
            :items="degres"
            :formatter="formatAlcohol"
            v-model="selected.degres"
            clearable
          />

          <FilterSection
            title="Millésime"
            :items="millesimes"
            v-model="selected.millesimes"
            clearable
          />
        </ul>
      </aside>
    </div>

    <WineGrid v-if="!loading" :vins="vins" />

    <Pagination
      v-if="!loading && totalPages > 1"
      :page="page"
      :totalPages="totalPages"
      :perPage="perPage"
      @next="nextPage"
      @prev="prevPage"
      @changePerPage="changePerPage"
    />
    <div class="espacement"></div>
  </div>
</template>

<script>
import { useWineStore } from "../stores/wineStore";
import WineGrid from "../components/WineGrid.vue";
import Navbar from "../components/Navbar.vue";
import Pagination from "../components/Pagination.vue";
import { Search, ListFilter, ArrowDownUp } from "lucide-vue-next";
import FilterSection from "../components/FilterSelection.vue";
import ColorFilter from "../components/ColorFilter.vue";

export default {
  components: {
    WineGrid,
    Navbar,
    Pagination,
    Search,
    ListFilter,
    ArrowDownUp,
    FilterSection,
    ColorFilter,
  },

  data() {
    return {
      showFilter: false,
      page: 1,
      perPage: 12,
      selected: {
        countries: [],
        regions: [],
        cepages: [],
        prix: [],
        formats: [],
        degres: [],
        millesimes: [],
        couleur: [],
      },
      wineStore: useWineStore(),
      termeDeRecherche: "",
    };
  },

  computed: {
    vins() {
      return this.wineStore.wines;
    },

    loading() {
      return this.wineStore.loading;
    },

    totalPages() {
      return Math.ceil(this.wineStore.total / this.perPage);
    },
    countries() {
      return this.wineStore.filters.countries || [];
    },
    regions() {
      return this.wineStore.filters.regions || [];
    },
    cepages() {
      return this.wineStore.filters.cepages || [];
    },
    prix() {
      return this.wineStore.filters.prix || [];
    },
    formats() {
      return this.wineStore.filters.formats || [];
    },
    degres() {
      return this.wineStore.filters.degres || [];
    },
    millesimes() {
      return this.wineStore.filters.millesimes || [];
    },

    total() {
      return this.wineStore.total;
    },

    debut() {
      if (this.total === 0) return 0;
      return (this.page - 1) * this.perPage + 1;
    },

    fin() {
      const fin = this.page * this.perPage;
      return fin > this.total ? this.total : fin;
    },
  },

  watch: {
    selected: {
      handler() {
        this.page = 1;
        this.fetchWines();
      },
      deep: true,
    },
    page() {
      this.fetchWines();
    },
    perPage() {
      this.page = 1;
      this.fetchWines();
    },
  },

  methods: {
    formatAlcohol(value) {
      if (!value) return "-";
      const num = parseFloat(value);
      return isNaN(num) ? value : num.toFixed(2);
    },

    toggleFilter() {
      this.showFilter = !this.showFilter;
    },

    async fetchWines() {
      const filters = {};
      if (this.selected.countries.length)
        filters.countries = this.selected.countries;
      if (this.selected.regions.length) filters.regions = this.selected.regions;
      if (this.selected.cepages.length) filters.cepages = this.selected.cepages;
      if (this.selected.prix.length) {
        filters.prix = this.selected.prix;
      }
      if (this.selected.formats.length) filters.formats = this.selected.formats;
      if (this.selected.degres.length) filters.degres = this.selected.degres;
      if (this.selected.millesimes.length)
        filters.millesimes = this.selected.millesimes;
      if (this.selected.couleur.length) filters.couleur = this.selected.couleur;
      await this.wineStore.fetchAllWines(this.page, this.perPage, filters);
      await this.wineStore.fetchAllWines(
        this.page,
        this.perPage,
        filters,
        this.termeDeRecherche,
      );
    },

    // pour la barre de recherche, va chercher tous les vins contenu dans cette recherche
    async rechercherVins() {
      const filters = {};
      await this.wineStore.fetchAllWines(
        0,
        this.perPage,
        filters,
        this.termeDeRecherche,
      );
    },

    goToPage(p) {
      this.page = p;
    },

    nextPage() {
      if (this.page < this.totalPages) this.page++;
    },

    prevPage() {
      if (this.page > 1) this.page--;
    },

    changePerPage(val) {
      this.perPage = val;
    },
  },

  async mounted() {
    await this.fetchWines();
  },
};
</script>
