<template>
    <button @click="deconnexion">Deconnexion</button>
</template>

<script setup>
  import api from '@/api';
  import { useRouter } from 'vue-router';

  const router = useRouter();

  async function deconnexion() {
    await api.post('/deconnexion');
    router.push('/connexion-usager');
  }
</script>