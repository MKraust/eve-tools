import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import api from "./api";
import './components';

Vue.config.productionTip = false;

import './metronic';

// Vue 3rd party plugins
import lodash from 'lodash';
import "./plugins/perfect-scrollbar";
import "./plugins/inline-svg";
import "./plugins/bootstrap-vue";
import "@mdi/font/css/materialdesignicons.css";

Vue.prototype.$lodash = lodash;
Vue.prototype.$api = api;

new Vue({
  router,
  render: h => h(App)
}).$mount("#app");
