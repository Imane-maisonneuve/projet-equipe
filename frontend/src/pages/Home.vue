<template>
  <Navbar />

  <div class="home">
    <Logo />
    <!-- Affiche une notification -->
    <div v-if="notifStore.message" :class="['notif', notifStore.type]">
      {{ notifStore.message }}
    </div>

    <!-- Filtres -->
    <div class="filtre">
      <div class="btn-recherche catalogue mobile-only">
        <button class="btn btn-entete-cellier" @click="toggleFilter">
          <ListFilter class="icon" /><span>Filtrer </span>
        </button>
        <button class="btn btn-entete-cellier" @click="showTri = true">
          <ArrowDownNarrowWide class="icon" /><span>Trier </span>
        </button>
      </div>

      <!-- afficher les filtres si showFilter est true -->
      <div
        class="filtre-ouvrir"
        :class="{ active: showFilter }"
        @click="toggleFilter"
      ></div>

      <aside class="filter-panel" :class="{ active: showFilter }">
        <div class="filter-header">
          <h2>Filtres</h2>
        </div>
        <!-- Bouton pour réinitialiser les filtres -->
        <button class="reset-filters" @click="reinitialiserFiltres">
          Réinitialiser les filtres
        </button>
        <!-- Liste des filtres -->
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
          <!-- <FourchetteFiltre
            :key="reinitialiser"
            v-if="prix && prix.min !== undefined"
            v-model="selected.prix"
            :minLimit="prix.min"
            :maxLimit="prix.max"
          /> -->
          <FilterSelect
            :key="reinitialiser"
            label="Format (ml)"
            :items="formats"
            v-model="selected.format"
          />

          <FilterSelect
            :key="reinitialiser"
            label="Degré (%)"
            :items="degres"
            v-model="selected.degres"
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
    <!-- modal de tri -->
    <ModalTri
      :show="showTri"
      :tri="tri"
      @apply="appliquerTri"
      @close="showTri = false"
    />
    <!-- barre de recherche des vins par nom -->
    <div class="search-container">
      <Search class="search-icon" />
      <input
        type="text"
        v-model="termeDeRecherche"
        placeholder="Rechercher une bouteille de vin par nom..."
        aria-label="Rechercher un vin"
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
    <!-- Pagination du catalogue -->
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
import { useNotifStore } from "../stores/notification";

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
      reinitialiser: 0,
      showFilter: false,
      showTri: false,
      tri: 0,
      page: 1,
      perPage: 12,

      selected: {
        countries: [],
        regions: [],
        cepages: [],
        prix: { min: null, max: null },

        format: [],
        degres: [],

        millesimes: [],
        couleur: [],
      },

      wineStore: useWineStore(),
      termeDeRecherche: "",
    };
  },

  computed: {
    // Récupère les vins depuis le store
    vins() {
      return this.wineStore.wines;
    },
    // Indique si les vins sont en cours de chargement
    loading() {
      return this.wineStore.loading;
    },
    // Calcule le nombre total de pages en fonction du nombre total de vins et du nombre de vins par page
    totalPages() {
      return Math.ceil(this.wineStore.total / this.perPage);
    },
    // Récupère les options de filtre depuis le store
    countries() {
      return this.wineStore.filters.countries || [];
    },
    // Récupère les régions disponibles pour le filtre
    regions() {
      return this.wineStore.filters.regions || [];
    },
    // Récupère les cépages disponibles pour le filtre
    cepages() {
      return this.wineStore.filters.cepages || [];
    },
    // Récupère les prix minimum et maximum pour le filtre
    prix() {
      return this.wineStore.filters.prix || { min: 0, max: 0 };
    },
    // Récupère les formats minimum et maximum pour le filtre
    formats() {
      return Array.isArray(this.wineStore.filters.format)
        ? this.wineStore.filters.format
        : [];
    },

    degres() {
      if (!Array.isArray(this.wineStore.filters.degres)) return [];

      return [...this.wineStore.filters.degres]
        .map((d) => Number(d))
        .filter((d) => !isNaN(d))
        .sort((a, b) => a - b);
    },

    // Calcule la liste des millésimes disponibles pour le filtre en fonction des valeurs minimum et maximum
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
  // Surveille les changements dans les filtres sélectionnés, la page actuelle et le nombre de vins par page pour déclencher une nouvelle recherche de vins
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
    // Affiche ou masque le panneau de filtres
    toggleFilter() {
      this.showFilter = !this.showFilter;
    },
    // Affiche ou masque le modal de tri
    toggleTri() {
      this.showTri = !this.showTri;
    },
    PrixChange() {
      this.page = 1;
      this.fetchWines();
    },
    // Récupère les vins en fonction des filtres sélectionnés, de la page actuelle, du nombre de vins par page, du terme de recherche et du tri sélectionné
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

      if (this.selected.format.length) {
        filters.format = this.selected.format;
      }

      if (this.selected.degres.length) {
        filters.degres = this.selected.degres;
      }

      if (this.selected.millesimes.length) {
        filters.millesimes = this.selected.millesimes;
      }

      if (this.selected.couleur.length) {
        filters.couleur = this.selected.couleur;
      }

      await this.wineStore.fetchAllWines(
        this.page,
        this.perPage,
        filters,
        this.termeDeRecherche,
        this.tri,
      );
    },
    // Récupère les vins en fonction du terme de recherche saisi
    async rechercherVins() {
      const filters = {};
      await this.wineStore.fetchAllWines(
        0,
        this.perPage,
        filters,
        this.termeDeRecherche,
      );
    },
    // Change la page actuelle
    goToPage(p) {
      this.page = p;
    },
    // Passe à la page suivante si elle existe
    nextPage() {
      if (this.page < this.totalPages) this.page++;
    },
    // Passe à la page précédente si elle existe
    prevPage() {
      if (this.page > 1) this.page--;
    },
    // Change le nombre de vins affichés par page
    changePerPage(val) {
      this.perPage = val;
    },
    // Applique le tri sélectionné et rafraîchit la liste des vins
    appliquerTri(triChoisi) {
      this.tri = triChoisi;
      this.fetchWines();
      this.showTri = false;
    },
    // Réinitialise tous les filtres, le terme de recherche, la page actuelle et rafraîchit la liste des vins
    reinitialiserFiltres() {
      this.selected = {
        countries: [],
        regions: [],
        cepages: [],
        prix: { min: null, max: null },

        format: [],
        degres: [],
        millesimes: [],
        couleur: [],
      };

      this.termeDeRecherche = "";
      this.page = 1;
      this.reinitialiser++;

      this.fetchWines();
    },
  },
  // Lors du montage du composant, récupère la liste des vins en appelant la méthode fetchWines
  async mounted() {
    await this.fetchWines();
  },
  setup() {
    const notifStore = useNotifStore();
    return { notifStore };
  },
};
</script>
