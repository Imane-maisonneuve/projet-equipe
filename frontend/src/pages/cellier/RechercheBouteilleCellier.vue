<template>
  <Navbar />

  <div class="banniere">
    <h1 class="banniere-titre">Recherche bouteille dans les celliers</h1>
  </div>
  <!-- recherche des bouteilles dans les celliers de l'usager -->
  <div class="search-container">
    <Search class="search-icon" />
    <input
      v-model="search"
      type="text"
      placeholder="Rechercher une bouteille..."
      class="search-input"
      aria-label="Rechercher une bouteille"
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
  <!-- filtrer les bouteilles qui existent dans les celliers de l'usager -->
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
        :items="filters.countries"
        v-model="selected.countries"
      />

      <FilterSelect
        :key="reinitialiser"
        label="Régions"
        :items="filters.regions"
        v-model="selected.regions"
      />

      <FilterSelect
        :key="reinitialiser"
        label="Cépages"
        :items="filters.cepages"
        v-model="selected.cepages"
      />

      <FourchetteFiltre
        v-if="filters.prix"
        :key="reinitialiser"
        v-model="selected.prix"
        :minLimit="safeNumber(filters.prix.min)"
        :maxLimit="safeNumber(filters.prix.max)"
      />

      <FourchetteFiltre
        :key="reinitialiser"
        v-model="selected.format"
        :minLimit="safeNumber(filters.format.min)"
        :maxLimit="safeNumber(filters.format.max)"
        label="Format (ml)"
      />

      <FourchetteFiltre
        :key="reinitialiser"
        v-model="selected.degres"
        :minLimit="safeNumber(filters.degres.min)"
        :maxLimit="safeNumber(filters.degres.max)"
        label="Degré (%)"
      />
      <AnneeFiltreSelect
        :key="reinitialiser"
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
  <!-- afficher la liste des bouteilles trouvées, et les actions associées -->
  <div class="liste-bouteilles">
    <div v-for="bouteille in bouteilles" :key="bouteille.id">
      <div v-if="bouteille.messageAjout" class="bloc-modale-succes">
        {{ bouteille.messageAjout }}
      </div>

      <div v-if="bouteille.messageErreur" class="erreur">
        {{ bouteille.messageErreur }}
      </div>

      <div class="carte-bouteille">
        <img
          :src="bouteille.vin.image_url"
          class="image-vin"
          :alt="'Le nom du vin est : ' + bouteille.vin.nom"
        />

        <div class="info">
          <h3>{{ bouteille.vin.nom }}</h3>
          <p>Cellier : {{ bouteille.cellier.nom }}</p>
          <p>Prix : {{ bouteille.vin.prix }}$</p>
          <p>Quantité : {{ bouteille.quantite }}</p>

          <button
            @click="modifierQuantiteVin(bouteille.quantite - 1, bouteille.id)"
            class="btn-qte"
            :disabled="bouteille.quantite <= 1"
            aria-label="Réduire la quantité"
          >
            <CircleMinus />
          </button>

          <button
            @click="modifierQuantiteVin(bouteille.quantite + 1, bouteille.id)"
            class="btn-qte"
            aria-label="Augmenter la quantité"
          >
            <CirclePlus />
          </button>
        </div>
        <!-- boutons d'action pour chaque bouteille : Afficher les détails, ajouter à la liste de courses, supprimer -->
        <div class="bouton-cellier">
          <button
            @click="voirDetail(bouteille.id)"
            class="btn btn-cellier"
            aria-label="Voir les détails"
          >
            <Eye />
          </button>

          <button
            class="btn btn-cellier"
            @click="ajouterListeAchats(bouteille.vin.id)"
            aria-label="Ajouter à la liste d'achats"
          >
            <ShoppingBasket class="icons" />
          </button>

          <button
            @click="ouvrirModale(bouteille.id)"
            class="btn btn-cellier"
            aria-label="Supprimer"
          >
            <Trash />
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- modale de confirmation de suppression -->
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
import { useAuthStore } from "../../stores/auth";

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
      reinitialiser: 0,
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
      reinitialiser: 0,
    };
  },

  watch: {
    // relancer la recherche à chaque changement de la barre de recherche ou des filtres
    search() {
      this.fetchBouteilles();
    },
    // relancer la recherche à chaque changement de la barre de recherche ou des filtres
    selected: {
      handler() {
        this.fetchBouteilles();
      },
      deep: true,
    },
  },

  methods: {
    // fonction utilitaire pour extraire les valeurs min et max d'un tableau de valeurs
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
    // fonction utilitaire pour convertir une valeur en nombre, ou retourner 0 si ce n'est pas un nombre
    safeNumber(val) {
      const n = Number(val);
      return isNaN(n) ? 0 : n;
    },
    // fonction pour ajouter une bouteille à la liste de courses
    toggleFilter() {
      this.showFilter = !this.showFilter;
    },
    // fonction pour appliquer le tri sélectionné dans la modale de tri
    appliquerTri(val) {
      this.tri = val;
      this.fetchBouteilles();
      this.showTri = false;
    },
    // fonction pour ajouter une bouteille à la liste de courses
    async fetchBouteilles() {
      try {
        const filters = {};
        // construire l'objet de filtres à partir des sélections de l'utilisateur
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
        // mettre à jour la liste des bouteilles avec les résultats de la recherche
        this.bouteilles = res.data.data || [];
        // mettre à jour les options de filtres disponibles en fonction des résultats de la recherche
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
    // fonction pour ajouter une bouteille à la liste de courses
    async modifierQuantiteVin(qte, id) {
      if (qte < 1) return;

      await fetchCsrfToken();
      await api.put(`/modifier-quantite/${id}`, { quantite: qte });

      this.fetchBouteilles();
    },
    // fonction pour ajouter une bouteille à la liste de courses
    ouvrirModale(id) {
      this.idASupprimer = id;
      this.afficherModale = true;
    },
    // fonction pour ajouter une bouteille à la liste de courses
    async confirmerSuppression() {
      await api.delete(`/cellier-vins/${this.idASupprimer}`);

      this.bouteilles = this.bouteilles.filter(
        (b) => b.id !== this.idASupprimer,
      );

      this.afficherModale = false;
    },
    // fonction pour ajouter une bouteille à la liste de courses
    voirDetail(id) {
      this.$router.push(`/cellier-vin/${id}`);
    },

    //ajoute une bouteille a la liste d'achar
    async ajouterListeAchats(idVin) {
      //Veut filtres les bouteilles, pour juste recuperer les bouteilles avec id concernees
      const bouteillesConcernees = this.bouteilles.filter(
        (b) => b.vin.id === idVin,
      );

      // Réinitialiser les messages
      bouteillesConcernees.forEach((bouteille) => {
        bouteille.messageAjout = null;
        bouteille.messageErreur = null;
      });

      try {
        // CSRF token
        await fetchCsrfToken();

        // Récupérer l'utilisateur connecté
        const authStore = useAuthStore();
        await authStore.fetchUsager();
        const usagerId = authStore.usager.id;

        // Récupérer l'id du vin
        const vinId = idVin;

        //appel api pour ajouter a la BD
        const response = await api.post("/ajouter-bouteille-liste", {
          usager_id: usagerId,
          vin_id: vinId,
        });

        if (response.status === 200 || response.status === 201) {
          // afficher un message de succès
          bouteillesConcernees.forEach((bouteille) => {
            bouteille.messageAjout =
              response.data.message || "Bouteille ajoutée à la liste d'achat !";
            setTimeout(() => {
              bouteille.messageAjout = null;
            }, 2000);
          });
        }
      } catch (erreur) {
        // afficher message d'erreur
        bouteillesConcernees.forEach((bouteille) => {
          bouteille.messageErreur =
            "Cette bouteille est déjà dans votre liste d'achat";
          setTimeout(() => {
            bouteille.messageErreur = null;
          }, 3000);
        });
      }
    },
    // fonction pour ajouter une bouteille à la liste de courses
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
