<template>
    <div class="d-flex flex-row flex-column-fluid page">
        <MainMenu />
        <router-view></router-view>
    </div>
</template>

<script>
import MainMenu from './components/MainMenu';

export default {
    components: {
        MainMenu,
    },
}
</script>

<style lang="less">

</style>
