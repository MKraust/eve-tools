import COLUMNS from '../columns';

export default [
  ...COLUMNS,
  {
    key: 'watch_form',
    label: '',
    class: 'text-nowrap',
    tdClass: 'align-middle',
    thAttr: {
      style: 'width: 250px;',
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
