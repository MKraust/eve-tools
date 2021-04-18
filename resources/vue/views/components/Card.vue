<template>
  <div ref="card" class="card card-custom gutter-b" :class="{ 'card-collapse': isCollapsed, 'card-stretch': stretched }" :id="cardID">
    <div class="card-header">
      <div class="card-title">
        <span v-if="icon" class="card-icon">
          <i class="text-primary" :class="icon"></i>
        </span>
        <h3 class="card-label">{{ title }}</h3>
      </div>
      <div v-if="hasActions" class="card-toolbar">
        <button v-for="(action, idx) in actions" :key="idx" class="btn btn-icon btn-sm btn-hover-light-primary ml-1" @click="action.handler">
          <i :class="action.icon"></i>
        </button>
        <button v-if="collapsable" class="btn btn-icon btn-sm btn-hover-light-primary ml-1" @click="toggleCollapse">
          <i class="ki icon-nm" :class="`ki-arrow-${isCollapsed ? 'up' : 'down'}`"></i>
        </button>
      </div>
    </div>
    <template v-if="!isCollapsed">
      <div class="card-body">
        <div v-if="stretched" class="card-scroll">
          <slot />
        </div>

        <slot v-else />
      </div>
      <div v-if="$slots.hasOwnProperty('footer')" class="card-footer">
        <slot name="footer" />
      </div>
    </template>
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
    collapsable: {
      type: Boolean,
      default() {
        return false;
      },
    },
    collapsed: {
      type: Boolean,
      default() {
        return false;
      },
    },
    stretched: {
      type: Boolean,
      default() {
        return false;
      },
    },
  },
  mounted() {
    if (this.stretched) {
      KTLayoutStretchedCard.init(this.cardID);
    }
  },
  data() {
    return {
      isCollapsed: this.collapsed,
    };
  },
  computed: {
    hasActions() {
      return this.actions.length > 0;
    },
    cardID() {
      return `card_${this._uid}`;
    }
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
  methods: {
    toggleCollapse() {
      this.isCollapsed = !this.isCollapsed;
    },
  },
}
</script>

<style scoped>

</style>
