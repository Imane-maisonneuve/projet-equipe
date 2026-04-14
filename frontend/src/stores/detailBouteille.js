import { defineStore } from "pinia";
import axios from "axios";

export const useCellierStore = defineStore("cellier", {
  state: () => ({
    bouteilleVin: [],
    cellierNom: [],
    loading: false,
  }),

  actions: {
    async fetchCellier(id) {
      this.loading = true;
      this.bouteilleVin = null;

      try {
        const res = await axios.get(
          `http://localhost:8000/api/cellier-vin/${id}`,
          { withCredentials: true },
        );

        this.bouteilleVin = res.data;
      } catch (error) {
        console.error(error);

        if (error.response && error.response.status === 404) {
          this.bouteilleVin = null;
        }
      } finally {
        this.loading = false;
      }
    },
  },
});
