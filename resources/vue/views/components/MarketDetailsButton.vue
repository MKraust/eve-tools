<template>
  <button class="btn btn-sm btn-icon btn-text-primary btn-hover-light-primary" @click="openMarketDetails">
    <i class="fas fa-external-link-alt"></i>
  </button>
</template>

<script>
export default {
  props: {
    id: Number,
  },
  methods: {
    async openMarketDetails() {
      await this.$api.openMarketDetails(this.id);
    },
  },
};
</script>
