import { defineStore } from "pinia";

export const useWineStore = defineStore("wine", {
  state: () => ({
    wines: [],
    total: 0,
    filters: {},
    loading: false,
  }),

  actions: {
    async fetchAllWines(
      page = 1,
      perPage = 12,
      selectedFilters = {},
      recherche = "",
      choix = 0,
    ) {
      this.loading = true;

      try {
        const params = new URLSearchParams();

        params.append("page", page);
        params.append("per_page", perPage);
        params.append("tri", choix);

        if (recherche) {
          params.append("recherche", recherche);
        }

        Object.keys(selectedFilters).forEach((key) => {
          const value = selectedFilters[key];

          if (Array.isArray(value)) {
            value.forEach((v) => {
              params.append(`filters[${key}][]`, v);
            });
          } else if (typeof value === "object" && value !== null) {
            if (value.min !== null) {
              params.append(`filters[${key}][min]`, value.min);
            }
            if (value.max !== null) {
              params.append(`filters[${key}][max]`, value.max);
            }
          }
        });

        const res = await fetch(
          `http://localhost:8000/vins?${params.toString()}`,
        );

        const data = await res.json();

        this.wines = data.data || [];
        this.total = data.total || 0;
        this.filters = data.filters || {};
      } catch (e) {
        console.error("Erreur fetch vins:", e);
      } finally {
        this.loading = false;
      }
    },
  },
});
