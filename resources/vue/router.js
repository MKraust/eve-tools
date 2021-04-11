import Vue from 'vue';
import Router from 'vue-router';

import Layout from '@/views/layout/Layout';

import Settings from '@/views/pages/Settings/App';

import ProductionRoot from '@/views/pages/Production/Root';
import ProductionFavorites from '@/views/pages/Production/Favorites/App';
import ProductionSearch from '@/views/pages/Production/Search/App';
import ProductionProfitable from '@/views/pages/Production/Profitable/App';
import ProductionTracking from '@/views/pages/Production/Tracking/App';

import TradingRoot from '@/views/pages/Trading/Root';
import TradingFavorites from '@/views/pages/Trading/Favorites/App';
import TradingSearch from '@/views/pages/Trading/Search/App';
import TradingProfitable from '@/views/pages/Trading/Profitable/App';
import TradingOrders from '@/views/pages/Trading/Orders/App';
import TradingDelivery from '@/views/pages/Trading/Delivery/App';
import TradingProfit from '@/views/pages/Trading/Profit/App';

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
              { name: 'Search', route: 'production_search' },
              { name: 'Profitable', route: 'production_profitable' },
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
              name: 'production_search',
              path: 'search',
              component: ProductionSearch,
            },
            {
              name: 'production_profitable',
              path: 'profitable',
              component: ProductionProfitable,
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
              { name: 'Search', route: 'trading_search' },
              { name: 'Profitable', route: 'trading_profitable' },
              { name: 'Orders', route: 'trading_orders' },
              { name: 'Delivery', route: 'trading_delivery' },
              { name: 'Profit', route: 'trading_profit' },
            ]
          },
          children: [
            {
              name: 'trading_favorites',
              path: 'favorites',
              component: TradingFavorites,
            },
            {
              name: 'trading_search',
              path: 'search',
              component: TradingSearch,
            },
            {
              name: 'trading_profitable',
              path: 'profitable',
              component: TradingProfitable,
            },
            {
              name: 'trading_orders',
              path: 'orders',
              component: TradingOrders,
            },
            {
              name: 'trading_delivery',
              path: 'delivery',
              component: TradingDelivery,
            },
            {
              name: 'trading_profit',
              path: 'profit',
              component: TradingProfit,
            },
          ]
        }
      ],
    },
  ]
});
