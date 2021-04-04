import COLUMNS from '../columns';

export default [
  ...COLUMNS,
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
    key: 'fast_shopping_limit',
    label: 'FS limit',
    class: 'text-nowrap',
    tdClass: 'align-middle',
    thAttr: {
      style: 'width: 120px;',
    },
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
