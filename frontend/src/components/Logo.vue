<template>
  <!-- logo est composé d'une image et d'un titre, il est centré horizontalement et verticalement sur les écrans larges -->
  <div class="logo">
    <img
      src="../assets/img/logo3.svg"
      alt="Logo de Vino, application de gestion de cave à vin"
    />
    <h1 class="logo-titre">Catalogue des vins</h1>
  </div>
</template>

<script>
export default {
  name: "Logo",
};
</script>

<style scoped>
/* Css pour centrer le logo et le titre sur les écrans larges */
@media (min-width: 1024px) {
  .logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
  }
}
</style>
