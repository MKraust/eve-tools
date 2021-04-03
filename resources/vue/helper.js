export function formatNumber(number) {
  return String(number).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}

export function formatColumnValue(val, formatter = null) {
  if (!val) {
    return '-';
  }

  return formatter ? formatter(val) : val;
}

export async function confirm(title, text, confirmButtonText) {
  const result = await Swal.fire({
    title,
    text,
    icon,
    confirmButtonText,
    buttonsStyling: false,
    showCancelButton: true,
    cancelButtonText: "Cancel",
    customClass: {
      confirmButton: "btn btn-danger",
      cancelButton: "btn btn-default"
    }
  });

  return result.value;
}
