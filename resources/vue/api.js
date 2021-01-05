import axios from 'axios';

const BACKEND_API = `${window.location.protocol}//${window.location.host}/api`;

function api(route) {
  return BACKEND_API + route;
}

export default {
  async searchModules(query) {
    const params = { search_query: query };

    return (await axios.get(api('/production/modules/search'), { params })).data;
  },
  async loadFavorites() {
    return (await axios.get(api('/production/favorites/list'))).data;
  },
  async addFavorite(typeId) {
    const params = { type_id: typeId };

    return (await axios.post(api('/production/favorites/add'), params)).data
  },
  async deleteFavorite(typeId) {
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
}
