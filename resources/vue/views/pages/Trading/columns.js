import { formatNumber, formatColumnValue } from '@/helper';

export const ICON = {
  key: 'icon',
  tdClass: 'align-middle',
  thAttr: {
    style: 'width: 50px;',
  },
};

export const NAME = {
  key: 'name',
  sortable: true,
  class: 'text-wrap',
  tdClass: 'align-middle',
};

export const TOTAL_COST = {
  key: 'buy',
  sortable: true,
  label: 'Buy',
  class: 'text-right text-nowrap',
  tdClass: 'align-middle',
  sortByFormatted: (value, key, item) => item.prices.buy,
  formatter: (value, key, item) => formatColumnValue(item.prices.buy, formatNumber),
};

export const SELL_PRICE = {
  key: 'sell',
  sortable: true,
  label: 'Sell',
  class: 'text-right text-nowrap',
  tdClass: 'align-middle',
  sortByFormatted: (value, key, item) => item.prices.sell,
  formatter: (value, key, item) => formatColumnValue(item.prices.sell, formatNumber),
}

export const MARGIN = {
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
};

export const MARGIN_PERCENT = {
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
};

export const MONTHLY_VOLUME = {
  key: 'monthly_volume',
  sortable: true,
  label: 'M vol',
  class: 'text-right text-nowrap',
  tdClass: 'align-middle',
  sortByFormatted: (value, key, item) => item.prices.monthly_volume,
  formatter: (value, key, item) => formatColumnValue(item.prices.monthly_volume, formatNumber),
};

export const WEEKLY_VOLUME = {
  key: 'weekly_volume',
  sortable: true,
  label: 'W vol',
  class: 'text-right text-nowrap',
  tdClass: 'align-middle',
  sortByFormatted: (value, key, item) => item.prices.weekly_volume,
  formatter: (value, key, item) => formatColumnValue(item.prices.weekly_volume, formatNumber),
};

export const AVERAGE_DAILY_VOLUME = {
  key: 'average_daily_volume',
  sortable: true,
  label: 'D vol',
  class: 'text-right text-nowrap',
  tdClass: 'align-middle',
  sortByFormatted: (value, key, item) => item.prices.average_daily_volume,
  formatter: (value, key, item) => formatColumnValue(item.prices.average_daily_volume, formatNumber),
};

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
  TOTAL_COST,
  SELL_PRICE,
  MARGIN,
  MARGIN_PERCENT,
  MONTHLY_VOLUME,
  WEEKLY_VOLUME,
  AVERAGE_DAILY_VOLUME,
  POTENTIAL_DAILY_PROFIT,
  IN_STOCK,
];
