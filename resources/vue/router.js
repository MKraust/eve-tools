import Vue from 'vue';
import Router from 'vue-router';

import Layout from '@/views/layout/Layout';

import Settings from '@/views/pages/Settings/App';

import ProductionRoot from '@/views/pages/Production/Root';
import ProductionFavorites from '@/views/pages/Production/Favorites/App';
import ProductionTracking from '@/views/pages/Production/Tracking/App';

import TradingRoot from '@/views/pages/Trading/Root';
import TradingFavorites from '@/views/pages/Trading/Favorites/App';
import TradingFavoritesNew from '@/views/pages/Trading/FavoritesNew/App';

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
          name: 'settings',
          path: 'settings',
          component: Settings,
          props: {
            menuItems: [
              { name: 'General', route: 'settings' },
            ]
          },
        },
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
        },
        {
          path: 'trading',
          component: TradingRoot,
          redirect: '/trading/favorites',
          props: {
            menuItems: [
              { name: 'Favorites', route: 'trading_favorites' },
              { name: 'Favorites (new)', route: 'trading_favorites_new' },
            ]
          },
          children: [
            {
              name: 'trading_favorites',
              path: 'favorites',
              component: TradingFavorites,
            },
            {
              name: 'trading_favorites_new',
              path: 'favorites_new',
              component: TradingFavoritesNew,
            },
          ]
        }
      ],
    },
  ]
});
