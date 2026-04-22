<template>
  <li>
    <div class="filter-title" @click="open = !open">
      <strong>{{ label }}</strong>
      <span>{{ open ? "−" : "+" }}</span>
    </div>

    <div v-show="open" class="select-box">
      <select
        v-model="selectedItem"
        @change="ajouterSelection"
        class="select"
        aria-label="Sélectionner un filtre"
      >
        <option disabled value="">Sélectionner...</option>
        <option v-for="item in selectionsDisponible" :key="item" :value="item">
          {{ item }}
        </option>
      </select>

      <div class="tags">
        <span v-for="item in localValue" :key="item" class="tag">
          {{ item }}
          <button @click="supprimerSelection(item)">×</button>
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
      default: "",
    },
  },

  emits: ["update:modelValue", "change"],

  data() {
    return {
      open: false,
      localValue: this.modelValue ?? [],
      selectedItem: "",
    };
  },

  computed: {
    selectionsDisponible() {
      return this.items.filter((i) => !this.localValue.includes(i));
    },
  },

  watch: {
    modelValue: {
      immediate: true,
      deep: true,
      handler(val) {
        this.localValue = val ? [...val] : [];
      },
    },
  },

  methods: {
    ajouterSelection() {
      if (this.selectedItem && !this.localValue.includes(this.selectedItem)) {
        this.localValue.push(this.selectedItem);
        this.emitModifie();
      }

      this.selectedItem = "";
    },

    supprimerSelection(item) {
      this.localValue = this.localValue.filter((i) => i !== item);
      this.emitModifie();
    },

    emitModifie() {
      this.$emit("update:modelValue", this.localValue);
      this.$emit("change", this.localValue);
    },
  },
};
</script>
