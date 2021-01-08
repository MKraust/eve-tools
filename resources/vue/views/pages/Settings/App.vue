<template>
  <mk-page :menu-items="menuItems">
    <div class="container">
      <div class="row">
        <div class="col-6">
          <MarketOrders />
        </div>
      </div>
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
