<template>
  <mk-card title="Favorite rigs" :loading="loading" icon="fas fa-star">
    <div v-if="!hasFavorites" class="text-center text-muted py-10">No favorite rigs</div>

    <div v-for="(module, idx) in favorites" :key="module.id" :class="{ 'mb-5': idx + 1 !== favorites.length }">
      <Module
        :item="module"
        favorite
        @toggle-favorite="toggleFavorite(module.id)"
      />
    </div>
  </mk-card>
</template>

<script>
import Module from './Module';

export default {
  name: "FavoritesList",
  components: {
    Module,
  },
  props: {
    loading: {
      type: Boolean,
      default() {
        return false;
      },
    },
    favorites: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  computed: {
    hasFavorites() {
      return this.favorites.length > 0;
    },
    sortedFavorites() {
      return this.$lodash.sortBy(this.favorites, ['name']);
    },
  },
  methods: {
    toggleFavorite(id) {
      console.log(id);
      this.$emit('toggle-favorite', id);
    },
  },
}
</script>

<style scoped>

</style>
