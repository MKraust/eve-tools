<template>
  <div>
    <mk-card title="Favorite items" :loading="loading" icon="fas fa-star" :actions="cardActions">
      <div v-if="!hasFavorites" class="text-center text-muted py-10">No favorite items</div>

      <div v-for="(module, idx) in sortedFavorites" :key="module.type_id" :class="{ 'mb-5': idx + 1 !== favorites.length }">
        <Module
          v-model="module.quantity"
          :item="module"
          favorite
          track-form-shown
          @toggle-favorite="toggleFavorite(module.type_id)"
        />
      </div>
    </mk-card>

    <div ref="shoppingListModal" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Modal Title</h5>
            <button type="button" class="close" data-dismiss="modal">
              <i class="ki ki-close"></i>
            </button>
          </div>
          <div class="modal-body">
            <div v-html="shoppingListHtml" data-scroll="true" data-height="500"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
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
    cardActions() {
      return [
        { icon: 'fas fa-shopping-cart', handler: this.showShoppingList },
      ]
    },
    shoppingListHtml() {
      const wtb = this.favorites.filter(i => i.quantity > 0);

      return wtb.map(i => `${i.name}* ${i.quantity}`).join('<br>');
    },
  },
  methods: {
    toggleFavorite(typeId) {
      this.$emit('toggle-favorite', typeId);
    },
    async showShoppingList() {
      $(this.$refs.shoppingListModal).modal();
    },
  },
}
</script>

<style scoped>

</style>
