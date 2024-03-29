import { formatNumber, formatColumnValue } from '@/helper';

import {
  ICON,
  NAME,
  AVERAGE_DAILY_VOLUME,
  POTENTIAL_DAILY_PROFIT,
  IN_STOCK
} from '../columns';

export const ACTIONS = {
  key: 'actions',
  label: '',
  tdClass: 'align-middle d-flex',
  thAttr: {
    style: 'width: 53px;',
  },
};

export const UNLISTED_ITEMS_COLUMNS = [
  ICON,
  NAME,
  AVERAGE_DAILY_VOLUME,
  POTENTIAL_DAILY_PROFIT,
  IN_STOCK,
  ACTIONS,
];

export const ORDERS_COLUMNS = [
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
    sortByFormatted: (value, key, item) => item.type.name,
    formatter: (value, key, item) => formatColumnValue(item.type.name),
  },
  // {
  //   key: 'volume',
  //   label: 'Volume',
  //   class: 'text-right text-nowrap',
  //   tdClass: 'align-middle monospace',
  //   formatter: (value, key, item) => formatColumnValue(item.volume_remain, formatNumber) + ' / ' + formatColumnValue(item.volume_total, formatNumber),
  // },
  // {
  //   key: 'price',
  //   sortable: true,
  //   label: 'Price',
  //   class: 'text-right text-nowrap',
  //   tdClass: 'align-middle monospace',
  //   sortByFormatted: (value, key, item) => item.price,
  //   formatter: (value, key, item) => formatColumnValue(item.price, formatNumber),
  // },
  // {
  //   key: 'outbid_margin',
  //   sortable: true,
  //   label: 'Outbid',
  //   class: 'text-right text-nowrap',
  //   tdClass: (value, key, item) => {
  //     const classes = ['align-middle', 'monospace'];
  //     if (item.outbid_margin !== null) {
  //       classes.push(item.outbid_margin > 0 ? 'text-success' : 'text-danger');
  //     }
  //
  //     return classes.join(' ');
  //   },
  //   sortByFormatted: (value, key, item) => item.outbid_margin,
  //   formatter: (value, key, item) => formatColumnValue(item.outbid_margin, formatNumber),
  // },
  {
    key: 'outbid_margin_percent',
    sortable: true,
    label: 'Outbid, %',
    class: 'text-right text-nowrap',
    tdClass: (value, key, item) => {
      const classes = ['align-middle', 'monospace'];
      if (item.outbid_margin_percent !== null) {
        classes.push(item.outbid_margin_percent > 0 ? 'text-success' : 'text-danger');
      }

      return classes.join(' ');
    },
    sortByFormatted: (value, key, item) => item.outbid_margin_percent,
    formatter: (value, key, item) => formatColumnValue(item.outbid_margin_percent, val => `${val}%`),
  },
  {
    key: 'margin_percent',
    sortable: true,
    label: 'Margin, %',
    class: 'text-right text-nowrap',
    tdClass: (value, key, item) => {
      const classes = ['align-middle', 'monospace'];
      if (item.type.prices.margin_percent !== null) {
        classes.push(item.type.prices.margin_percent > 0 ? 'text-success' : 'text-danger');
      }

      return classes.join(' ');
    },
    sortByFormatted: (value, key, item) => item.type.prices.margin_percent,
    formatter: (value, key, item) => formatColumnValue(item.type.prices.margin_percent, val => `${val}%`),
  },
  {
    key: 'potential_daily_profit',
    sortable: true,
    label: 'Potential Profit',
    class: 'text-right text-nowrap',
    tdClass: (value, key, item) => {
      const classes = ['align-middle', 'monospace'];
      if (item.type.prices.potential_daily_profit !== null) {
        classes.push(item.type.prices.potential_daily_profit > 0 ? 'text-success' : 'text-danger');
      }

      return classes.join(' ');
    },
    sortByFormatted: (value, key, item) => item.type.prices.potential_daily_profit,
    formatter: (value, key, item) => formatColumnValue(item.type.prices.potential_daily_profit, formatNumber),
  },
  ACTIONS,
];
