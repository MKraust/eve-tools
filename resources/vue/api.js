import axios from 'axios';

const BACKEND_API = `${window.location.protocol}//${window.location.host}/api`;

function api(route) {
  return BACKEND_API + route;
}

export default {
  async searchRigs(query) {
    const params = { search_query: query };

    return (await axios.get(api('/production/modules/search'), { params })).data;
  },
  async loadProductionFavorites() {
    return (await axios.get(api('/production/favorites/list'))).data.map(i => Object.assign(i, { production_count: 0, invention_count: 0 }));
  },
  async loadProductionProfitableItems() {
    return (await axios.get(api('/production/profitable/list'))).data.map(i => Object.assign(i, { production_count: 0, invention_count: 0 }));
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
    return (await axios.get(api('/production/tracked/list'))).data;
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
    const params = { search_query: query };

    return (await axios.get(api('/trading/modules/search'), { params })).data;
  },
  async loadTradingFavorites() {
    return (await axios.get(api('/trading/favorites/list'))).data.map(i => Object.assign(i, { quantity: 0 }));
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
  async refreshMarketOrders() {
    return (await axios.post(api('/settings/refresh-market-orders'))).data;
  },
  async loadTradingProfitableItems() {
    return (await axios.get(api('/trading/profitable/list'))).data.map(i => Object.assign(i, { quantity: 0 }));
  },
  async loadTradingOrders() {
    return (await axios.get(api('/trading/orders/list'))).data;
  },
  async openMarketDetails(typeId) {
    const params = { type_id: typeId };

    await axios.post(api('/trading/open-market-details'), params);
  },
}
