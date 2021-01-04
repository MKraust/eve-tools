<template>
    <div id="kt_aside" class="aside aside-left aside-fixed d-flex flex-column flex-row-auto">
        <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
            <div id="kt_aside_menu" class="aside-menu my-4 scroll ps ps--active-y" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500" style="height: 245px; overflow: hidden;">
                <ul class="menu-nav">
                    <li class="menu-item menu-item-active">
                        <router-link to="/production/favorites" class="menu-link">
                            <i class="menu-icon fas fa-industry"></i>
                            <span class="menu-text">Production</span>
                        </router-link>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Header"
}
</script>

<style scoped>

</style>
