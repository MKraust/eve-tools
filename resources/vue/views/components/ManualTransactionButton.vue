<template>
  <div>
    <button :id="buttonId" class="btn btn-sm btn-icon btn-text-primary btn-hover-light-primary" @click="toggleVisibility">
      <i class="fas fa-coins"></i>
    </button>

    <b-popover
      :target="buttonId"
      :show.sync="shown"
      triggers="click blur"
      placement="left"
      custom-class="popover-custom"
    >
      <template #title>
        {{ title }}
      </template>

      <b-form inline @keyup.enter="save">
        <b-input-group prepend="Price" size="sm" class="mr-2" style="width: 180px;">
          <b-form-input v-model.number="price" type="number" size="sm" :min="0" autofocus />
        </b-input-group>

        <b-input-group prepend="Qty" size="sm" class="mr-2" style="width: 100px;">
          <b-form-input v-model.number="quantity" type="number" size="sm" :min="0" />
        </b-input-group>

        <b-form-select v-model="locationId" size="sm" class="mr-2">
          <b-form-select-option
            v-for="location in locations"
            :key="location.id"
            :value="location.id"
          >
            {{ location.name }}
          </b-form-select-option>
        </b-form-select>

        <b-button variant="primary" size="sm" @click="save">Save</b-button>
      </b-form>
    </b-popover>
  </div>
</template>

<script>
export default {
  props: {
    id: Number,
    icon: String,
    title: String,
  },
  data() {
    return {
      locations: window.__locations,
      shown: false,
      price: 0,
      quantity: 0,
      locationId: window.__locations[0].id,
    };
  },
  computed: {
    buttonId() {
      return `history-button-${this.id}`;
    },
    defaultLocationId() {
      return this.locations[0].id;
    },
  },
  methods: {
    toggleVisibility() {
      this.shown = !this.shown;
    },
    async save() {
      if (this.price <= 0 || this.quantity <= 0) {
        return;
      }

      await this.$api.createManualBuyTransaction(this.id, this.price, this.quantity, this.locationId);

      this.toggleVisibility();

      this.price = 0;
      this.quantity = 0;
      this.locationId = this.defaultLocationId;
    },
  },
};
</script>

<style scoped>
.popover-custom {
  max-width: 600px;
}
</style>
