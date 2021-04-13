<template>
  <mk-card title="Daily profit" :loading="loading" class="mb-10">
    <mk-chart :chart-data="chartData" :height="300" :ticks="30" type="bar" />
  </mk-card>
</template>

<script>
export default {
  created() {
    this.loadData();
  },
  data() {
    return {
      loading: false,
      statistics: [],
    };
  },
  computed: {
    chartData() {
      return this.statistics.map(item => ({
        x: item.day,
        y: item.profit,
      }));
    },
  },
  methods: {
    async loadData() {
      this.loading = true;

      this.statistics = await this.$api.loadDailyProfitStatistics(30);

      this.loading = false;
    },
  },
};
</script>
