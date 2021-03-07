<template>
  <div class="container">
    <mk-card title="Profitable items" :actions="cardActions">
      <div class="d-flex align-items-center mb-7">
        <div class="flex-grow-1">
          <input v-model="filterQuery" type="text" class="form-control" placeholder="Filter items..." />
        </div>
        <div class="ml-5">
          <mk-switch v-model="withFavorites" label="Избранные" type="brand" size="sm" />
        </div>
      </div>

      <b-table
        :busy="isLoading"
        :fields="tableColumns"
        :items="filteredItems"
        :responsive="true"
        :per-page="perPage"
        :current-page="currentPage"
        :sort-desc="true"
        sort-by="potential_daily_profit"
        hover
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
          <div class="d-flex">
            <div class="btn btn-hover-light-warning btn-sm btn-icon" @click="toggleFavorite(data.item.type_id)">
              <i class="text-warning fa-star" :class="isFavorite(data.item) ? 'fas' : 'far'"></i>
            </div>
            <mk-money-flow-button :id="data.item.type_id" :name="data.item.name" />
            <mk-market-details-button :id="data.item.type_id" />
          </div>
        </template>
      </b-table>

      <b-pagination
        v-model="currentPage"
        :total-rows="items.length"
        :per-page="perPage"
        aria-controls="my-table"
      ></b-pagination>
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
            <div v-html="shoppingListHtml" data-scroll="true" data-height="500"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import COLUMNS from './columns';

export default {
  mounted() {
    this.loadData();
  },
  data: () => ({
    items: [],
    isLoading: false,
    filterQuery: '',
    withFavorites: true,
    tableColumns: COLUMNS,
    favorites: [],
    currentPage: 1,
    perPage: 40,
  }),
  computed: {
    filteredItems() {
      let filteredItems = this.items;
      if (!this.withFavorites) {
        filteredItems = filteredItems.filter(i => !this.isFavorite(i));
      }

      if (this.filterQuery === '') {
        return filteredItems;
      }

      return filteredItems.filter(i => i.name.toLowerCase().indexOf(this.filterQuery.toLocaleLowerCase()) !== -1);
    },
    cardActions() {
      return [
        { icon: 'fas fa-shopping-cart', handler: this.showShoppingList },
      ];
    },
    shoppingListHtml() {
      const wtb = this.items.filter(i => i.quantity > 0);

      return wtb.map(i => `${i.name}* ${i.quantity}`).join('<br>');
    },
  },
  methods: {
    async loadData() {
      this.isLoading = true;

      this.favorites = await this.$api.loadTradingFavorites();
      this.items = await this.$api.loadTradingProfitableItems();

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
    async showShoppingList() {
      $(this.$refs.shoppingListModal).modal();
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
