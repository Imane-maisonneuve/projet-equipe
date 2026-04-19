import { defineStore } from "pinia";
import axios from "axios";

export const useListeStore = defineStore("listeAchat", {
  state: () => ({
    bouteilleVin: null,
    loading: false,
  }),

  actions: {
    async fetchVin(id) {
      this.loading = true;
      this.bouteilleVin = null;

      try {
        const res = await axios.get(
          `http://localhost:8000/api/liste-achats-vin/${id}`,
          { withCredentials: true }
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
