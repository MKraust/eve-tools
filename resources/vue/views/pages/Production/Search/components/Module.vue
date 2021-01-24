<template>
  <div class="d-flex align-items-start">
    <div class="symbol symbol-50 mr-5">
      <span class="symbol-label overflow-hidden">
        <img :src="item.icon" class="module-icon" alt="">
      </span>
    </div>
    <div class="d-flex flex-column flex-grow-1 mr-5">
      <span class="font-size-h6 font-weight-bold">{{ item.name }}</span>

      <div class="metrics d-flex mt-2">
        <div
          v-for="(metricsGroup, metricsGroupIndex) in metrics"
          :key="`group_${metricsGroupIndex}`"
          class="d-flex mr-3"
        >
          <div class="d-flex flex-column text-muted mr-2">
            <p v-for="(metric, metricIndex) in metricsGroup" :key="`metric_title_${metricIndex}`" :class="`mb-${metricIndex === metricsGroup.length ? 0 : 1}`">
              {{ metric.title }}
            </p>
          </div>
          <div class="d-flex flex-column font-weight-bold" :style="{ width: metricsGroupIndex === metrics.length ? 'auto' : '102px' }">
            <p
              v-for="(metric, metricIndex) in metricsGroup"
              :key="`metric_value_${metricIndex}`"
              :class="[
                `mb-${metricIndex === metricsGroup.length ? 0 : 1}`,
                metric.status && `text-${metric.status}`,
              ]"
            >
              {{ metric.value }}
            </p>
          </div>
        </div>
      </div>

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
  computed: {
    metrics() {
      return [
        [
          {
            title: 'Prod. cost',
            value: this.item.costs.total ? this.formatMoney(this.item.costs.total) : '-',
          },
          {
            title: 'Dichstar',
            value: this.item.prices.dichstar ? this.formatMoney(this.item.prices.dichstar) : '-',
          },
          {
            title: 'Margin',
            value: this.item.prices.margin ? this.formatMoney(this.item.prices.margin) : '-',
            status: this.item.prices.margin_percent > 0 ? 'success' : 'danger',
          },
          {
            title: 'Margin, %',
            value: this.item.prices.margin_percent ? `${this.formatMoney(this.item.prices.margin_percent)}%` : '-',
            status: this.item.prices.margin_percent > 0 ? 'success' : 'danger',
          },
        ],
        [
          {
            title: 'Monthly',
            value: this.item.prices.monthly_volume ? this.item.prices.monthly_volume : '-',
          },
          {
            title: 'Weekly',
            value: this.item.prices.weekly_volume ? this.item.prices.weekly_volume : '-',
          },
          {
            title: 'Avg. daily',
            value: this.item.prices.average_daily_volume ? Number(this.item.prices.average_daily_volume).toFixed(2) : '-',
          },
        ],
      ];
    },
  },
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
      return String(money).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    },
  },
}
</script>

<style lang="scss" scoped>
.module-icon {
  width: 100%;
  height: 100%;
}

.tracking-form {
  display: flex;

  input {
    max-width: 60px;
  }
}
</style>
