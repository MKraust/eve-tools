<template>
    <div id="kt_aside" class="aside aside-left aside-fixed d-flex flex-column flex-row-auto">
        <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
            <div id="kt_aside_menu" class="aside-menu my-4 scroll ps ps--active-y" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500" style="height: 245px; overflow: hidden;">
                <ul class="menu-nav">
                  <li v-for="(menuItem, idx) in menuItems" :key="idx" class="menu-item" :class="{ 'menu-item-active': $route.name.startsWith(menuItem.routeGroup) }">
                    <router-link :to="menuItem.route" class="menu-link">
                      <i class="menu-icon" :class="menuItem.icon"></i>
                      <span class="menu-text">{{ menuItem.name }}</span>
                    </router-link>
                  </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  name: "Header",
  data: () => ({
    menuItems: [
      { name: 'Production', routeGroup: 'production', route: '/production/favorites', icon: 'fas fa-industry' },
      { name: 'Trading', routeGroup: 'trading', route: '/trading/favorites', icon: 'fas fa-chart-line' },
    ]
  }),
}
</script>

<style scoped>

</style>
