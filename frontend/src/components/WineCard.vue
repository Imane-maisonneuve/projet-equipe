<template>
    <div class="card">
      <div class="media">
        <!-- <div class="badge media" :style="{ background: badge.color }"> -->
        <!-- {{ badge.text }} -->
        <!-- </div> -->

        <div class="image-container">
          <img :src="wine.image" class="image" />
          <div class="price">{{ wine.price }} $
            <button class="btn fixed-btn" @click="addToCart">Ajouter au cellier</button>
          </div>
        </div>

        <div class="content">
          <h2 class="name">{{ wine.name }}</h2>
          <!-- <h2 class="code_SAQ">Code SAQ : {{ wine.sku }}</h2> -->
          <p class="meta">{{ wine.country }}</p>
          <p class="meta couleur">
            {{ wine.couleur }}
            <span
              class="color-dot"
              :style="{ backgroundColor: getColor(wine.couleur) }"
            ></span>
          </p>
        </div>
      </div>
      <div class="hover-info" :class="{ active: showInfo }">
        <button class="close-btn" @click="toggleInfo">
          <!-- <i class="fa-regular fa-rectangle-xmark"></i> -->
        </button>
        <div class="info-media">
          <p>Région : {{ wine.region }}</p>
          <p>Cépage : {{ wine.grape }}</p>
          <p>Alcool : {{ formattedAlcohol }} %</p>
          <p>Sucre : {{ wine.sugar }}</p>
          <!-- <p>Format : {{ wine.litre }} ml</p> -->
          <!-- <p>Producteur : {{ wine.producer }}</p> -->
          <p>Millésime : {{ wine.millesime }}</p>
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
      wine: Object,
    },

    data() {
      return {
        showInfo: false,
      };
    },

    computed: {
      badge() {
        return this.getBadge(this.wine);
      },

      formattedAlcohol() {
        if (!this.wine.alcohol) return "-";
        const num = parseFloat(this.wine.alcohol);
        if (isNaN(num)) return this.wine.alcohol;
        return num.toFixed(2);
      },
    },

    methods: {
      getColor(couleur) {
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
        this.showInfo = !this.showInfo;
      },

      getBadge(wine) {
        const name = wine.name.toLowerCase();

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
      addToCart() {
        this.$emit("add-to-cart", this.wine);
        this.$router.push("/cart");
      },
    },
  };
  </script>
