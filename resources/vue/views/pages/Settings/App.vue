<template>
  <mk-page :menu-items="menuItems">
    <div class="container">
        <MarketOrders />
    </div>
  </mk-page>
</template>

<script>
import MarketOrders from './components/MarketOrders';

export default {
  name: "App",
  components: {
    MarketOrders,
  },
  props: {
    menuItems: {
      type: Array,
      default() {
        return [];
      },
    },
  },
}
</script>

<style scoped>

</style>
