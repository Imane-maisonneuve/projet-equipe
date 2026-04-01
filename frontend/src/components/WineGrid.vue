<template>
    <div class="grid">
      <WineCard
        v-for="wine in wines"
        :key="wine.id"
        :wine="wine"
        @add-to-cart="addToCart"
      />
    </div>
  </template>

  <script>
  import WineCard from "./WineCard.vue";

  export default {
    components: {
      WineCard,
    },

    props: {
      wines: Array,
    },
    data() {},
    methods: {
      addToCart(wine) {
        this.$emit("add-to-cart", wine);
      },
    },
  };
  </script>
