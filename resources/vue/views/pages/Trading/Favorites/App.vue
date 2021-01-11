<template>
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <Search :favorites="favorites" @toggle-favorite="toggleFavorite" />
      </div>
      <div class="col-md-6">
        <FavoritesList :favorites="favorites" :loading="isLoadingFavorites" @toggle-favorite="toggleFavorite" />
      </div>
    </div>
  </div>
</template>

<script>
import Search from './components/Search';
import FavoritesList from './components/FavoritesList';

export default {
  name: 'Favorites',
  components: {
    Search,
    FavoritesList,
  },
  mounted() {
    this.loadFavorites();
  },
  data: () => ({
    favorites: [],
    isLoadingFavorites: false,
  }),
  methods: {
    async loadFavorites() {
      this.isLoadingFavorites = true;

      this.favorites = await this.$api.loadTradingFavorites();

      this.isLoadingFavorites = false;
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
  }
}
</script>

<style lang="less">

</style>
