import { defineStore } from "pinia";

export const useWineStore = defineStore("wine", {
  state: () => ({
    wines: [],
    total: 0,
    filters: {},
    loading: false,
  }),

  actions: {
    async fetchAllWines(page = 1, perPage = 12, selectedFilters = {}) {
      this.loading = true;

      try {
        const params = new URLSearchParams();

        params.append("page", page);
        params.append("per_page", perPage);

        Object.keys(selectedFilters).forEach((key) => {
          selectedFilters[key].forEach((value) => {
            params.append(`filters[${key}][]`, value);
          });
        });

        const res = await fetch(
          `http://localhost:8000/vins?${params.toString()}`
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
