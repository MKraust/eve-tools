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
    confirmButtonText,
    icon: null,
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

export function debounce(f, ms) {
  let isCooldown = false;

  return function () {
    if (isCooldown) return;

    f.apply(this, arguments);

    isCooldown = true;

    setTimeout(() => isCooldown = false, ms);
  };
}
