<template>
  <mk-page :menu-items="menuItems">
    <div class="container">
        <div>
          <Characters />
        </div>
        <div class="mt-8">
          <MarketHistory />
        </div>
        <div class="mt-8">
          <MarketOrders />
        </div>
    </div>
  </mk-page>
</template>

<script>
import MarketOrders from './components/MarketOrders';
import MarketHistory from './components/MarketHistory';
import Characters from './components/Characters';

export default {
  name: "App",
  components: {
    Characters,
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
