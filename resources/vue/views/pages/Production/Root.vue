<template>
  <mk-page :menu-items="menuItems">
    <router-view></router-view>
  </mk-page>
</template>

<script>
export default {
  name: "Root",
  props: {
    menuItems: {
      type: Array,
      default() {
        return [];
      },
    },
  },
};
</script>

<style scoped>

</style>
