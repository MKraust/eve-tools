import { formatNumber, formatColumnValue } from '@/helper';

import COLUMNS from '../columns';

export default [
  ...COLUMNS,
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
