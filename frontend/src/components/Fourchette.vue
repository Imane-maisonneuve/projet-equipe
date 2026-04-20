<template>
  <li>
    <div class="filter-title" @click="open = !open">
      <strong>{{ label }}</strong>
      <span>{{ open ? "−" : "+" }}</span>
    </div>

    <div v-show="open" class="price-range">
      <div class="slider-container">
        <Slider
          v-model="range"
          :min="minLimit"
          :max="maxLimit"
          :tooltips="true"
          :margin="1"
        />

        <div class="values">
          <span>{{ range[0] }}</span>
          <span>{{ range[1] }}</span>
        </div>
        <button @click="apply" class="app-filtre">Appliquer le filtre</button>
      </div>
    </div>
  </li>
</template>

<script>
import Slider from "@vueform/slider";
import "@vueform/slider/themes/default.css";

export default {
  components: { Slider },

  props: {
    modelValue: Object,
    minLimit: Number,
    maxLimit: Number,
    label: {
      type: String,
      default: "Prix",
    },
  },

  emits: ["update:modelValue"],

  data() {
    return {
      open: false,
      range: null,
    };
  },

  watch: {
    modelValue: {
      immediate: true,
      handler(val) {
        if (val && val.min != null && val.max != null) {
          this.range = [Number(val.min), Number(val.max)];
        } else if (!this.range) {
          this.range = [Number(this.minLimit), Number(this.maxLimit)];
        }
      },
    },

    minLimit() {
      this.initRange();
    },

    maxLimit() {
      this.initRange();
    },
  },

  methods: {
    initRange() {
      if (!this.modelValue?.min && !this.modelValue?.max) {
        this.range = [Number(this.minLimit), Number(this.maxLimit)];
      }
    },

    apply() {
      this.$emit("update:modelValue", {
        min: this.range[0],
        max: this.range[1],
      });
    },
  },
};
</script>
