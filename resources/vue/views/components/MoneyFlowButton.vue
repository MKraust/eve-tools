<template>
  <div>
    <button class="btn btn-sm btn-icon btn-text-primary btn-hover-light-primary" @click="loadChartData">
      <i class="fas fa-chart-bar"></i>
    </button>

    <b-modal :id="`money-flow-modal-${id}`" :title="name" :busy="loading" :visible="isModalShown" size="lg" hide-footer>
      <mk-chart :chart-data="chartData" :height="300" type="area" />
    </b-modal>
  </div>
</template>

<script>
export default {
  props: {
    id: Number,
    name: String,
  },
  data: () => ({
    chartData: [],
    isModalShown: false,
    loading: false,
  }),
  methods: {
    async loadChartData() {
      this.loading = true;

      this.chartData = [];
      this.isModalShown = true;
      this.chartData = await this.$api.loadIntradayMoneyFlowData(this.id);

      this.loading = false;
    },
  },
};
</script>
