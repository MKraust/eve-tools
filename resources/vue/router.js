import Vue from 'vue';
import Router from 'vue-router';

import Layout from '@/views/layout/Layout';
import ProductionFavorites from '@/views/pages/Production/Favorites/App';

Vue.use(Router);

export default new Router({
  mode: 'history',
  routes: [
    {
      path: "/",
      component: Layout,
      redirect: '/production/favorites',
      children: [
        {
          name: 'dashboard',
          path: '/production/favorites',
          component: ProductionFavorites,
        },
      ],
    },
  ]
});