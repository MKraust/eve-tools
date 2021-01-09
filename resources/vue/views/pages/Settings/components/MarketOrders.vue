<template>
  <mk-card title="Market orders" :loading="!isInitialSettingsLoadingDone" icon="fas fa-chart-line">
    <div class="d-flex mb-5">
      <button class="btn btn-primary mr-5" :class="{ disabled: isUpdateInProgress }" @click="refreshMarketOrders">Refresh</button>
      <div v-if="settings !== null" class="info d-flex">
        <div class="d-flex flex-column mr-2">
          <p>Start date:</p>
          <p>End date:</p>
        </div>
        <div class="d-flex flex-column mr-3">
          <p>{{ settings.start_date }}</p>
          <p>{{ settings.end_date || '-' }}</p>
        </div>
        <div class="d-flex flex-column mr-2">
          <p>Dichstar:</p>
          <p>Jita:</p>
        </div>
        <div class="d-flex flex-column">
          <p>{{ settings.progress.dichstar.processed_pages || '-' }} / {{ settings.progress.dichstar.total_pages || '-' }}</p>
          <p>{{ settings.progress.jita.processed_pages || '-' }} / {{ settings.progress.jita.total_pages || '-' }}</p>
        </div>
      </div>
    </div>
    <div class="progress mb-2" style="height: 20px;">
      <div class="progress-bar" :style="{ width: `${dichstarProgress}%` }"></div>
    </div>
    <div class="progress mb-2" style="height: 20px;">
      <div class="progress-bar" :style="{ width: `${jitaProgress}%` }"></div>
    </div>
  </mk-card>
</template>

<script>
export default {
  name: "MarketOrders",
  created() {
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
    isUpdateInProgress() {
      return this.settings && this.settings.status === 'in_progress';
    },
    jitaProgress() {
      if (!this.settings) {
        return 0;
      }

      const total = this.settings.progress.jita.total_pages;
      const processed = this.settings.progress.jita.processed_pages;

      return total ? Math.floor(processed / total * 100) : 0;
    },
    dichstarProgress() {
      if (!this.settings) {
        return 0;
      }

      const total = this.settings.progress.dichstar.total_pages;
      const processed = this.settings.progress.dichstar.processed_pages;

      return total ? Math.floor(processed / total * 100) : 0;
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
      }, 500);
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
