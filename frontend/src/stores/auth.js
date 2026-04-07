import { defineStore } from "pinia";
import api from "../api";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    usager: null,
    loading: false,
  }),
  actions: {
    async fetchUsager() {
      this.loading = true;
      try {
        const response = await api.get("/usager");
        this.usager = response.data;
      } catch {
        this.usager = null;
      } finally {
        this.loading = false;
      }
    },
    setUsager(usager) {
      this.usager = usager;
    },
    clearUsager() {
      this.usager = null;
    },
  },
});
