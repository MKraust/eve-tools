<template>
  <div class="container">
    <mk-card title="Saving delivered items">
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
</template>

<script>
export default {
  data: () => ({
    deliveredItemsRawText: '',
    isSaving: false,
  }),
  methods: {
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
