<template>
  <div class="container">
    <div class="row">
      <div class="col-8">
        <mk-card title="Outbidded orders" :loading="isLoading">
          <div class="form-group">
            <div class="input-group">
              <input v-model="filterQuery" type="text" class="form-control" placeholder="Filter items..." />
            </div>
          </div>

          <b-table :fields="tableColumns" :items="filteredOrders" sort-by="name" :sort-desc="false" :responsive="true">
            <template #cell(icon)="data">
              <div class="symbol symbol-30 d-block">
                <span class="symbol-label overflow-hidden">
                  <img :src="data.item.type.icon" class="module-icon" alt="">
                </span>
              </div>
            </template>

            <template #cell(actions)="data">
              <mk-market-details-button :id="data.item.type.type_id" />
            </template>
          </b-table>
        </mk-card>
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
    orders: [],
    isLoading: false,
    filterQuery: '',
    tableColumns: COLUMNS,
  }),
  computed: {
    outbiddedOrders() {
      return this.orders.filter(i => i.is_outbidded);
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

      this.orders = await this.$api.loadTradingOrders();

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
