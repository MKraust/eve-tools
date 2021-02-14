import axios from 'axios';

const BACKEND_API = `${window.location.protocol}//${window.location.host}/api`;

const DEFAULT_CHARACTER_ID = 2117638152;

function api(route) {
  return BACKEND_API + route;
}

function getLocationId() {
  return localStorage.getItem('current_location_id');
}

export default {
  async getLocations() {
    return (await axios.get(api('/settings/location/list'))).data;
  },

  async searchRigs(query) {
    const params = { search_query: query, location_id: getLocationId() };

    return (await axios.get(api('/production/modules/search'), { params })).data;
  },

  async loadProductionFavorites() {
    const params = { location_id: getLocationId() };

    return (await axios.get(api('/production/favorites/list'), { params })).data.map(i => Object.assign(i, { production_count: 0, invention_count: 0 }));
  },

  async loadProductionProfitableItems() {
    const params = { location_id: getLocationId() };

    return (await axios.get(api('/production/profitable/list'), { params })).data.map(i => Object.assign(i, { production_count: 0, invention_count: 0 }));
  },

  async addProductionFavorite(typeId) {
    const params = { type_id: typeId };

    return (await axios.post(api('/production/favorites/add'), params)).data
  },

  async deleteProductionFavorite(typeId) {
    const params = { type_id: typeId };

    await axios.post(api('/production/favorites/delete'), params);
  },

  async loadTrackedTypes() {
    const params = { location_id: getLocationId() };

    return (await axios.get(api('/production/tracked/list'), { params })).data;
  },

  async addTrackedType(typeId, productionCount, inventionCount) {
    const params = {
      type_id: typeId,
      production_count: Number(productionCount),
      invention_count: Number(inventionCount),
    };

    await axios.post(api('/production/tracked/add'), params);
  },

  async deleteTrackedType(id) {
    await axios.post(api('/production/tracked/delete'), { id });
  },

  async editTrackedItem(id, productionCount, inventionCount) {
    const params = { id, production_count: productionCount, invention_count: inventionCount };
    await axios.post(api('/production/tracked/edit'), params);
  },

  async updateTrackedItemDoneCounts(id, produced, invented) {
    await axios.post(api('/production/tracked/update-done-counts'), { id, produced, invented });
  },

  async loadShoppingList(trackedTypeIds) {
    const params = { tracked_type_ids: trackedTypeIds };

    return (await axios.get(api('/production/tracked/shopping-list'), { params })).data;
  },

  async searchTypes(query) {
    const params = { search_query: query, location_id: getLocationId() };

    return (await axios.get(api('/trading/modules/search'), { params })).data;
  },

  async loadTradingFavorites() {
    const params = { location_id: getLocationId() };

    return (await axios.get(api('/trading/favorites/list'), { params })).data.map(i => Object.assign(i, { quantity: 0 }));
  },

  async addTradingFavorite(typeId) {
    const params = { type_id: typeId };

    return (await axios.post(api('/trading/favorites/add'), params)).data
  },

  async deleteTradingFavorite(typeId) {
    const params = { type_id: typeId };

    await axios.post(api('/trading/favorites/delete'), params);
  },

  async loadMarketOrdersUpdateSettings() {
    return (await axios.get(api('/settings/market-orders-update-info'))).data;
  },

  async loadMarketHistoryUpdateInfo() {
    return (await axios.get(api('/settings/market-history-update-info'))).data;
  },

  async refreshMarketData() {
    return (await axios.post(api('/settings/refresh-market-data'))).data;
  },

  async refreshMarketHistory() {
    return (await axios.post(api('/settings/refresh-market-history'))).data;
  },

  async loadTradingProfitableItems() {
    const params = { location_id: getLocationId() };

    return (await axios.get(api('/trading/profitable/list'), { params })).data.map(i => Object.assign(i, { quantity: 0 }));
  },

  async loadTradingOrders() {
    const params = { location_id: getLocationId(), character_id: DEFAULT_CHARACTER_ID };

    return (await axios.get(api('/trading/orders/list'), { params })).data;
  },

  async openMarketDetails(typeId) {
    const params = { type_id: typeId };

    await axios.post(api('/trading/open-market-details'), params);
  },

  async loadIntradayMoneyFlowData(typeId) {
    const params = { type_id: typeId };

    return (await axios.get(api('/trading/stats-by-half-hour'), { params })).data
  },
};
