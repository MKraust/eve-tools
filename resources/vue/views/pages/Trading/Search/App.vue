<template>
  <div class="container">
    <mk-card title="Search" :loading="isLoading">
      <div class="form-group">
        <div class="input-group">
          <input v-model="searchQuery" type="text" class="form-control" placeholder="Search items..." @keyup.enter="search" />
        </div>
      </div>

      <b-table :fields="tableColumns" :items="searchResults" sort-by="name" :sort-desc="false" :responsive="true">
        <template #cell(icon)="data">
          <div class="symbol symbol-30 d-block">
            <span class="symbol-label overflow-hidden">
              <img :src="data.item.icon" class="module-icon" alt="">
            </span>
          </div>
        </template>

        <template #cell(actions)="data">
          <div class="btn btn-hover-light-warning btn-sm btn-icon" @click="toggleFavorite(data.item.type_id)">
            <i class="text-warning fa-star" :class="isFavorite(data.item) ? 'fas' : 'far'"></i>
          </div>
          <mk-market-details-button :id="data.item.type_id" />
        </template>
      </b-table>
    </mk-card>
  </div>
</template>

<script>
import COLUMNS from './columns';

export default {
  mounted() {
    this.loadFavorites();
  },
  data: () => ({
    searchResults: [],
    isLoading: false,
    searchQuery: '',
    tableColumns: COLUMNS,
    favorites: [],
  }),
  methods: {
    async loadFavorites() {
      this.isLoading = true;

      this.favorites = await this.$api.loadTradingFavorites();

      this.isLoading = false;
    },
    async toggleFavorite(typeId) {
      const isAlreadyFavorite = Boolean(this.favorites.find(f => f.type_id === typeId));

      if (isAlreadyFavorite) {
        await this.$api.deleteTradingFavorite(typeId);
        this.favorites = this.favorites.filter(f => f.type_id !== typeId);
      } else {
        console.log(typeId);
        const favorite = await this.$api.addTradingFavorite(typeId);
        this.favorites.push(favorite);
      }
    },
    async search() {
      if (this.isSearching || this.searchQuery.length < 4) {
        return;
      }

      this.isLoading = true;

      this.searchResults = await this.$api.searchTypes(this.searchQuery);

      this.isLoading = false;
    },
    isFavorite(module) {
      return Boolean(this.favorites.find(f => f.type_id === module.type_id));
    },
  },
}
</script>

<style scoped>
.module-icon {
  width: 100%;
  height: 100%;
}

.container {
  max-width: 100%;
}
</style>
