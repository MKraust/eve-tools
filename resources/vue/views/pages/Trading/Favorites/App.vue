<template>
  <div class="container">
    <mk-card title="Favorites" :actions="cardActions">
      <div class="form-group">
        <div class="input-group">
          <input v-model="filterQuery" type="text" class="form-control" placeholder="Filter items..." />
        </div>
      </div>

      <b-table
        :busy="isLoadingFavorites"
        :fields="tableColumns"
        :items="filteredFavorites"
        :sort-desc="true"
        :responsive="true"
        sort-by="potential_daily_profit"
      >
        <template #table-busy>
          <div class="text-center text-primary my-2">
            <b-spinner class="align-middle mr-2"></b-spinner>
            <strong>Loading...</strong>
          </div>
        </template>

        <template #cell(icon)="data">
          <div class="symbol symbol-30 d-block">
            <span class="symbol-label overflow-hidden">
              <img :src="data.item.icon" class="module-icon" alt="">
            </span>
          </div>
        </template>

        <template #cell(quantity)="data">
          <div class="form-group mb-0">
            <div class="input-group input-group-sm mr-1">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <i class="fas fas fa-shopping-cart"></i>
                </span>
              </div>
              <input
                v-model="data.item.quantity"
                type="number"
                class="form-control form-control-sm"
                placeholder="0"
              >
            </div>
          </div>
        </template>

        <template #cell(actions)="data">
          <div class="btn btn-hover-light-danger btn-sm btn-icon" @click="toggleIgnoreFastShopping(data.item.type_id)">
            <i class="fas fa-bolt" :class="{ 'text-danger': isFastShoppingIgnoredForItem(data.item.type_id) }"></i>
          </div>
          <div class="btn btn-hover-light-warning btn-sm btn-icon" @click="toggleFavorite(data.item.type_id)">
            <i class="text-warning fas fa-star"></i>
          </div>
          <mk-money-flow-button :id="data.item.type_id" :name="data.item.name" />
          <mk-market-details-button :id="data.item.type_id" />
        </template>
      </b-table>
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
import COLUMNS from './columns';

const KEY_FAST_SHOPPING_IGNORED_TYPES = 'fast_shopping_ignored_types';

export default {
  mounted() {
    this.loadFavorites();
  },
  data: () => ({
    favorites: [],
    isLoadingFavorites: false,
    filterQuery: '',
    tableColumns: COLUMNS,
    fastShoppingIgnoredTypes: [],
  }),
  computed: {
    filteredFavorites() {
      if (this.filterQuery === '') {
        return this.favorites;
      }

      return this.favorites.filter(i => i.name.toLowerCase().indexOf(this.filterQuery.toLocaleLowerCase()) !== -1);
    },
    cardActions() {
      return [
        { icon: 'fas fa-bolt', handler: this.fastFillShoppingList },
        { icon: 'fas fa-shopping-cart', handler: this.showShoppingList },
      ];
    },
    shoppingListHtml() {
      const wtb = this.favorites.filter(i => i.quantity > 0);

      return wtb.map(i => `${i.name}* ${i.quantity}`).join('<br>');
    },
  },
  methods: {
    async loadFavorites() {
      this.isLoadingFavorites = true;

      this.favorites = await this.$api.loadTradingFavorites();
      this.loadFastShoppingIgnoredTypes();

      this.isLoadingFavorites = false;
    },
    loadFastShoppingIgnoredTypes() {
      const fastShoppingIgnoredTypes = localStorage.getItem(KEY_FAST_SHOPPING_IGNORED_TYPES);
      this.fastShoppingIgnoredTypes = fastShoppingIgnoredTypes ? JSON.parse(fastShoppingIgnoredTypes) : [];
    },
    async toggleFavorite(typeId) {
      console.log(typeId);
      await this.$api.deleteTradingFavorite(typeId);
      this.favorites = this.favorites.filter(f => f.type_id !== typeId);
    },
    async showShoppingList() {
      $(this.$refs.shoppingListModal).modal();
    },
    fastFillShoppingList() {
      this.favorites.forEach(f => {
        if (f.prices.potential_daily_profit >= 3_000_000 && !this.isFastShoppingIgnoredForItem(f.type_id)) {
          f.quantity = f.prices.weekly_volume - f.in_stock - f.in_delivery;
        }
      });
    },
    toggleIgnoreFastShopping(typeId) {
      if (this.fastShoppingIgnoredTypes.some(i => i === typeId)) {
        this.fastShoppingIgnoredTypes = this.fastShoppingIgnoredTypes.filter(t => t !== typeId);
      } else {
        this.fastShoppingIgnoredTypes.push(typeId);
      }

      localStorage.setItem(KEY_FAST_SHOPPING_IGNORED_TYPES, JSON.stringify(this.fastShoppingIgnoredTypes));
    },
    isFastShoppingIgnoredForItem(typeId) {
      return this.fastShoppingIgnoredTypes.some(i => i === typeId);
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
