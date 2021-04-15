<template>
  <div class="container">
    <div class="row">
      <div class="col-md-5">
        <mk-card title="Intraday money flow" :loading="isLoading" class="mb-10">
          <mk-chart :chart-data="intradayMoneyFlowData" :height="300" :ticks="24" type="bar" />
        </mk-card>
      </div>

      <div class="col-md-7">
        <mk-card title="Unlisted items">
          <b-table :busy="isLoading" :fields="unlistedItemsColumns" :items="unlistedItems" sort-by="potential_daily_profit" :sort-desc="true" :responsive="true">
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

            <template #cell(actions)="data">
              <mk-money-flow-button :id="data.item.type_id" :name="data.item.name" />
              <mk-market-details-button :id="data.item.type_id" />
            </template>
          </b-table>
        </mk-card>
      </div>

      <div class="col-md-12">
        <mk-card title="Outbidded orders">
          <div class="d-flex align-items-center mb-7">
            <div class="flex-grow-1">
              <input v-model="filterQuery" type="text" class="form-control" placeholder="Filter items..." />
            </div>
            <div class="ml-5">
              Total: <b>{{ filteredOrders.length }}</b>
            </div>
          </div>

          <b-table :busy="isLoading" :fields="ordersColumns" :items="filteredOrders" sort-by="outbid_margin_percent" :sort-desc="true" :responsive="true">
            <template #table-busy>
              <div class="text-center text-primary my-2">
                <b-spinner class="align-middle mr-2"></b-spinner>
                <strong>Loading...</strong>
              </div>
            </template>

            <template #cell(icon)="data">
              <div class="symbol symbol-30 d-block">
                <span class="symbol-label overflow-hidden">
                  <img :src="data.item.type.icon" class="module-icon" alt="">
                </span>
              </div>
            </template>

            <template #cell(actions)="data">
              <mk-item-history-button :id="data.item.type.type_id" :title="data.item.type.name" :icon="data.item.type.icon" />
              <mk-money-flow-button :id="data.item.type.type_id" :name="data.item.type.name" />
              <mk-market-details-button :id="data.item.type.type_id" />
            </template>
          </b-table>
        </mk-card>
      </div>
    </div>
  </div>
</template>

<script>
import { ORDERS_COLUMNS, UNLISTED_ITEMS_COLUMNS } from './columns';

export default {
  mounted() {
    this.loadData();
  },
  data: () => ({
    orders: [],
    isLoading: false,
    filterQuery: '',
    ordersColumns: ORDERS_COLUMNS,
    unlistedItemsColumns: UNLISTED_ITEMS_COLUMNS,
    intradayMoneyFlowData: [],
    unlistedItems: [],
  }),
  computed: {
    outbiddedOrders() {
      return this.orders.filter(i => i.outbid_margin);
    },
    filteredOrders() {
      if (this.filterQuery === '') {
        return this.outbiddedOrders;
      }

      return this.outbiddedOrders.filter(i => i.type.name.toLowerCase().indexOf(this.filterQuery.toLocaleLowerCase()) !== -1);
    },
  },
  methods: {
    async loadData() {
      this.isLoading = true;

      const [unlistedItems, orders, intradayMoneyFlowData] = await Promise.all([
        this.$api.loadUnlistedItems(),
        this.$api.loadTradingOrders(),
        this.$api.loadIntradayMoneyFlowData(),
      ])

      this.unlistedItems = unlistedItems;
      this.orders = orders;
      this.intradayMoneyFlowData = intradayMoneyFlowData;

      this.isLoading = false;
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
