<template>
  <mk-card title="Favorite rigs" :loading="loading" icon="fas fa-star">
    <div v-if="!hasFavorites" class="text-center text-muted py-10">No favorite rigs</div>

    <div v-for="(module, idx) in sortedFavorites" :key="module.type_id" :class="{ 'mb-8': idx + 1 !== favorites.length }">
      <Module
        :item="module"
        favorite
        track-button-shown
        @toggle-favorite="toggleFavorite(module.type_id)"
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
      return this.$lodash.sortBy(this.favorites, i => i.name);
    },
  },
  methods: {
    toggleFavorite(typeId) {
      this.$emit('toggle-favorite', typeId);
    },
  },
}
</script>

<style scoped>

</style>
