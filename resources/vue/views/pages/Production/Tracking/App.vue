<template>
  <div class="container">
    <div class="row">
      <div class="col-3">
        <mk-card title="Tracking lists" icon="fas fa-list" :loading="isLoading">
          <div v-if="hasTrackingLists" class="timeline timeline-2">
            <div class="timeline-bar"></div>
            <div v-for="trackingList in trackingLists" :key="trackingList.date" class="timeline-item">
              <div class="timeline-badge" :class="`bg-${getTrackingListStateColor(trackingList)}`"></div>
              <div class="timeline-content d-flex align-items-center justify-content-between">
                <a href="javascript:void(0);" class="text-hover-primary" @click="setActiveTrackingList(trackingList)">{{ trackingList.date }}</a>
              </div>
            </div>
          </div>

          <div v-else class="text-center text-muted py-10">
            No tracking lists
          </div>
        </mk-card>
      </div>
      <div class="col-9">
        <mk-card v-if="activeTrackingList" :title="`List: ${activeTrackingList.date}`" icon="fas fa-eye" :actions="cardActions">
          <div v-for="trackedType in activeTrackingList.types" :key="`${activeTrackingList.date}_${trackedType.id}`">
            <TrackedModule :item="trackedType" @delete="handleDelete(trackedType)" />
          </div>
        </mk-card>
      </div>
    </div>

    <div ref="shoppingListModal" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Modal Title</h5>
            <button type="button" class="close" data-dismiss="modal">
              <i class="ki ki-close"></i>
            </button>
          </div>
          <div class="modal-body">
            <div v-html="shoppingListHtml" data-scroll="true" data-height="500"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import TrackedModule from './components/TrackedModule';

export default {
  name: 'Tracking',
  components: {
    TrackedModule,
  },
  created() {
    this.loadTrackedTypes();
  },
  data: () => ({
    isLoading: false,
    trackedTypes: [],
    activeTrackingListDate: null,
    shoppingList: {},
  }),
  computed: {
    sortedTrackedTypes() {
      return this.$lodash.sortBy(this.trackedTypes, i => `${i.type.tech_level}_${i.type.name}`);
    },
    trackingLists() {
      const groupedTrackedTypes = this.$lodash.groupBy(this.sortedTrackedTypes, i => i.date);
      const trackingLists = [];

      for (let date in groupedTrackedTypes) {
        const types = groupedTrackedTypes[date];
        trackingLists.push({ date, types });
      }

      return this.$lodash.sortBy(trackingLists, i => i.date).reverse();
    },
    hasTrackingLists() {
      return this.trackingLists.length > 0;
    },
    activeTrackingList() {
      return this.trackingLists.find(i => i.date === this.activeTrackingListDate);
    },
    cardActions() {
      return [
        { icon: 'fas fa-shopping-cart', handler: this.showShoppingList },
      ]
    },
    shoppingListHtml() {
      const names = this.$lodash.sortBy(Object.keys(this.shoppingList), i => i);
      const lines = names.map(i => `${i}* ${this.shoppingList[i]}`);

      return lines.join('<br>');
    },
  },
  watch: {
    trackingLists(val) {
      const activeTrackingList = val.find(i => i.date === this.activeTrackingListDate);
      if (!activeTrackingList) {
        this.activeTrackingListDate = val.length > 0 ? val[0].date : null;
      }
    },
  },
  methods: {
    async showShoppingList() {
      this.isLoadingShoppingList = true;

      const trackedTypeIds = this.activeTrackingList.types.map(i => i.id);
      this.shoppingList = await this.$api.loadShoppingList(trackedTypeIds);

      this.isLoadingShoppingList = false;

      $(this.$refs.shoppingListModal).modal();
    },
    async loadTrackedTypes() {
      this.isLoading = true;

      this.trackedTypes = await this.$api.loadTrackedTypes();
      if (this.hasTrackingLists) {
        this.setActiveTrackingList(this.trackingLists[0]);
      }

      this.isLoading = false;
    },
    setActiveTrackingList(trackingList) {
      this.activeTrackingListDate = trackingList.date;
    },
    getTrackingListState(trackingList) {
      const isProductionStarted = trackingList.types.some(type => type.produced > 0);
      if (!isProductionStarted) {
        return 'not_started';
      }

      const isProductionFinished = trackingList.types.some(type => type.produced === type.production_count);

      return isProductionFinished ? 'finished' : 'unfinished';
    },
    getTrackingListStateIcon(trackingList) {
      const state = this.getTrackingListState(trackingList);
      switch (state) {
        case 'not_started':
          return 'times';
        case 'unfinished':
          return 'ellipsis-h';
        case 'finished':
          return 'check';
      }
    },
    getTrackingListStateColor(trackingList) {
      const state = this.getTrackingListState(trackingList);
      switch (state) {
        case 'not_started':
          return 'danger';
        case 'unfinished':
          return 'warning';
        case 'finished':
          return 'success';
      }
    },
    async handleDelete(trackedType) {
      const result = await Swal.fire({
        title: "Delete?",
        text: "Really delete this module from tracking list?",
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: "Delete",
        showCancelButton: true,
        cancelButtonText: "Cancel",
        customClass: {
          confirmButton: "btn btn-danger",
          cancelButton: "btn btn-default"
        }
      });

      if (result.value) {
        await this.$api.deleteTrackedType(trackedType.id);
        const deletedIndex = this.trackedTypes.indexOf(i => i.id === trackedType.id && i.date === trackedType.date);
        this.trackedTypes.splice(deletedIndex, 1);
        this.$forceUpdate();
      }
    },
  }
}
</script>

<style scoped>

</style>
