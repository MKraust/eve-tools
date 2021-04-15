<template>
  <div>
    <button :id="buttonId" class="btn btn-sm btn-icon btn-text-primary btn-hover-light-primary" @click="showHistory">
      <i class="fas fa-history"></i>
    </button>

    <b-popover
      :target="buttonId"
      :show.sync="shown"
      triggers="click blur"
      placement="left"
      custom-class="popover-custom"
    >
      <template #title>
        {{ title }}
      </template>

      <b-table
        :items="history"
        :fields="columns"
        :busy="!loaded"
      >
        <template #table-busy>
          <div class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
        </template>
      </b-table>
    </b-popover>
  </div>
</template>

<script>
import { formatColumnValue, formatNumber } from '@/helper';

export default {
  props: {
    id: Number,
    icon: String,
    title: String,
  },
  data() {
    return {
      loaded: false,
      shown: false,
      history: [],
    };
  },
  computed: {
    buttonId() {
      return `history-button-${this.id}`;
    },
    columns() {
      return [
        {
          key: 'date',
          sortable: true,
          label: 'Date',
          class: 'text-nowrap',
          tdClass: 'text-left monospace',
        },
        {
          key: 'quantity',
          sortable: false,
          label: 'Qty',
          class: 'text-nowrap',
          tdClass: 'text-right monospace',
        },
        {
          key: 'buy',
          sortable: false,
          label: 'Buy',
          class: 'text-right text-nowrap',
          tdClass: 'align-middle monospace',
          sortByFormatted: (value, key, item) => item.buy,
          formatter: (value, key, item) => formatColumnValue(item.buy, formatNumber),
        },
        {
          key: 'margin',
          sortable: false,
          class: 'text-right text-nowrap',
          tdClass: (value, key, item) => {
            const classes = ['align-middle', 'monospace'];
            if (item.margin !== null) {
              classes.push(item.margin > 0 ? 'text-success' : 'text-danger');
            }

            return classes.join(' ');
          },
          sortByFormatted: (value, key, item) => item.margin,
          formatter: (value, key, item) => formatColumnValue(item.margin, formatNumber),
        },
        {
          key: 'margin_percent',
          sortable: false,
          label: 'Margin, %',
          class: 'text-right text-nowrap',
          tdClass: (value, key, item) => {
            const classes = ['align-middle', 'monospace'];
            if (item.margin_percent !== null) {
              classes.push(item.margin_percent > 0 ? 'text-success' : 'text-danger');
            }

            return classes.join(' ');
          },
          sortByFormatted: (value, key, item) => item.margin_percent,
          formatter: (value, key, item) => formatColumnValue(item.margin_percent, val => `${val}%`),
        },
      ];
    },
  },
  methods: {
    async showHistory() {
      if (this.shown) {
        this.shown = false;
        return;
      }

      if (!this.loaded) {
        this.history = await this.$api.loadItemHistory(this.id);
        this.loaded = true;
      }

      this.shown = true;
    },
  },
};
</script>

<style scoped>
.popover-custom {
  max-width: 600px;
}
</style>
