<template>
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <daily-profit-statistics />
      </div>
      <div class="col-md-12">
        <mk-card title="Profits">
          <div class="d-flex align-items-center mb-7">
            <div class="flex-grow-1">
              <input v-model="filterQuery" type="text" class="form-control" placeholder="Filter items..." />
            </div>
          </div>

          <b-table
            :busy="isLoading"
            :fields="tableColumns"
            :items="loadData"
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
                <img :src="data.item.type.icon" class="module-icon" alt="">
              </span>
              </div>
            </template>

            <template #cell(actions)="data">
              <div class="d-flex">
                <mk-money-flow-button :id="data.item.type_id" :name="data.item.type.typeName" />
                <mk-market-details-button :id="data.item.type_id" />
              </div>
            </template>
          </b-table>

          <b-pagination
            v-model="currentPage"
            :total-rows="total"
            :per-page="perPage"
            aria-controls="my-table"
          ></b-pagination>
        </mk-card>
      </div>
    </div>
  </div>
</template>

<script>
import COLUMNS from './columns';

import DailyProfitStatistics from './components/DailyProfitStatistics';

export default {
  components: {
    DailyProfitStatistics,
  },
  data: () => ({
    isLoading: false,
    filterQuery: '',
    tableColumns: COLUMNS,
    currentPage: 1,
    perPage: 50,
    total: 0,
  }),
  computed: {
    filteredItems() {
      if (this.filterQuery === '') {
        return this.items;
      }

      return this.items.filter(i => i.name.toLowerCase().indexOf(this.filterQuery.toLocaleLowerCase()) !== -1);
    },
  },
  methods: {
    async loadData() {
      this.isLoading = true;

      const { items, total } = await this.$api.loadTradingProfits(this.currentPage, this.perPage);

      this.total = total;
      this.isLoading = false;

      return items;
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
