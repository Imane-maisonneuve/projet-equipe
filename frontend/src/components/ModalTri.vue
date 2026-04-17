<template>
  <div v-if="show" class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-tri">
      <div class="modal-tete">
        <h3>Trier par</h3>
        <button class="modal-fermer" @click="$emit('close')">❌</button>
      </div>

      <label>
        <input type="radio" value="0" v-model="localTri" />
        {{ tous }}
      </label>

      <label>
        <input type="radio" value="1" v-model="localTri" />
        {{ nomAZ }}
      </label>

      <label>
        <input type="radio" value="2" v-model="localTri" />
        {{ nomZA }}
      </label>

      <label>
        <input type="radio" value="3" v-model="localTri" />
        {{ prixCroissant }}
      </label>

      <label>
        <input type="radio" value="4" v-model="localTri" />
        {{ prixDecroissant }}
      </label>

      <button @click="appliquerTri">Appliquer</button>
    </div>
  </div>
</template>
<script>
export default {
  name: "ModalTri",
  emits: ["apply", "close"],
  props: {
    show: Boolean,
    tri: String,
    tous: {
      type: String,
      default: "Tout désélectionner",
    },
    nomAZ: {
      type: String,
      default: "Nom A-Z",
    },
    nomZA: {
      type: String,
      default: "Nom Z-A",
    },
    prixCroissant: {
      type: String,
      default: "Prix croissant",
    },
    prixDecroissant: {
      type: String,
      default: "Prix décroissant",
    },
  },
  data() {
    return {
      localTri: this.tri,
    };
  },

  watch: {
    tri(nouvelleVal) {
      this.localTri = nouvelleVal;
    },
  },

  methods: {
    appliquerTri() {
      this.$emit("apply", this.localTri);
      this.$emit("close");
    },
  },
};
</script>
