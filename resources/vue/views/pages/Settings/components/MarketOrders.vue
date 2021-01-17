<template>
  <mk-card title="Market prices update" :loading="!isInitialSettingsLoadingDone" icon="fas fa-chart-line">
    <div v-if="settings !== null" class="row">
      <div class="col-3">
        <div class="d-flex flex-column">
          <div class="mb-8">
            <div class="font-size-sm text-muted font-weight-bold">Start date</div>
            <div class="font-size-h4 font-weight-bolder">{{ startDate }}</div>
          </div>
          <div>
            <div class="font-size-sm text-muted font-weight-bold">End date</div>
            <div class="font-size-h4 font-weight-bolder">{{ endDate }}</div>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="d-flex flex-column">
          <div class="d-flex align-items-center mb-8">
            <div class="symbol symbol-40 mr-3 flex-shrink-0" :class="`symbol-${dichstarOrdersUpdateStatusColor}`">
              <div class="symbol-label">
                <span class="svg-icon svg-icon-2x" :class="`svg-icon-${dichstarOrdersUpdateIconStatusColor}`">
                  <component :is="dichstarOrdersUpdateIconComponent" />
                </span>
              </div>
            </div>
            <div :style="{ width: '120px' }" class="mr-5">
              <div class="font-size-sm text-muted font-weight-bold mb-1">Dichstar orders</div>
              <div class="font-size-h6 text-dark-75 font-weight-bolder">{{ dichstarOrdersUpdateText }}</div>
            </div>
            <div class="progress flex-grow-1" style="height: 40px;">
              <div class="progress-bar" :class="{ [`bg-${dichstarOrdersUpdateIconStatusColor}`]: true, ['progress-bar-striped progress-bar-animated']: isUpdateInProgress && !isDichstarOrdersUpdateFinished }" :style="{ width: `${dichstarProgress}%` }"></div>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <div class="symbol symbol-40 mr-3 flex-shrink-0" :class="`symbol-${jitaOrdersUpdateStatusColor}`">
              <div class="symbol-label">
                <span class="svg-icon svg-icon-2x" :class="`svg-icon-${jitaOrdersUpdateIconStatusColor}`">
                  <component :is="jitaOrdersUpdateIconComponent" />
                </span>
              </div>
            </div>
            <div :style="{ width: '120px' }" class="mr-5">
              <div class="font-size-sm text-muted font-weight-bold mb-1">Jita orders</div>
              <div class="font-size-h6 text-dark-75 font-weight-bolder">{{ jitaOrdersUpdateText }}</div>
            </div>
            <div class="progress flex-grow-1" style="height: 40px;">
              <div class="progress-bar" :class="{ [`bg-${jitaOrdersUpdateIconStatusColor}`]: true, ['progress-bar-striped progress-bar-animated']: isUpdateInProgress && !isJitaOrdersUpdateFinished }" :style="{ width: `${jitaProgress}%` }"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-3">
        <div class="d-flex flex-column">
          <div class="d-flex align-items-center mb-8">
            <div class="symbol symbol-40 mr-3 flex-shrink-0" :class="`symbol-${pricesUpdateStatusColor}`">
              <div class="symbol-label">
                <span class="svg-icon svg-icon-2x" :class="`svg-icon-${pricesUpdateIconStatusColor}`">
                  <component :is="pricesUpdateIconComponent" />
                </span>
              </div>
            </div>
            <div>
              <div class="font-size-sm text-muted font-weight-bold mb-1">Prices update</div>
              <div class="font-size-h6 text-dark-75 font-weight-bolder">{{ pricesUpdateText }}</div>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <div class="symbol symbol-40 mr-3 flex-shrink-0" :class="`symbol-${volumesUpdateStatusColor}`">
              <div class="symbol-label">
                <span class="svg-icon svg-icon-2x" :class="`svg-icon-${volumesUpdateIconStatusColor}`">
                  <component :is="volumesUpdateIconComponent" />
                </span>
              </div>
            </div>
            <div>
              <div class="font-size-sm text-muted font-weight-bold mb-1">Volumes update</div>
              <div class="font-size-h6 text-dark-75 font-weight-bolder">{{ volumesUpdateText }}</div>
            </div>
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
  name: "MarketOrders",
  components: {
    ThreeDotsIcon,
    CheckIcon,
    ErrorCircleIcon,
  },
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
    jitaProgress() {
      if (!this.settings) {
        return 0;
      }

      const total = this.settings.progress.jita.total_pages;
      const processed = this.settings.progress.jita.processed_pages;

      return total ? Math.floor(processed / total * 100) : 0;
    },
    isJitaOrdersUpdateFinished() {
      return this.jitaProgress === 100;
    },
    dichstarProgress() {
      if (!this.settings) {
        return 0;
      }

      const total = this.settings.progress.dichstar.total_pages;
      const processed = this.settings.progress.dichstar.processed_pages;

      return total ? Math.floor(processed / total * 100) : 0;
    },
    isDichstarOrdersUpdateFinished() {
      return this.dichstarProgress === 100;
    },
    jitaOrdersUpdateText() {
      return `${this.settings.progress.jita.processed_pages || '-' } / ${this.settings.progress.jita.total_pages || '-' } pages`;
    },
    jitaOrdersUpdateIconComponent() {
      if (this.isJitaOrdersUpdateFinished) {
        return CheckIcon;
      }

      return this.isUpdateInProgress ? ThreeDotsIcon : ErrorCircleIcon;
    },
    jitaOrdersUpdateStatusColor() {
      if (this.isJitaOrdersUpdateFinished) {
        return 'light-success';
      }

      return this.isUpdateInProgress ? 'light' : 'light-danger';
    },
    jitaOrdersUpdateIconStatusColor() {
      if (this.isJitaOrdersUpdateFinished) {
        return 'success';
      }

      return this.isUpdateInProgress ? 'info' : 'danger';
    },
    dichstarOrdersUpdateText() {
      return `${this.settings.progress.dichstar.processed_pages || '-' } / ${this.settings.progress.dichstar.total_pages || '-' } pages`;
    },
    dichstarOrdersUpdateIconComponent() {
      if (this.isDichstarOrdersUpdateFinished) {
        return CheckIcon;
      }

      return this.isUpdateInProgress ? ThreeDotsIcon : ErrorCircleIcon;
    },
    dichstarOrdersUpdateStatusColor() {
      if (this.isDichstarOrdersUpdateFinished) {
        return 'light-success';
      }

      return this.isUpdateInProgress ? 'light' : 'light-danger';
    },
    dichstarOrdersUpdateIconStatusColor() {
      if (this.isDichstarOrdersUpdateFinished) {
        return 'success';
      }

      return this.isUpdateInProgress ? 'info' : 'danger';
    },
    pricesUpdateText() {
      if (this.settings.progress.is_prices_updated) {
        return 'Done';
      }

      return this.isUpdateInProgress ? 'In progress' : 'Failed';
    },
    pricesUpdateIconComponent() {
      if (this.settings.progress.is_prices_updated) {
        return CheckIcon;
      }

      return this.isUpdateInProgress ? ThreeDotsIcon : ErrorCircleIcon;
    },
    pricesUpdateStatusColor() {
      if (this.settings.progress.is_prices_updated) {
        return 'light-success';
      }

      return this.isUpdateInProgress ? 'light' : 'light-danger';
    },
    pricesUpdateIconStatusColor() {
      if (this.settings.progress.is_prices_updated) {
        return 'success';
      }

      return this.isUpdateInProgress ? 'info' : 'danger';
    },
    volumesUpdateText() {
      if (this.settings.progress.is_volumes_updated) {
        return 'Done';
      }

      return this.isUpdateInProgress ? 'In progress' : 'Failed';
    },
    volumesUpdateIconComponent() {
      if (this.settings.progress.is_volumes_updated) {
        return CheckIcon;
      }

      return this.isUpdateInProgress ? ThreeDotsIcon : ErrorCircleIcon;
    },
    volumesUpdateStatusColor() {
      if (this.settings.progress.is_volumes_updated) {
        return 'light-success';
      }

      return this.isUpdateInProgress ? 'light' : 'light-danger';
    },
    volumesUpdateIconStatusColor() {
      if (this.settings.progress.is_volumes_updated) {
        return 'success';
      }

      return this.isUpdateInProgress ? 'info' : 'danger';
    },
  },
  methods: {
    async loadSettings() {
      this.settings = await this.$api.loadMarketOrdersUpdateSettings();

      this.isInitialSettingsLoadingDone = true;
    },
    async refreshMarketOrders() {
      if (this.isUpdateInProgress) {
        return;
      }

      await this.$api.refreshMarketOrders();
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
  },
}
</script>

<style scoped>

</style>
