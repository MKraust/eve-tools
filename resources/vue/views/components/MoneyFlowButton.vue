<template>
  <div>
    <button class="btn btn-sm btn-icon btn-text-primary btn-hover-light-primary" @click="loadChartData">
      <i class="fas fa-chart-bar"></i>
    </button>

    <b-modal v-model="isModalShown" :id="`money-flow-modal-${id}`" :title="name" :busy="loading" size="lg" hide-footer>
      <mk-chart :chart-data="chartData" :height="300" :ticks="24" type="bar" />
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
      if (this.chartData.length > 0) {
        this.isModalShown = true;
        return;
      }

      this.loading = true;

      this.isModalShown = true;
      this.chartData = await this.$api.loadIntradayMoneyFlowData(this.id);

      this.loading = false;
    },
  },
};
</script>
