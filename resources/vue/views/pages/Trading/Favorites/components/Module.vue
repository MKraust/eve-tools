<template>
  <div class="d-flex align-items-center">
    <div class="symbol symbol-50 mr-5">
      <span class="symbol-label">
        <img :src="item.icon" class="module-icon" alt="">
      </span>
    </div>
    <div class="d-flex flex-column flex-grow-1 mr-5">
      <span class="font-size-h6 text-dark-75">{{ item.name }}</span>
      <div class="prices d-flex mt-2">
        <div class="d-flex flex-column mr-2">
          <p class="mb-1">Jita:</p>
          <p class="mb-0">Dichstar:</p>
        </div>
        <div class="d-flex flex-column mr-3" :style="{ width: '102px' }">
          <p class="font-weight-lighter mb-1">{{ item.prices.jita ? formatMoney(item.prices.jita) : '-' }}</p>
          <p class="font-weight-lighter mb-0">{{ item.prices.dichstar ? formatMoney(item.prices.dichstar) : '-' }}</p>
        </div>
        <div class="d-flex flex-column mr-2">
          <p class="mb-1">Margin:</p>
          <p class="mb-0">Margin percent:</p>
        </div>
        <div class="d-flex flex-column">
          <p class="font-weight-lighter mb-1" :class="item.prices.margin_percent > 0 ? 'text-success' : 'text-danger'">{{ item.prices.margin ? formatMoney(item.prices.margin) : '-' }}</p>
          <p class="font-weight-lighter mb-0" :class="item.prices.margin_percent > 0 ? 'text-success' : 'text-danger'">{{ item.prices.margin_percent ? `${item.prices.margin_percent}%` : '-' }}</p>
        </div>
      </div>
    </div>
    <div v-if="trackFormShown" class="tracking-form justify-content-end">
      <div class="form-group mb-0">
        <div class="input-group input-group-sm mr-1">
          <div class="input-group-prepend">
              <span class="input-group-text">
                <i class="fas fas fa-shopping-cart"></i>
              </span>
          </div>
          <input
            v-model="quantity"
            ref="quantity"
            type="number"
            class="form-control form-control-sm"
            placeholder="0"
          >
        </div>
      </div>
    </div>
    <div class="btn btn-hover-light-warning btn-sm btn-icon" @click="toggleFavorite">
      <i class="text-warning fa-star" :class="favorite ? 'fas' : 'far'"></i>
    </div>
  </div>
</template>

<script>
export default {
  name: "Module",
  props: {
    value: {
      type: Number,
      default() {
        return 0;
      }
    },
    trackFormShown: {
      type: Boolean,
      default() {
        return false;
      },
    },
    item: {
      required: true,
      type: Object,
    },
    favorite: {
      type: Boolean,
      default() {
        return false;
      }
    },
  },
  data: () => ({
    quantity: 0,
  }),
  watch: {
    value: {
      immediate: true,
      handler(val) {
        this.quantity = val;
      },
    },
    quantity: {
      handler(val) {
        this.$emit('input', Number(val));
      },
    },
  },
  methods: {
    toggleFavorite() {
      this.$emit('toggle-favorite');
    },
    formatMoney(money) {
      return String(money).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    },
  },
}
</script>

<style lang="scss" scoped>
.module-icon {
  width: 44px;
  height: 44px;

  border-radius: 0.42rem;
}

.tracking-form {
  display: flex;

  input {
    max-width: 60px;
  }
}
</style>
