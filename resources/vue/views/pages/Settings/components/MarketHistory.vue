<template>
  <mk-card title="Market history update" :loading="!isInitialSettingsLoadingDone" icon="fas fa-chart-line">
    <div v-if="settings !== null" class="row">
      <div class="col-2">
        <div class="font-size-sm text-muted font-weight-bold">Start date</div>
        <div class="font-size-h4 font-weight-bolder">{{ startDate }}</div>
      </div>
      <div class="col-2">
        <div class="font-size-sm text-muted font-weight-bold">End date</div>
        <div class="font-size-h4 font-weight-bolder">{{ endDate }}</div>
      </div>
      <div class="col-8">
        <div class="d-flex align-items-center">
          <div class="symbol symbol-40 mr-3 flex-shrink-0" :class="`symbol-${statusColor}`">
            <div class="symbol-label">
              <span class="svg-icon svg-icon-2x" :class="`svg-icon-${iconStatusColor}`">
                <component :is="statusIconComponent" />
              </span>
            </div>
          </div>
          <div :style="{ width: '145px' }" class="mr-5">
            <div class="font-size-sm text-muted font-weight-bold mb-1">Items</div>
            <div class="font-size-h6 text-dark-75 font-weight-bolder">{{ statusText }}</div>
          </div>
          <div class="progress flex-grow-1" style="height: 40px;">
            <div class="progress-bar" :class="{ [`bg-${iconStatusColor}`]: true, ['progress-bar-striped progress-bar-animated']: isUpdateInProgress }" :style="{ width: `${progress}%` }"></div>
          </div>
        </div>
      </div>
    </div>
  </mk-card>
</template>

<script>
import moment from 'moment';

import ThreeDotsIcon from '@/views/components/svg/ThreeDots';
import CheckIcon from '@/views/components/svg/Check';
import ErrorCircleIcon from '@/views/components/svg/ErrorCircle';

export default {
  name: "MarketHistory",
  mounted() {
    this.startWatchingMarketOrdersUpdate();
  },
  destroyed() {
    this.stopWatchingMarketOrdersUpdate();
  },
  data: () => ({
    isInitialSettingsLoadingDone: false,
    settings: null,
    isWatchingMarketOrdersUpdate: false,
  }),
  computed: {
    startDate() {
      return this.settings.start_date ? moment(this.settings.start_date).locale('en-ie').format('lll') : '-';
    },
    endDate() {
      return this.settings.end_date ? moment(this.settings.end_date).locale('en-ie').format('lll') : '-';
    },
    isUpdateInProgress() {
      return this.settings && this.settings.status === 'in_progress';
    },
    isUpdateFailed() {
      return this.settings && this.settings.status === 'error';
    },
    isUpdateFinished() {
      return this.settings && this.settings.status === 'finished';
    },
    progress() {
      if (!this.settings) {
        return 0;
      }

      const total = this.settings.progress.total_types;
      const processed = this.settings.progress.types_processed;

      return total ? Math.floor(processed / total * 100) : 0;
    },
    statusText() {
      return `${this.settings.progress.types_processed || '-' } / ${this.settings.progress.total_types || '-' }`;
    },
    statusIconComponent() {
      if (this.isUpdateFinished) {
        return CheckIcon;
      }

      return this.isUpdateInProgress ? ThreeDotsIcon : ErrorCircleIcon;
    },
    statusColor() {
      if (this.isUpdateFinished) {
        return 'light-success';
      }

      return this.isUpdateInProgress ? 'light' : 'light-danger';
    },
    iconStatusColor() {
      if (this.isUpdateFinished) {
        return 'success';
      }

      return this.isUpdateInProgress ? 'info' : 'danger';
    },
  },
  methods: {
    async loadSettings() {
      this.settings = await this.$api.loadMarketHistoryUpdateInfo();

      this.isInitialSettingsLoadingDone = true;
    },
    startWatchingMarketOrdersUpdate() {
      if (this.isWatchingMarketOrdersUpdate) {
        return;
      }

      this.isWatchingMarketOrdersUpdate = true;
      this.runAsyncInterval(async () => {
        await this.loadSettings();
      }, 1000);
    },
    stopWatchingMarketOrdersUpdate() {
      if (this.isWatchingMarketOrdersUpdate) {
        this.isWatchingMarketOrdersUpdate = false;
      }
    },
    async runAsyncInterval (cb, interval) {
      await cb();
      if (this.isWatchingMarketOrdersUpdate) {
        setTimeout(() => this.runAsyncInterval(cb, interval), interval);
      }
    },
  }
}
</script>

<style scoped>

</style>
