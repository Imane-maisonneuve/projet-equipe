<template>
    <div class="catalogue-carte">
      <div class="media">
        <div class="image-conteneur">
          <img :src="vin.image" class="image" :alt="vin.name"/>
          <div class="prix">{{ vin.price }}$
            <p class="litre">{{ vin.litre }}ml</p>
          </div>
        </div>

        <div class="contenu">
          <h2 class="nom">{{ vin.name }}</h2>
          <p class="meta">{{ vin.country }}</p>
          <p class="meta couleur">
            {{ vin.couleur }}
            <span
              class="couleur-point"
              :style="{ backgroundColor: chercherCouleur(vin.couleur) }"
            ></span>
          </p>
          <button class="btn fixed-btn" @click="ajoutDuVin">Ajouter au cellier</button>
        </div>
      </div>
      <div class="hover-info" :class="{ active: montrerInfo }">
        <div class="info-media">
          <p>Région : {{ vin.region }}</p>
          <p>Cépage : {{ vin.grape }}</p>
          <p>Alcool : {{ alcoolFormatter }} %</p>
          <p>Sucre : {{ vin.sugar }}</p>
          <p>Millésime : {{ vin.millesime }}</p>
        </div>
      </div>

      <button class="info-btn" @click.stop="toggleInfo">
        ↑
        <span class="info-fleche">Infos</span>
      </button>
    </div>
</template>

<script>
  export default {
    props: {
      vin: Object,
    },

    data() {
      return {
        montrerInfo: false,
      };
    },

    computed: {
      badge() {
        return this.chercherBadge(this.vin);
      },

      alcoolFormatter() {
        if (!this.vin.alcohol) return "-";
        const num = parseFloat(this.vin.alcohol);
        if (isNaN(num)) return this.vin.alcohol;
        return num.toFixed(2);
      },
    },

    methods: {
      chercherCouleur(couleur) {
        if (!couleur) return "#ccc";

        const c = couleur.toLowerCase();

        if (c.includes("rouge")) return "#8b0000";
        if (c.includes("blanc")) return "#f1c40f";
        if (c.includes("rosé") || c.includes("rose")) return "#ff6b81";
        if (c.includes("orange")) return "#e67e22";
        if (c.includes("ambré(e)") || c.includes("ambre")) return "#FFBF00";
        if (c.includes("doré(e)") || c.includes("dore")) return "#FFD700";

        return "#999";
      },
      toggleInfo() {
        this.montrerInfo = !this.montrerInfo;
      },

      chercherBadge(vin) {
        const name = vin.name.toLowerCase();

        if (name.includes("malbec")) {
          return { text: "DÉLICAT ET LÉGER", color: "#8b0000" };
        }

        if (name.includes("chardonnay")) {
          return { text: "FRUITÉ ET GÉNÉREUX", color: "#e67e22" };
        }

        if (name.includes("rosé")) {
          return { text: "FRAIS ET VIF", color: "#ff6b81" };
        }

        if (wine.country === "France") {
          return { text: "AROMATIQUE ET SOUPLE", color: "#2c3e50" };
        }

        return { text: "AROMATIQUE ET ROND", color: "#c0392b" };
      },
      ajoutDuVin() {
        this.$emit("ajout-du-vin", this.vin);
        this.$router.push("/cart");
      },
    },
  };
</script>
