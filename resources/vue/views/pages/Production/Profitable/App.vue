<template>
  <div class="container">
    <mk-card title="Profitable" :loading="isLoadingFavorites">
      <div class="form-group">
        <div class="input-group">
          <input v-model="filterQuery" type="text" class="form-control" placeholder="Filter items..." />
        </div>
      </div>

      <b-table :fields="tableColumns" :items="filteredItems" sort-by="potential_daily_profit" :sort-desc="true" :responsive="true">
        <template #cell(icon)="data">
          <div class="symbol symbol-30 d-block">
            <span class="symbol-label overflow-hidden">
              <img :src="data.item.icon" class="module-icon" alt="">
            </span>
          </div>
        </template>

        <template #cell(actions)="data">
          <div class="btn btn-hover-light-warning btn-sm btn-icon" @click="toggleFavorite(data.item.type_id)">
            <i class="text-warning fa-star" :class="isFavorite(data.item) ? 'fas' : 'far'"></i>
          </div>
        </template>

        <template #cell(watch_form)="data">
          <div class="d-flex">
            <div v-if="data.item.tech_level === 2" class="form-group mb-0 mr-2">
              <div class="input-group mr-1">
                <div class="input-group-prepend">
              <span class="input-group-text">
                <i class="fas fa-microscope"></i>
              </span>
                </div>
                <input
                  v-model="data.item.invention_count"
                  type="number"
                  class="form-control form-control-sm"
                  placeholder="0"
                  @keyup.enter="trackType(data.item)"
                >
              </div>
            </div>
            <div class="form-group mb-0">
              <div class="input-group input-group-sm mr-1">
                <div class="input-group-prepend">
              <span class="input-group-text">
                <i class="fas fa-industry"></i>
              </span>
                </div>
                <input
                  v-model="data.item.production_count"
                  ref="productionCountInput"
                  type="number"
                  class="form-control form-control-sm"
                  placeholder="0"
                  @keyup.enter="trackType(data.item)"
                >
              </div>
            </div>
          </div>
        </template>
      </b-table>
    </mk-card>
  </div>
</template>

<script>
import COLUMNS from './columns';

export default {
  mounted() {
    this.loadData();
  },
  data: () => ({
    favorites: [],
    items: [],
    isLoadingFavorites: false,
    filterQuery: '',
    tableColumns: COLUMNS,
  }),
  computed: {
    filteredItems() {
      if (this.filterQuery === '') {
        return this.items;
      }

      return this.items.filter(i => i.name.toLowerCase().indexOf(this.filterQuery.toLocaleLowerCase()) !== -1);
    },
  },
  methods: {
    async loadData() {
      this.isLoading = true;

      this.favorites = await this.$api.loadProductionFavorites();
      this.items = await this.$api.loadProductionProfitableItems();

      this.isLoading = false;
    },
    async toggleFavorite(typeId) {
      await this.$api.deleteProductionFavorite(typeId);
      this.favorites = this.favorites.filter(f => f.type_id !== typeId);
    },
    async trackType(item) {
      if (item.production_count === 0 && item.invention_count === 0) {
        this.toggleTrackingForm();
        return;
      }

      await this.$api.addTrackedType(item.type_id, item.production_count || 0, item.invention_count || 0);
      item.production_count = 0;
      item.invention_count = 0;
    },
    isFavorite(module) {
      return Boolean(this.favorites.find(f => f.type_id === module.type_id));
    },
  },
}
</script>

<style scoped>
.module-icon {
  width: 100%;
  height: 100%;
}

.container {
  max-width: 100%;
}
</style>
