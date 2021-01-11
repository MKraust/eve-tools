<template>
  <div class="d-flex align-items-center">
    <div class="symbol symbol-50 mr-5">
      <span class="symbol-label">
        <img :src="item.icon" class="module-icon" :alt="item.name">
      </span>
    </div>
    <div class="d-flex flex-column flex-grow-1">
      <span class="font-size-h6 text-dark-75">{{ item.name }}</span>

      <div v-show="isTrackingFormShown" class="tracking-form justify-content-start mt-2">
        <div class="form-group mb-0">
          <div class="input-group input-group-sm mr-1">
            <div class="input-group-prepend">
              <span class="input-group-text">
                <i class="fas fa-industry"></i>
              </span>
            </div>
            <input
              v-model="productionCount"
              ref="productionCountInput"
              type="number"
              class="form-control form-control-sm"
              placeholder="0"
              @keyup.enter="trackType"
            >
          </div>
        </div>
        <div v-if="item.tech_level === 2" class="form-group mb-0">
          <div class="input-group mr-1">
            <div class="input-group-prepend">
              <span class="input-group-text">
                <i class="fas fa-microscope"></i>
              </span>
            </div>
            <input
              v-model="inventionCount"
              type="number"
              class="form-control form-control-sm"
              placeholder="0"
              @keyup.enter="trackType"
            >
          </div>
        </div>
        <div>
          <div class="btn btn-hover-light-primary btn-sm btn-icon" @click="trackType">
            <i class="text-primary fas fa-save"></i>
          </div>
        </div>
      </div>

      <div class="prices d-flex mt-2">
        <div class="d-flex flex-column mr-2">
          <p class="mb-1">Prod. cost:</p>
          <p class="mb-0">Dichstar:</p>
        </div>
        <div class="d-flex flex-column mr-3" :style="{ width: '102px' }">
          <p class="font-weight-lighter mb-1">{{ item.costs.total ? formatMoney(item.costs.total) : '-' }}</p>
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
    <div v-if="trackButtonShown" class="btn btn-hover-light-primary btn-sm btn-icon" @click="toggleTrackingForm">
      <i class="text-primary fas" :class="isTrackingFormShown ? 'fa-eye-slash' : 'fa-eye'"></i>
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
    trackButtonShown: {
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
    isTrackingFormShown: false,
    productionCount: 0,
    inventionCount: 0,
  }),
  methods: {
    toggleFavorite() {
      this.$emit('toggle-favorite');
    },
    toggleTrackingForm() {
      this.isTrackingFormShown = !this.isTrackingFormShown;
      this.productionCount = 0;
      this.inventionCount = 0;

      this.$nextTick(() => {
        this.$refs.productionCountInput.focus();
      });
    },
    async trackType() {
      if (this.productionCount === 0 && this.inventionCount === 0) {
        this.toggleTrackingForm();
        return;
      }

      await this.$api.addTrackedType(this.item.type_id, this.productionCount || 0, this.inventionCount || 0);
      this.toggleTrackingForm();
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
