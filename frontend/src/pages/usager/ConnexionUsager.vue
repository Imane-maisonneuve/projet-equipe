<template>
    <div class="container">
      <div class="bloc-img">
        <img src="../../assets/img/image.png" />
        <div class="bloc-img-secondaire">Vino</div>
      </div>
        <form class="bloc-form" @submit.prevent="connexion">
            <h2>Connectez Vous</h2>
            <p class="already-txt">Pas de compte ?
              <router-link to="/creation-usager">
                S'inscrire
              </router-link>
            </p>
            <div>
                <label for="courriel">Courriel</label>
                <input v-model="courriel" id="courriel" type="email" placeholder="exemple@email.com" required>
            </div>
            <div >
                <label for="mot_de_passe">Mot de passe</label>
                <input v-model="mot_de_passe" id="mot_de_passe" type="password" placeholder="6 caractères au minimum" required>
            </div>
            <button class="signup-btn" type="submit" :disabled="loading">
                {{ loading ? 'Connexion...' : 'Se Connecter' }}
            </button>
            <p v-if="erreur" class="erreur">{{ erreur }}</p>
        </form>
    </div>
</template>

<script setup>
  import { ref } from 'vue';
  import api, { fetchCsrfToken } from '../../api';
  import { useRouter } from 'vue-router';

  const router = useRouter();
  const courriel = ref('');
  const mot_de_passe = ref('');
  const erreur = ref('');
  const loading = ref(false);

  async function connexion() {
    loading.value = true;
    erreur.value = '';

    try {
      await fetchCsrfToken();
      const response = await api.post('/', {
        courriel: courriel.value,
        mot_de_passe: mot_de_passe.value
      });
      router.push("/catalogue");

    } catch (err) {

      if (err.response) {
        erreur.value = 'Erreur de connexion';
      } else if (err.request) {
        erreur.value = 'Impossible de joindre le serveur';
      } else {
        erreur.value = 'Une erreur est survenue';
      }
    } finally {

      loading.value = false;
    }
  }
</script>