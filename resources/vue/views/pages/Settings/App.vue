<template>
  <mk-page :menu-items="menuItems">
    <div class="container">
        <div>
          <MarketOrders />
        </div>
        <div class="mt-8">
          <MarketHistory />
        </div>
    </div>
  </mk-page>
</template>

<script>
import MarketOrders from './components/MarketOrders';
import MarketHistory from './components/MarketHistory';

export default {
  name: "App",
  components: {
    MarketOrders,
    MarketHistory,
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
