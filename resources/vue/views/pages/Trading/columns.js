import { formatNumber, formatColumnValue } from '@/helper';

export const ICON = {
  key: 'icon',
  tdClass: 'align-middle',
  thAttr: {
    style: 'width: 60px;',
  },
};

export const NAME = {
  key: 'name',
  sortable: true,
  class: 'text-wrap',
  tdClass: 'align-middle',
};

export const PRICES = {
  key: 'prices',
  sortable: false,
  label: 'Prices',
  class: 'text-right text-nowrap',
  tdClass: 'flex-column align-middle',
  tdAttr: {
    style: 'font-size: 0.94rem !important',
  },
  sortByFormatted: (value, key, item) => item.prices.buy,
};

export const MARGIN = {
  key: 'margin',
  sortable: true,
  label: 'Margin',
  class: 'text-right text-nowrap',
  tdClass: (value, key, item) => {
    const classes = ['flex-column', 'align-middle'];
    if (item.prices.margin !== null) {
      classes.push(item.prices.margin > 0 ? 'text-success' : 'text-danger');
    }

    return classes.join(' ');
  },
  tdAttr: {
    style: 'font-size: 0.94rem !important',
  },
  sortByFormatted: (value, key, item) => item.prices.margin_percent,
};

export const VOLUME = {
  key: 'volume',
  sortable: false,
  label: 'Volume',
  class: 'text-right text-nowrap',
  tdClass: 'flex-column align-middle',
  tdAttr: {
    style: 'font-size: 0.86rem !important',
  },
}

export const POTENTIAL_DAILY_PROFIT = {
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
};

export const IN_STOCK = {
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
};

export default [
  ICON,
  NAME,
  PRICES,
  MARGIN,
  VOLUME,
  POTENTIAL_DAILY_PROFIT,
  IN_STOCK,
];
