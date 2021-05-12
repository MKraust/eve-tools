<template>
    <div class="d-flex flex-column flex-root">
        <router-view></router-view>
    </div>
</template>

<script>
export default {

}
</script>

<style lang="scss">
    // 3rd party plugins css
    @import "~bootstrap-vue/dist/bootstrap-vue.css";
    @import "~perfect-scrollbar/css/perfect-scrollbar.css";
    @import "~socicon/css/socicon.css";
    @import "~@fortawesome/fontawesome-free/css/all.css";
    @import "~line-awesome/dist/line-awesome/css/line-awesome.css";
    @import "~sweetalert2/src/sweetalert2.scss";
    @import "metronic/plugins/flaticon/flaticon.css";
    @import "metronic/plugins/flaticon2/flaticon.css";
    @import "metronic/plugins/keenthemes-icons/font/ki.css";
    @import "metronic/sass/style.scss";

    .monospace {
      font-family: Consolas,Monaco, "Andale Mono", "Ubuntu Mono", monospace;
    }
</style>
