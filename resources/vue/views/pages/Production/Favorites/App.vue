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
  components: {
    Search,
    FavoritesList,
  },
  created() {
    this.loadFavorites();
  },
  data: () => ({
    favorites: [],
    isLoadingFavorites: false,
  }),
  methods: {
    async loadFavorites() {
      this.isLoadingFavorites = true;

      this.favorites = await this.$api.loadFavorites();

      this.isLoadingFavorites = false;
    },
    async toggleFavorite(id) {
      const isAlreadyFavorite = Boolean(this.favorites.find(f => f.id === id));

      if (isAlreadyFavorite) {
        await this.$api.deleteFavorite(id);
        this.favorites = this.favorites.filter(f => f.id !== id);
      } else {
        const favorite = await this.$api.addFavorite(id);
        this.favorites.push(favorite);
      }
    },
  }
}
</script>

<style lang="less">

</style>
