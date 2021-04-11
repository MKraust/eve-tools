import { formatColumnValue, formatNumber } from '@/helper';

export default [
  {
    key: 'date',
    sortable: false,
    class: 'text-nowrap',
    tdClass: 'align-middle monospace',
  },
  {
    key: 'icon',
    tdClass: 'align-middle',
    thAttr: {
      style: 'width: 50px;',
    },
  },
  {
    key: 'name',
    sortable: false,
    class: 'text-nowrap',
    tdClass: 'align-middle',
    sortByFormatted: (value, key, item) => item.type.typeName,
    formatter: (value, key, item) => formatColumnValue(item.type.typeName),
  },
  {
    key: 'buy',
    sortable: false,
    label: 'Buy',
    class: 'text-right text-nowrap',
    tdClass: 'align-middle monospace',
    sortByFormatted: (value, key, item) => item.buy_transaction.unit_price,
    formatter: (value, key, item) => formatColumnValue(item.buy_transaction.unit_price, formatNumber),
  },
  {
    key: 'sell',
    sortable: false,
    label: 'Sell',
    class: 'text-right text-nowrap',
    tdClass: 'align-middle monospace',
    sortByFormatted: (value, key, item) => item.sell_transaction.unit_price,
    formatter: (value, key, item) => formatColumnValue(item.sell_transaction.unit_price, formatNumber),
  },
  {
    key: 'quantity',
    sortable: false,
    label: 'Quantity',
    class: 'text-right text-nowrap',
    tdClass: 'align-middle monospace',
    sortByFormatted: (value, key, item) => item.quantity,
    formatter: (value, key, item) => formatColumnValue(item.quantity, formatNumber),
  },
  {
    key: 'delivery_cost',
    sortable: false,
    label: 'Delivery Cost',
    class: 'text-right text-nowrap',
    tdClass: 'align-middle monospace',
    sortByFormatted: (value, key, item) => item.delivery_cost,
    formatter: (value, key, item) => formatColumnValue(item.delivery_cost, formatNumber),
  },
  {
    key: 'profit',
    sortable: false,
    label: 'Profit',
    class: 'text-right text-nowrap',
    tdClass: (value, key, item) => {
      const classes = ['align-middle', 'monospace'];
      if (item.profit !== null) {
        classes.push(item.profit > 0 ? 'text-success' : 'text-danger');
      }

      return classes.join(' ');
    },
    sortByFormatted: (value, key, item) => item.profit,
    formatter: (value, key, item) => formatColumnValue(parseInt(item.profit), formatNumber),
  },
  {
    key: 'actions',
    label: '',
    tdClass: 'align-middle d-flex',
    thAttr: {
      style: 'width: 53px;',
    },
  },
];
