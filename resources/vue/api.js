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
  async addFavorite(id) {
    const params = { type_id: id };
    return (await axios.post(api('/production/favorites/add'), params)).data
  },
  async deleteFavorite(id) {
    const params = { type_id: id };
    await axios.post(api('/production/favorites/delete'), params);
  }
}
