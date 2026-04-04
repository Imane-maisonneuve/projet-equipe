<template>
    <div class="grid">
      <VinCatalogueCarte
        v-for="vin in vins"
        :key="vin.id"
        :vin="vin"
        @ajout-du-vin="ajoutDuVin"
      />
    </div>
</template>

<script>

  import VinCatalogueCarte from "./VinCatalogueCarte.vue";

  export default {
    components: {
      VinCatalogueCarte,
    },

    props: {
      vins: Array,
    },
    data() {},
    methods: {
      ajoutDuVin(vin) {
        this.$emit("ajout-du-vin", vin);
      },
    },
  };

</script>
