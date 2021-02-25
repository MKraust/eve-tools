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
    sortByFormatted: (value, key, item) => item.name,
    formatter: (value, key, item) => formatColumnValue(item.name),
  },
  {
    key: 'quantity',
    label: 'Quantity',
    class: 'text-right text-nowrap',
    tdClass: 'align-middle',
    formatter: (value, key, item) => formatColumnValue(item.quantity, formatNumber),
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
