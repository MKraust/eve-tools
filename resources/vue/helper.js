export function formatNumber(number) {
  return String(number).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}

export function formatColumnValue(val, formatter = null) {
  if (!val) {
    return '-';
  }

  return formatter ? formatter(val) : val;
}
