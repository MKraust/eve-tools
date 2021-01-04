<template>
  <div class="d-flex align-items-center">
    <div class="symbol symbol-50 mr-5">
      <span class="symbol-label">
        <img :src="item.icon" class="module-icon" :alt="item.name">
      </span>
    </div>
    <div class="d-flex flex-column flex-grow-1">
      <span class="font-size-h6 text-dark-75 font-weight-bold">{{ item.name }}</span>
    </div>

    <div class="btn btn-hover-light-primary btn-sm btn-icon" @click="toggleTrackingForm">
      <i class="text-primary fas fa-eye"></i>
    </div>
    <div class="btn btn-hover-light-warning btn-sm btn-icon" @click="toggleFavorite">
      <i class="text-warning fa-star" :class="favorite ? 'fas' : 'far'"></i>
    </div>
  </div>
</template>

<script>
export default {
  name: "Module",
  props: {
    trackButtonShown: {
      type: Boolean,
      default() {
        return false;
      },
    },
    item: {
      required: true,
      type: Object,
    },
    favorite: {
      type: Boolean,
      default() {
        return false;
      }
    },
  },
  data: () => ({
    isTrackingFormShown: false,
  }),
  methods: {
    toggleFavorite() {
      this.$emit('toggle-favorite');
    },
    toggleTrackingForm() {
      this.isTrackingFormShown = !this.isTrackingFormShown;
    },
  },
}
</script>

<style scoped>
.module-icon {
  width: 44px;
  height: 44px;
}
</style>
