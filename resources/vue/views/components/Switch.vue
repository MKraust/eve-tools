<template>
  <div class="d-flex align-items-center">
    <div class="mr-2">{{ label }}</div>

    <div>
      <span class="switch" :class="`switch-${size} switch-${type}`">
        <label>
          <input v-model="internalValue" type="checkbox" />
          <span></span>
        </label>
      </span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Switch',
  props: {
    value: {
      type: Boolean,
      requred: true,
    },
    label: {
      type: String,
      required: true,
    },
    size: {
      type: String,
      default() {
        return 'md';
      },
    },
    type: {
      type: String,
      default() {
        return 'primary';
      },
    },
  },
  data() {
    return {
      internalValue: false,
    };
  },
  watch: {
    value: {
      immediate: true,
      handler(val) {
        this.internalValue = val;
      },
    },
    internalValue(val) {
      this.$emit('input', val);
    },
  },
}
</script>
