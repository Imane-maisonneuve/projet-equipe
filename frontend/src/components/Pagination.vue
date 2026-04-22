<template>
  <div class="pagination">
    <div class="nav-group">
      <button :disabled="page === 1" @click="$emit('prev')" class="btn-pagination">&lsaquo;</button>

      <span class="page-indicator"">{{ page }} / {{ totalPages }}</span>

      <button :disabled="page === totalPages" @click="$emit('next')" class="btn-pagination">
        &rsaquo;
      </button>
    </div>

    <div class="divider"></div>

    <select
      :value="perPage"
      @change="$emit('changePerPage', $event.target.value)"
      aria-label="Choisir le nombre d'items par page"
    >
      <option :value="12">12</option>
      <option :value="24">24</option>
      <option :value="48">48</option>
    </select>
  </div>
</template>

<script setup>
defineProps({
  page: Number,
  totalPages: Number,
  perPage: Number,
});

defineEmits(["next", "prev", "changePage", "changePerPage"]);
</script>
