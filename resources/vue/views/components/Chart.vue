<template>
  <ApexChart
    :type="type"
    :height="height"
    :options="options"
    :series="series"
  />
</template>

<script>
import ApexChart from 'vue-apexcharts';

import { formatNumber } from '@/helper';

export default {
  name: "Chart",
  components: {
    ApexChart,
  },
  props: {
    type: {
      type: String,
      default() {
        return 'bar';
      },
    },
    height: {
      type: Number,
      default() {
        return 400;
      },
    },
    chartData: {
      type: Array,
      default() {
        return [];
      },
    },
    ticks: {
      type: Number,
      required: true,
    },
  },
  computed: {
    options() {
      return {
        chart: {
          id: this._uid,
          toolbar: {
            show: false,
          },
        },
        plotOptions: {
          bar: {
            columnWidth: '95%',
          },
        },
        dataLabels: {
          enabled: false,
        },
        grid: {
          xaxis: {
            lines: {
              show: false,
            },
          },
        },
        xaxis: {
          categories: this.chartData.map(datum => datum.x),
          tickAmount: this.ticks,
          labels: {
            formatter: val => String(val).split(':')[0],
            hideOverflowingLabels: true,
          },
        },
        yaxis: {
          labels: {
            formatter: val => this.$numeral(val).format('0.0a'),
          },
        },
        tooltip: {
          y: {
            formatter: formatNumber,
          },
        },
      };
    },
    series() {
      return [
        {
          name: 'ISK',
          data: this.chartData.map(datum => datum.y),
        },
      ];
    },
  },
}
</script>
