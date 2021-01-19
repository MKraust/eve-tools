<template>
  <div class="container">
    <mk-card title="Favorites" :loading="isLoadingFavorites" :actions="cardActions">
      <b-table :fields="tableColumns" :items="favorites" sort-by="name" :sort-desc="false" :responsive="true">
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
          <div class="btn btn-hover-light-warning btn-sm btn-icon" @click="toggleFavorite(data.item.type_id)">
            <i class="text-warning fas fa-star"></i>
          </div>
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
export default {
  mounted() {
    this.loadFavorites();
  },
  data: () => ({
    favorites: [],
    isLoadingFavorites: false,
  }),
  computed: {
    cardActions() {
      return [
        { icon: 'fas fa-shopping-cart', handler: this.showShoppingList },
      ];
    },
    shoppingListHtml() {
      const wtb = this.favorites.filter(i => i.quantity > 0);

      return wtb.map(i => `${i.name}* ${i.quantity}`).join('<br>');
    },
    tableColumns() {
      return [
        {
          key: 'icon',
          tdClass: 'align-middle',
        },
        {
          key: 'name',
          sortable: true,
          class: 'text-nowrap',
          tdClass: 'align-middle',
        },
        {
          key: 'jita_price',
          sortable: true,
          label: 'Jita',
          class: 'text-right text-nowrap',
          tdClass: 'align-middle',
          sortByFormatted: (value, key, item) => item.prices.jita,
          formatter: (value, key, item) => item.prices.jita ? this.formatMoney(item.prices.jita) : '-',
        },
        {
          key: 'dichstar_price',
          sortable: true,
          label: 'Dichstar',
          class: 'text-right text-nowrap',
          tdClass: 'align-middle',
          sortByFormatted: (value, key, item) => item.prices.dichstar,
          formatter: (value, key, item) => item.prices.dichstar ? this.formatMoney(item.prices.dichstar) : '-',
        },
        {
          key: 'margin',
          sortable: true,
          class: 'text-right text-nowrap',
          tdClass: (value, key, item) => {
            const classes = ['align-middle'];
            if (item.prices.margin !== null) {
              classes.push(item.prices.margin > 0 ? 'text-success' : 'text-danger');
            }

            return classes.join(' ');
          },
          sortByFormatted: (value, key, item) => item.prices.margin,
          formatter: (value, key, item) => item.prices.margin ? this.formatMoney(item.prices.margin) : '-',
        },
        {
          key: 'margin_percent',
          sortable: true,
          label: 'Margin, %',
          class: 'text-right text-nowrap',
          tdClass: (value, key, item) => {
            const classes = ['align-middle'];
            if (item.prices.margin_percent !== null) {
              classes.push(item.prices.margin_percent > 0 ? 'text-success' : 'text-danger');
            }

            return classes.join(' ');
          },
          sortByFormatted: (value, key, item) => item.prices.margin_percent,
          formatter: (value, key, item) => item.prices.margin_percent ? `${item.prices.margin_percent}%` : '-',
        },
        {
          key: 'monthly_volume',
          sortable: true,
          label: 'M vol',
          class: 'text-right text-nowrap',
          tdClass: 'align-middle',
          sortByFormatted: (value, key, item) => item.prices.monthly_volume,
          formatter: (value, key, item) => item.prices.monthly_volume || '-',
        },
        {
          key: 'weekly_volume',
          sortable: true,
          label: 'W vol',
          class: 'text-right text-nowrap',
          tdClass: 'align-middle',
          sortByFormatted: (value, key, item) => item.prices.weekly_volume,
          formatter: (value, key, item) => item.prices.weekly_volume || '-',
        },
        {
          key: 'average_daily_volume',
          sortable: true,
          label: 'D vol',
          class: 'text-right text-nowrap',
          tdClass: 'align-middle',
          sortByFormatted: (value, key, item) => item.prices.average_daily_volume,
          formatter: (value, key, item) => item.prices.average_daily_volume || '-',
        },
        {
          key: 'quantity',
          label: 'Quantity',
          class: 'text-nowrap',
          tdClass: 'align-middle',
          thAttr: {
            style: 'width: 120px;',
          },
        },
        {
          key: 'actions',
          label: '',
          tdClass: 'align-middle',
          thAttr: {
            style: 'width: 53px;',
          },
        },
      ];
    },
  },
  methods: {
    async loadFavorites() {
      this.isLoadingFavorites = true;

      this.favorites = await this.$api.loadTradingFavorites();

      this.isLoadingFavorites = false;
    },
    async toggleFavorite(typeId) {
      console.log(typeId);
      await this.$api.deleteTradingFavorite(typeId);
      this.favorites = this.favorites.filter(f => f.type_id !== typeId);
    },
    async showShoppingList() {
      $(this.$refs.shoppingListModal).modal();
    },
    formatMoney(money) {
      return String(money).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    },
  },
}
</script>

<style scoped>
.module-icon {
  width: 100%;
  height: 100%;
}
</style>
