import { defineStore } from "pinia";
import axios from "axios";
// Store pour gérer les détails d'une bouteille dans le cellier
export const useCellierStore = defineStore("cellier", {
  state: () => ({
    bouteilleVin: [],
    cellierNom: [],
    loading: false,
    review: null,
  }),

  actions: {
    // Fonction pour récupérer les détails d'une bouteille spécifique dans le cellier
    async fetchCellier(id) {
      this.loading = true;
      this.bouteilleVin = null;

      try {
        const res = await axios.get(
          `http://localhost:8000/api/cellier-vin/${id}`,
          { withCredentials: true },
        );
        // Met à jour l'état avec les détails de la bouteille récupérée
        this.bouteilleVin = res.data;

        if (this.bouteilleVin) {
					await this.fetchReview(this.bouteilleVin.vin_id, this.bouteilleVin.usager_id);
				}        
      } catch (error) {
        console.error(error);

        if (error.response && error.response.status === 404) {
          this.bouteilleVin = null;
        }
      } finally {
        // Assure que le chargement est terminé, même en cas d'erreur
        this.loading = false;
      }
    },
    async fetchReview(vinId, usagerId) {
			try {
				const res = await axios.get(
					`http://localhost:8000/api/reviews/${vinId}/${usagerId}`,
					{ withCredentials: true }
				);
				this.review = res.data;
			} catch (error) {
				console.error(error);
				this.review = null;
			}
		}    
  },
});
