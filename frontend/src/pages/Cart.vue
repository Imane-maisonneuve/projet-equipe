<template>
  <Navbar />
  <div class="cart">
    <h1>Ajouter au Cellier</h1>

    <div v-for="item in cart" :key="item.id" class="cart-item">
      <img :src="item.image_url" class="cart-img" />

      <div class="cart-info">
        <h3>{{ item.nom }}</h3>
        <p>{{ item.pays }} | {{ item.format }} ml</p>

        <p class="price">Prix : {{ item.prix }} $</p>
      </div>

      <div class="cart-qty">
        Quantité :
        <input
          type="number"
          min="1"
          :value="item.quantity"
          @change="(e) => updateQuantity(item, e)"
        />
      </div>

      <div class="cart-subtotal">{{ item.prix * item.quantity }} $</div>

      <button @click="removeItem(item.id)">🗑</button>
    </div>

    <div class="continu">
      <p class="cart-total">Total : {{ total.toFixed(2) }} $</p>
      <button class="btn" style="max-width: 150px" @click="otherWine">
        Continuer...
      </button>
    </div>
  </div>
</template>
<script>
import Navbar from "../components/Navbar.vue";

export default {
  components: {
    Navbar,
  },
  data() {
    return {
      cart: [],
    };
  },

  computed: {
    total() {
      return this.cart.reduce(
        (sum, item) => sum + item.prix * item.quantity,
        0
      );
    },
  },

  methods: {
    updateQuantity(item, event) {
      item.quantity = Number(event.target.value);
      this.saveCart();
    },

    removeItem(id) {
      this.cart = this.cart.filter((item) => item.id !== id);
      this.saveCart();
    },

    saveCart() {
      localStorage.setItem("cart", JSON.stringify(this.cart));
    },
    otherWine() {
      this.$router.push("/");
    },
  },

  created() {
    const saved = localStorage.getItem("cart");
    if (saved) {
      this.cart = JSON.parse(saved);
    }
  },
};
</script>
