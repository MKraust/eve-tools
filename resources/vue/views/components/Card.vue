<template>
  <div ref="card" class="card card-custom gutter-b">
    <div class="card-header">
      <div class="card-title">
        <span class="card-icon">
          <i class="text-primary" :class="icon"></i>
        </span>
        <h3 class="card-label">{{ title }}</h3>
      </div>
    </div>
    <div class="card-body">
      <slot />
    </div>
  </div>
</template>

<script>
export default {
  name: "Card",
  props: {
    title: {
      type: String,
      default() {
        return '';
      },
    },
    icon: {
      type: String,
      default() {
        return '';
      },
    },
    loading: {
      type: Boolean,
      default() {
        return false;
      },
    },
  },
  data: () => ({

  }),
  watch: {
    loading(val) {
      if (val) {
        KTApp.block(this.$refs.card, {
          overlayColor: '#ffffff',
          type: 'loader',
          state: 'primary',
          opacity: 0.4,
          size: 'lg'
        });
      } else {
        KTApp.unblock(this.$refs.card);
      }
    },
  },
}
</script>

<style scoped>

</style>
