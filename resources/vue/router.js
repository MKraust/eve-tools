import Vue from 'vue';
import Router from 'vue-router';

import Layout from '@/views/layout/Layout';
import ProductionRoot from '@/views/pages/Production/Root';
import ProductionFavorites from '@/views/pages/Production/Favorites/App';
import ProductionTracking from '@/views/pages/Production/Tracking/App';

Vue.use(Router);

export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/',
      component: Layout,
      redirect: { name: 'production_favorites' },
      children: [
        {
          path: 'production',
          component: ProductionRoot,
          redirect: '/production/favorites',
          props: {
            menuItems: [
              { name: 'Favorites', route: 'production_favorites' },
              { name: 'Tracking', route: 'production_tracking' },
            ]
          },
          children: [
            {
              name: 'production_favorites',
              path: 'favorites',
              component: ProductionFavorites,
            },
            {
              name: 'production_tracking',
              path: 'tracking',
              component: ProductionTracking,
            },
          ]
        }
      ],
    },
  ]
});
