<template>
  <b-container>
    <mk-card title="Favorites" :actions="cardActions">
      <b-row class="mb-7">
        <b-col cols="12" md="6">
          <b-input v-model="filterQuery" placeholder="Filter items..." />
        </b-col>
        <b-col cols="12" md="6">
          <b-input 
            v-model.number="minProfitForFastShopping" 
            type="number" 
            min="0"
            placeholder="Fast shopping min profit" 
          />
        </b-col>
      </b-row>

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

        <template #cell(actions)="{ item }">
          <div class="btn btn-hover-light-warning btn-sm btn-icon" @click="toggleFavorite(item.type_id)">
            <i class="text-warning fas fa-star"></i>
          </div>
          <mk-money-flow-button :id="item.type_id" :name="item.name" />
          <mk-manual-transaction-button :id="item.type_id" :title="item.name" />
          <mk-market-details-button :id="item.type_id" />
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
  data() {
    return {
      favorites: [],
      isLoadingFavorites: false,
      filterQuery: '',
      tableColumns: COLUMNS,
      fastShoppingIgnoredTypes: [],
      onlyFastShopIgnored: false,
      fastShoppingLimits: {},
      throttledSaveFastShoppingLimitsLimits: this.$lodash.throttle(this.saveFastShoppingLimits, 2000),
      minProfitForFastShopping: 8_000_000,
    }
  },
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
        this.throttledSaveFastShoppingLimitsLimits();
      },
    },
  },
  methods: {
    async loadFavorites() {
      this.isLoadingFavorites = true;

      this.favorites = await this.$api.loadTradingFavorites();
      this.fastShoppingLimits = (await this.$api.loadSettings(KEY_FAST_SHOPPING_LIMITS)) || {}

      this.isLoadingFavorites = false;
    },
    async saveFastShoppingLimits() {
      await this.$api.saveSettings(KEY_FAST_SHOPPING_LIMITS, this.fastShoppingLimits);
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
        if (f.prices.potential_daily_profit >= this.minProfitForFastShopping || !f.prices.sell) {
          const fastShoppingLimit = Number(this.fastShoppingLimits[f.type_id] || 0);
          const needToBuy = fastShoppingLimit - f.in_stock - f.in_delivery;
          const needToBuyPercent = fastShoppingLimit > 0 ? needToBuy / fastShoppingLimit * 100 : 0;

          f.quantity = needToBuyPercent > 30 ? Math.max(0, needToBuy) : 0;
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
