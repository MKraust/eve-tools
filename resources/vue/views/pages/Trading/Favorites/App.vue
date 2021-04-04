<template>
  <div class="container">
    <mk-card title="Favorites" :actions="cardActions">
      <div class="d-flex align-items-center mb-7">
        <div class="flex-grow-1">
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
                  <i class="fas fa-shopping-cart"></i>
                </span>
              </div>
              <input
                v-model="data.item.quantity"
                type="number"
                class="form-control form-control-sm"
                placeholder="0"
                min="0"
              >
            </div>
          </div>
        </template>

        <template #cell(fast_shopping_limit)="data">
          <div class="form-group mb-0">
            <div class="input-group input-group-sm mr-1">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <i class="fas fa-bolt"></i>
                </span>
              </div>
              <input
                v-model="fastShoppingLimits[data.item.type_id]"
                type="number"
                class="form-control form-control-sm"
                placeholder="0"
                min="0"
              >
            </div>
          </div>
        </template>

        <template #cell(actions)="data">
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
            <h5 class="modal-title">Shopping list</h5>
            <button type="button" class="close" data-dismiss="modal">
              <i class="ki ki-close"></i>
            </button>
          </div>
          <div class="modal-body">
            <b-form-textarea
              v-model="shoppingList"
              id="shopping-list-input"
              rows="35"
              no-resize
            />
          </div>
          <div class="modal-footer">
            <b-button variant="primary" @click="copyShoppingListToClipboard">Copy</b-button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import COLUMNS from './columns';

const KEY_FAST_SHOPPING_LIMITS = 'fast_shopping_limits';

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
    onlyFastShopIgnored: false,
    fastShoppingLimits: {},
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
    shoppingList() {
      return this.favorites
        .filter(i => i.quantity > 0)
        .map(i => `${i.name}* ${i.quantity}`)
        .join('\n');
    }
  },
  watch: {
    fastShoppingLimits: {
      deep: true,
      handler() {
        this.saveFastShoppingLimits();
      },
    }
  },
  methods: {
    async loadFavorites() {
      this.isLoadingFavorites = true;

      this.favorites = await this.$api.loadTradingFavorites();
      this.loadFastShoppingLimits();

      this.isLoadingFavorites = false;
    },
    loadFastShoppingLimits() {
      const fastShoppingLimits = localStorage.getItem(KEY_FAST_SHOPPING_LIMITS);
      this.fastShoppingLimits = fastShoppingLimits ? JSON.parse(fastShoppingLimits) : {};
    },
    saveFastShoppingLimits() {
      localStorage.setItem(KEY_FAST_SHOPPING_LIMITS, JSON.stringify(this.fastShoppingLimits));
    },
    async toggleFavorite(typeId) {
      await this.$api.deleteTradingFavorite(typeId);
      this.favorites = this.favorites.filter(f => f.type_id !== typeId);
    },
    async showShoppingList() {
      $(this.$refs.shoppingListModal).modal();
    },
    copyShoppingListToClipboard() {
      $('#shopping-list-input').select();
      document.execCommand('copy');
    },
    fastFillShoppingList() {
      this.favorites.forEach(f => {
        if (f.prices.potential_daily_profit >= 3_000_000 || !f.prices.sell) {
          const fastShoppingLimit = this.fastShoppingLimits[f.type_id] || 0;
          f.quantity = Math.max(0, Number(fastShoppingLimit) - f.in_stock - f.in_delivery);
        }
      });
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
