<template>
  <div class="container">
    <div class="row">
      <div class="col-6">
        <mk-card title="Delivered items" :actions="cardActions">
          <b-table :busy="isLoadingDeliveredItems" :fields="columns" :items="deliveredItems" sort-by="name" :sort-desc="false" :responsive="true">
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
import COLUMNS from './columns';

export default {
  created() {
    this.loadDeliveredItems();
  },
  data: () => ({
    deliveredItemsRawText: '',
    isSaving: false,
    isLoadingDeliveredItems: false,
    deliveredItems: [],
    columns: COLUMNS,
  }),
  computed: {
    cardActions() {
      return [
        { icon: 'fas fa-flag-checkered', handler: this.finishDelivery },
      ];
    },
  },
  methods: {
    async finishDelivery() {
      this.isLoadingDeliveredItems = true;

      await this.$api.finishDelivery();
      await this.loadDeliveredItems();

      this.isLoadingDeliveredItems = false;
    },
    async loadDeliveredItems() {
      this.isLoadingDeliveredItems = true;

      this.deliveredItems = await this.$api.loadDeliveredItems();

      this.isLoadingDeliveredItems = false;
    },
    async save() {
      this.isSaving = true;

      const items = this.deliveredItemsRawText.split('\n').map(itemRow => {
        if (!itemRow.trim()) {
          return null;
        }

        const itemDataParts = itemRow.split('\t');

        return {
          name: itemDataParts[0].replace(/\*$/g, ''),
          quantity: Number(itemDataParts[1].replace(/\W/g, '')),
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
