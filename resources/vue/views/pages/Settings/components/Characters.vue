<template>
  <mk-card title="Characters" :loading="loading" icon="fas fa-users" :actions="actions">
    <div v-for="(character, idx) in characters" :key="character.id" class="d-flex align-items-center" :class="{ 'mb-10': idx < characters.length - 1 }">
      <div class="symbol symbol-50 symbol-light-white mr-5">
        <div class="symbol-label overflow-hidden">
          <img :src="getCharacterAvatar(character.id)" class="h-100 align-self-end" :alt="character.name">
        </div>
      </div>

      <div class="d-flex flex-column">
        <div class="font-weight-bold text-dark mb-2 font-size-lg">
          {{ character.name }}
        </div>

        <div class="d-flex checkbox-inline">
          <label v-for="role in roles" :key="role.key" class="checkbox">
            <input type="checkbox" v-model="character.roles[role.key]" @change="(e) => toggleRole(character, role.key, e.target.checked)">
            <span></span>{{ role.name }}
          </label>
        </div>
      </div>
    </div>
  </mk-card>
</template>

<script>
export default {
  data() {
    return {
      characters: window.__characters,
      roles: [
        { key: 'trader', name: 'Trader' },
        { key: 'industrialist', name: 'Industrialist' },
        { key: 'data_source', name: 'Data Source' },
      ],
      actions: [
        { icon: 'fas fa-plus', handler: () => window.location.pathname = '/auth' },
      ]
    };
  },
  methods: {
    getCharacterAvatar(characterId) {
      return `https://images.evetech.net/characters/${characterId}/portrait?size=64`;
    },
    toggleRole(character, roleKey, val) {
      this.$api.toggleCharacterRole(character.id, roleKey, val);
    },
  },
};
</script>
