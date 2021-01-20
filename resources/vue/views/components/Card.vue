<template>
  <div ref="card" class="card card-custom gutter-b">
    <div class="card-header border-0">
      <div class="card-title">
        <span v-if="icon" class="card-icon">
          <i class="text-primary" :class="icon"></i>
        </span>
        <h3 class="card-label">{{ title }}</h3>
      </div>
      <div v-if="hasActions" class="card-toolbar">
        <button v-for="(action, idx) in actions" :key="idx" class="btn btn-icon btn-sm btn-hover-light-primary" @click="action.handler">
          <i :class="action.icon"></i>
        </button>
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
        return null;
      },
    },
    loading: {
      type: Boolean,
      default() {
        return false;
      },
    },
    actions: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  data: () => ({

  }),
  computed: {
    hasActions() {
      return this.actions.length > 0;
    },
  },
  watch: {
    loading: {
      immediate: true,
      handler(val) {
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
  },
}
</script>

<style scoped>

</style>
