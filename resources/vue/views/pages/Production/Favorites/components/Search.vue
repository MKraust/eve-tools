<template>
  <mk-card title="Rigs search" :loading="isSearching" icon="fas fa-search">
    <div class="form-group" :class="{ ['mb-' + (hasSearchResults ? '5' : '0')]: true }">
      <div class="input-group">
        <input
          v-model="query"
          type="text"
          class="form-control"
          placeholder="Find rigs..."
          @keyup.enter="$event.target.blur()"
          @blur="search"
        />
        <div class="input-group-append">
          <button class="btn btn-primary btn-icon" @click="search">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </div>

    <div v-if="!hasSearchResults" class="text-center text-muted py-10">No search results</div>

    <div v-for="(module, idx) in sortedSearchResults" :key="module.id" :class="{ 'mb-5': idx + 1 !== searchResults.length }">
      <Module
        :item="module"
        :favorite="isFavorite(module)"
        @toggle-favorite="toggleFavorite(module.id)"
      />
    </div>
  </mk-card>
</template>

<script>
import Module from './Module';

export default {
  name: "Search",
  components: {
    Module,
  },
  props: {
    favorites: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  data: () => ({
    isSearching: false,
    searchResults: [],
    query: '',
  }),
  computed: {
    hasSearchResults() {
      return this.searchResults.length > 0;
    },
    sortedSearchResults() {
      return this.$lodash.sortBy(this.searchResults, i => i.name);
    },
  },
  methods: {
    async search() {
      if (this.isSearching || this.query.length < 4) {
        return;
      }

      this.isSearching = true;

      this.searchResults = await this.$api.searchModules(this.query);

      this.isSearching = false;
    },
    isFavorite(module) {
      return Boolean(this.favorites.find(f => f.id === module.id));
    },
    toggleFavorite(id) {
      this.$emit('toggle-favorite', id);
    },
  },
}
</script>

<style scoped>

</style>
