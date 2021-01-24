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
    class: 'text-nowrap',
    tdClass: 'align-middle',
    sortByFormatted: (value, key, item) => item.type.name,
    formatter: (value, key, item) => formatColumnValue(item.type.name),
  },
  {
    key: 'volume',
    label: 'Volume',
    class: 'text-right text-nowrap',
    tdClass: 'align-middle',
    formatter: (value, key, item) => formatColumnValue(item.volume_remain, formatNumber) + ' / ' + formatColumnValue(item.volume_total, formatNumber),
  },
  {
    key: 'price',
    sortable: true,
    label: 'Price',
    class: 'text-right text-nowrap',
    tdClass: 'align-middle',
    sortByFormatted: (value, key, item) => item.price,
    formatter: (value, key, item) => formatColumnValue(item.price, formatNumber),
  },
  {
    key: 'outbid_margin',
    sortable: true,
    label: 'Outbid',
    class: 'text-right text-nowrap',
    tdClass: (value, key, item) => {
      const classes = ['align-middle'];
      if (item.outbid_margin !== null) {
        classes.push(item.outbid_margin > 0 ? 'text-success' : 'text-danger');
      }

      return classes.join(' ');
    },
    sortByFormatted: (value, key, item) => item.outbid_margin,
    formatter: (value, key, item) => formatColumnValue(item.outbid_margin, formatNumber),
  },
  {
    key: 'outbid_margin_percent',
    sortable: true,
    label: 'Outbid, %',
    class: 'text-right text-nowrap',
    tdClass: (value, key, item) => {
      const classes = ['align-middle'];
      if (item.outbid_margin_percent !== null) {
        classes.push(item.outbid_margin_percent > 0 ? 'text-success' : 'text-danger');
      }

      return classes.join(' ');
    },
    sortByFormatted: (value, key, item) => item.outbid_margin_percent,
    formatter: (value, key, item) => formatColumnValue(item.outbid_margin_percent, val => `${val}%`),
  },
];
