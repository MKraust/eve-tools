import Vue from "vue";
import App from "./App.vue";
import router from "./router";

Vue.config.productionTip = false;

// Global 3rd party plugins
import "popper.js";
import "tooltip.js";

// Vue 3rd party plugins
import lodash from 'lodash';
import "./plugins/perfect-scrollbar";
import "@mdi/font/css/materialdesignicons.css";

Vue.prototype.$lodash = lodash;

new Vue({
  router,
  render: h => h(App)
}).$mount("#app");