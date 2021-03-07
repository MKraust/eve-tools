import { formatNumber, formatColumnValue } from '@/helper';

export default [
  {
    key: 'icon',
    tdClass: 'align-middle',
    thAttr: {
      style: 'width: 50px;',
    },
  },
  {
    key: 'name',
    sortable: true,
    class: 'text-wrap',
    tdClass: 'align-middle',
  },
  {
    key: 'buy',
    sortable: true,
    label: 'Buy',
    class: 'text-right text-nowrap',
    tdClass: 'align-middle',
    sortByFormatted: (value, key, item) => item.prices.buy,
    formatter: (value, key, item) => formatColumnValue(item.prices.buy, formatNumber),
  },
  {
    key: 'sell',
    sortable: true,
    label: 'Sell',
    class: 'text-right text-nowrap',
    tdClass: 'align-middle',
    sortByFormatted: (value, key, item) => item.prices.sell,
    formatter: (value, key, item) => formatColumnValue(item.prices.sell, formatNumber),
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
    formatter: (value, key, item) => formatColumnValue(item.prices.margin, formatNumber),
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
    formatter: (value, key, item) => formatColumnValue(item.prices.margin_percent, val => `${val}%`),
  },
  {
    key: 'monthly_volume',
    sortable: true,
    label: 'M vol',
    class: 'text-right text-nowrap',
    tdClass: 'align-middle',
    sortByFormatted: (value, key, item) => item.prices.monthly_volume,
    formatter: (value, key, item) => formatColumnValue(item.prices.monthly_volume, formatNumber),
  },
  {
    key: 'weekly_volume',
    sortable: true,
    label: 'W vol',
    class: 'text-right text-nowrap',
    tdClass: 'align-middle',
    sortByFormatted: (value, key, item) => item.prices.weekly_volume,
    formatter: (value, key, item) => formatColumnValue(item.prices.weekly_volume, formatNumber),
  },
  {
    key: 'average_daily_volume',
    sortable: true,
    label: 'D vol',
    class: 'text-right text-nowrap',
    tdClass: 'align-middle',
    sortByFormatted: (value, key, item) => item.prices.average_daily_volume,
    formatter: (value, key, item) => formatColumnValue(item.prices.average_daily_volume, formatNumber),
  },
  {
    key: 'potential_daily_profit',
    sortable: true,
    class: 'text-right text-nowrap',
    tdClass: (value, key, item) => {
      const classes = ['align-middle'];
      if (item.prices.potential_daily_profit !== null) {
        classes.push(item.prices.potential_daily_profit > 0 ? 'text-success' : 'text-danger');
      }

      return classes.join(' ');
    },
    sortByFormatted: (value, key, item) => item.prices.potential_daily_profit,
    formatter: (value, key, item) => formatColumnValue(item.prices.potential_daily_profit, formatNumber),
  },
  {
    key: 'in_stock',
    sortable: true,
    label: 'In stock',
    class: 'text-right text-nowrap',
    tdClass: 'align-middle',
    sortByFormatted: (value, key, item) => item.in_stock,
    formatter: (value, key, item) => {
      const inStock = formatColumnValue(item.in_stock, formatNumber);

      if (item.in_delivery === 0) {
        return inStock;
      }

      return inStock + ` (${item.in_delivery})`;
    },
  },
];
