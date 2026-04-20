<template>
  <li>
    <div class="filter-title" @click="open = !open">
      <strong>{{ label }}</strong>
      <span>{{ open ? "−" : "+" }}</span>
    </div>

    <div v-show="open" class="select-box">
      <select
        v-model="anneeSelectionnee"
        @change="ajouterSelection"
        class="select"
      >
        <option disabled value="">Choisir une année...</option>
        <option v-for="annee in anneeDisponible" :key="annee" :value="annee">
          {{ annee }}
        </option>
      </select>

      <div class="tags">
        <span v-for="annee in localValue" :key="annee" class="tag">
          {{ annee }}
          <button @click="supprimerSelection(annee)">×</button>
        </span>
      </div>
    </div>
  </li>
</template>
<script>
export default {
  props: {
    modelValue: {
      type: Array,
      default: () => [],
    },
    items: {
      type: Array,
      default: () => [],
    },
    label: {
      type: String,
      default: "Millésime",
    },
  },

  emits: ["update:modelValue", "change"],

  data() {
    return {
      open: false,
      localValue: this.modelValue ?? [],
      anneeSelectionnee: "",
    };
  },

  computed: {
    anneeDisponible() {
      return this.items.filter((y) => !this.localValue.includes(y));
    },
  },

  watch: {
    modelValue: {
      immediate: true,
      deep: true,
      handler(val) {
        if (Array.isArray(val)) {
          this.localValue = [...val];
        } else {
          this.localValue = [];
        }
      },
    },
  },

  methods: {
    ajouterSelection() {
      const annee = Number(this.anneeSelectionnee);

      if (annee && !this.localValue.includes(annee)) {
        this.localValue.push(annee);
        this.emitModification();
      }

      this.anneeSelectionnee = "";
    },

    supprimerSelection(annee) {
      this.localValue = this.localValue.filter((y) => y !== annee);
      this.emitModification();
    },

    emitModification() {
      this.$emit("update:modelValue", this.localValue);
      this.$emit("change", this.localValue);
    },
  },
};
</script>
