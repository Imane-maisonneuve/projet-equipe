<template>
  <Navbar />
  <div class="home">
    <Logo />
    <div class="filtre">
      <div class="btn-recherche catalogue mobile-only">
        <button class="btn btn-entete-cellier" @click="toggleFilter">
          <ListFilter class="icon" /><span>Filtrer </span>
        </button>
        <button class="btn btn-entete-cellier" @click="showTri = true">
          <ArrowDownNarrowWide class="icon" /><span>Trier </span>
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
            :key="reinitialiser"
            label="Pays"
            :items="countries"
            v-model="selected.countries"
          />

          <FilterSelect
            :key="reinitialiser"
            label="Régions"
            :items="regions"
            v-model="selected.regions"
          />

          <FilterSelect
            :key="reinitialiser"
            label="Cépages"
            :items="cepages"
            v-model="selected.cepages"
          />
          <FourchetteFiltre
            :key="reinitialiser"
            v-if="prix && prix.min !== undefined"
            v-model="selected.prix"
            :minLimit="prix.min"
            :maxLimit="prix.max"
          />
          <FourchetteFiltre
            :key="reinitialiser"
            v-model="selected.format"
            :minLimit="formats.min"
            :maxLimit="formats.max"
            label="Format (ml)"
          />
          <FourchetteFiltre
            :key="reinitialiser"
            v-model="selected.degres"
            :minLimit="formatDonnees(degres.min)"
            :maxLimit="formatDonnees(degres.max)"
            label="Degré (%)"
          />

          <AnneeFiltreSelect
            :key="reinitialiser"
            :items="millesimes"
            v-model="selected.millesimes"
            label="Millésimes"
          />
        </ul>
      </aside>
    </div>

    <ModalTri
      :show="showTri"
      :tri="tri"
      @apply="appliquerTri"
      @close="showTri = false"
    />

    <div class="search-container">
      <Search class="search-icon" />
      <input
        type="text"
        v-model="termeDeRecherche"
        placeholder="Rechercher une bouteille de vin par nom..."
        @input="rechercherVins"
        class="search-input"
      />

      <div class="search-actions desktop-only">
        <button class="search-action-btn" @click="toggleFilter" title="Filtrer">
          <ListFilter class="icon-small" />
        </button>
        <div class="divider-vertical"></div>
        <button class="search-action-btn" @click="showTri = true" title="Trier">
          <ArrowDownNarrowWide class="icon-small" />
        </button>
      </div>
    </div>

    <p class="catalogue-description">
      Parcourez et ajouter vos vins à vos celliers !
    </p>

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
import { Search, ListFilter, ArrowDownNarrowWide } from "lucide-vue-next";
import FilterSection from "../components/FilterSelection.vue";
import FourchetteFiltre from "../components/Fourchette.vue";
import FilterSelect from "../components/FiltreSelect.vue";
import AnneeFiltreSelect from "../components/AnneeFiltreSelect.vue";
import ColorFilter from "../components/ColorFilter.vue";
import ModalTri from "../components/ModalTri.vue";
import Logo from "../components/Logo.vue";

export default {
  components: {
    WineGrid,
    Navbar,
    Pagination,
    Search,
    ListFilter,
    ArrowDownNarrowWide,
    FilterSection,
    ColorFilter,
    ModalTri,
    FourchetteFiltre,
    FilterSelect,
    AnneeFiltreSelect,
    Logo,
  },

  data() {
    return {
      showFilter: false,
      showTri: false,
      tri: 0,
      page: 1,
      perPage: 12,
      selected: {
        countries: [],
        regions: [],
        cepages: [],
        prix: {
          min: null,
          max: null,
        },
        format: {
          min: null,
          max: null,
        },
        degres: {
          min: null,
          max: null,
        },
        millesimes: {
          min: null,
          max: null,
        },
        couleur: [],
        reinitialiser: 0,
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
      return this.wineStore.filters.prix || { min: 0, max: 0 };
    },
    formats() {
      return this.wineStore.filters.format || { min: 0, max: 0 };
    },
    degres() {
      return this.wineStore.filters.degres || { min: 0, max: 0 };
    },

    millesimes() {
      const range = this.wineStore.filters.millesimes;

      if (!range || range.min == null || range.max == null) return [];

      const years = [];
      for (let y = range.max; y >= range.min; y--) {
        years.push(y);
      }

      return years;
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
    formatDonnees(value) {
      const num = parseFloat(value);
      return isNaN(num) ? 0 : num;
    },

    toggleFilter() {
      this.showFilter = !this.showFilter;
    },

    toggleTri() {
      this.showTri = !this.showTri;
    },
    PrixChange() {
      this.page = 1;
      this.fetchWines();
    },

    async fetchWines() {
      const filters = {};
      if (this.selected.countries.length)
        filters.countries = this.selected.countries;
      if (this.selected.regions.length) filters.regions = this.selected.regions;
      if (this.selected.cepages.length) filters.cepages = this.selected.cepages;
      if (
        this.selected.prix &&
        (this.selected.prix.min !== null || this.selected.prix.max !== null)
      ) {
        filters.prix = this.selected.prix;
      }
      if (
        this.selected.format &&
        (this.selected.format.min !== null || this.selected.format.max !== null)
      ) {
        filters.format = this.selected.format;
      }
      if (
        this.selected.degres &&
        (this.selected.degres.min !== null || this.selected.degres.max !== null)
      ) {
        filters.degres = this.selected.degres;
      }

      if (
        this.selected.millesimes &&
        (this.selected.millesimes.min !== null ||
          this.selected.millesimes.max !== null)
      ) {
        filters.millesimes = this.selected.millesimes;
      }
      if (this.selected.couleur.length) filters.couleur = this.selected.couleur;

      await this.wineStore.fetchAllWines(
        this.page,
        this.perPage,
        filters,
        this.termeDeRecherche,
        this.tri,
      );
    },

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

    appliquerTri(triChoisi) {
      this.tri = triChoisi;
      this.fetchWines();
      this.showTri = false;
    },

    reinitialiserFiltres() {
      this.selected = {
        countries: [],
        regions: [],
        cepages: [],
        prix: { min: null, max: null },
        format: { min: null, max: null },
        degres: { min: null, max: null },
        millesimes: { min: null, max: null },
        couleur: [],
      };

      this.termeDeRecherche = "";
      this.page = 1;
      this.reinitialiser++;
      this.fetchWines();
    },
  },

  async mounted() {
    await this.fetchWines();
  },
};
</script>

<style scoped>
.home {
  min-height: 100vh;
}

.mobile-only {
  display: flex;
}

.desktop-only {
  display: none;
}

.search-container {
  position: relative;
  display: flex;
  align-items: center;
  max-width: 700px;
  margin: 1.5rem auto;
}

.search-input {
  width: 100%;
  padding: 12px 15px 12px 45px;
  border-radius: 50px;
  border: 1px solid #ddd;
  font-size: 1rem;
  outline: none;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.search-icon {
  position: absolute;
  left: 15px;
  color: #888;
  width: 20px;
}

@media (min-width: 1024px) {
  .home {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
  }

  .mobile-only {
    display: none !important;
  }

  .desktop-only {
    display: flex;
    align-items: center;
    position: absolute;
    right: 15px;
    gap: 10px;
  }

  .search-input {
    padding-right: 100px;
  }
}

.search-action-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: #555;
  display: flex;
  align-items: center;
  padding: 5px;
  transition: color 0.2s;
}

.search-action-btn:hover {
  color: #8b0000;
}

.icon-small {
  width: 18px;
  height: 18px;
}

.divider-vertical {
  width: 1px;
  height: 20px;
  background-color: #ddd;
}

.catalogue-description {
  text-align: center;
  color: #666;
  margin-bottom: 2rem;
}

.espacement {
  height: 100px;
}
</style>
