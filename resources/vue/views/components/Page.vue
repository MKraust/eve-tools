<template>
  <div id="kt_wrapper" class="d-flex flex-column flex-row-fluid wrapper">
    <div id="kt_header" class="header header-fixed">
      <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
          <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
            <ul class="menu-nav">
              <li v-for="(menuItem, idx) in menuItems" :key="idx" class="menu-item" :class="{ 'menu-item-active': menuItem.route === $route.name }">
                <router-link :to="{ name: menuItem.route }" class="menu-link">
                  <span class="menu-text">{{ menuItem.name }}</span>
                </router-link>
              </li>
            </ul>
          </div>
        </div>
        <div class="topbar align-items-center">
          <b-form-select v-model="currentLocationId">
            <b-form-select-option
              v-for="location in locations"
              :key="location.id"
              :value="location.id"
            >
              {{ location.name }}
            </b-form-select-option>
          </b-form-select>
        </div>
      </div>
    </div>

    <div id="kt_content" class="content d-flex flex-column flex-column-fluid">
      <div :key="key" class="d-flex flex-column-fluid">
        <slot />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Page",
  props: {
    menuItems: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  data: () => ({
    key: 0,
    locations: window.__locations.filter(l => !l.is_trading_hub),
    currentLocationId: localStorage.getItem('current_location_id'),
  }),
  watch: {
    currentLocationId(val) {
      localStorage.setItem('current_location_id', val);
      this.key++;
    },
  },
};
</script>

<style scoped>
.menu-text {
  font-weight: 600 !important;
}
</style>
