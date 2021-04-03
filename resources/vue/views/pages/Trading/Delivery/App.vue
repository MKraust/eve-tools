<template>
  <div class="container">
    <div class="row">
      <div class="col-6">
        <div class="d-flex flex-column">
          <div class="mb-5">
            <mk-card
              v-for="delivery in deliveries"
              :key="delivery.id"
              :title="getDeliveryVolume(delivery)"
              :actions="[
                { icon: 'fas fa-flag-checkered', handler: () => finishDelivery(delivery) },
              ]"
              collapsable
              collapsed
            >
              <b-table :busy="isLoadingDeliveredItems" :fields="columns" :items="delivery.items" sort-by="name" :sort-desc="false" :responsive="true">
                <template #table-busy>
                  <div class="text-center text-primary my-2">
                    <b-spinner class="align-middle mr-2"></b-spinner>
                    <strong>Loading...</strong>
                  </div>
                </template>

                <template #cell(icon)="data">
                  <div class="symbol symbol-30 d-block">
                <span class="symbol-label overflow-hidden">
                  <img :src="data.item.icon" class="module-icon" alt="">
                </span>
                  </div>
                </template>

                <template #cell(actions)="data">
                  <mk-money-flow-button :id="data.item.type_id" :name="data.item.name" />
                  <mk-market-details-button :id="data.item.type_id" />
                </template>
              </b-table>
            </mk-card>
          </div>
        </div>
      </div>
      <div class="col-6">
        <mk-card title="Save delivered items">
          <b-form-textarea
            v-model="deliveredItemsRawText"
            :disabled="isSaving"
            placeholder="Paste copied items here..."
            rows="20"
          />

          <template #footer>
            <div class="d-flex align-items-center">
              <button :disabled="isSaving" class="btn btn-light-primary font-weight-bold" @click="save">Save</button>
            </div>
          </template>
        </mk-card>
      </div>
    </div>
  </div>
</template>

<script>
import { confirm, formatNumber } from '@/helper';

import COLUMNS from './columns';

export default {
  created() {
    this.loadDeliveries();
  },
  data: () => ({
    deliveredItemsRawText: '',
    isSaving: false,
    isLoadingDeliveredItems: false,
    deliveries: [],
    columns: COLUMNS,
  }),
  methods: {
    getDeliveryVolume(delivery) {
      return `${formatNumber(delivery.items.reduce((sum, i) => i.volume + sum, 0))} m³`;
    },
    async finishDelivery(delivery) {
      const confirmed = await confirm('Finish?', 'Really finish this delivery?', 'Finish');
      if (!confirmed) {
        return;
      }

      this.isLoadingDeliveredItems = true;

      await this.$api.finishDelivery(delivery.id);
      await this.loadDeliveries();

      this.isLoadingDeliveredItems = false;
    },
    async loadDeliveries() {
      this.isLoadingDeliveredItems = true;

      this.deliveries = await this.$api.loadDeliveredItems();

      this.isLoadingDeliveredItems = false;
    },
    async save() {
      this.isSaving = true;

      const items = this.deliveredItemsRawText.split('\n').map(itemRow => {
        if (!itemRow.trim()) {
          return null;
        }

        const itemDataParts = itemRow.split('\t');
        const volumePart = itemDataParts.find(i => i.includes('m3'));
        const volume = Number((volumePart || '0').replace('m3', '').replace(',', '.').replace(/\W/g, ''));

        return {
          name: itemDataParts[0].replace(/\*$/g, ''),
          quantity: Number(itemDataParts[1].replace(/\W/g, '')),
          volume,
        };
      }).filter(i => i);

      await this.$api.saveDeliveredItems(items);

      this.deliveredItemsRawText = '';

      this.isSaving = false;
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
