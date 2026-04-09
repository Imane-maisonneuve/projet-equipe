<template>
  <div v-if="show">
    <div class="bloc-modale">
      <div class="bloc-modale-overlay" @click="$emit('cancel')"></div>

      <div class="bloc-modale-carte">
        <h2>{{ message }}</h2>

        <div class="bloc-modale-btn">
          <button class="signup-btn" @click="$emit('confirm')">
            {{ confirmText }}
          </button>

          <button class="signup-btn btn-annuler" @click="$emit('cancel')">
            {{ cancelText }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ModalConfirmation",
  props: {
    show: Boolean,
    message: {
      type: String,
      default: "Êtes-vous sûr ?",
    },
    confirmText: {
      type: String,
      default: "Confirmer",
    },
    cancelText: {
      type: String,
      default: "Annuler",
    },
  },
};
</script>
