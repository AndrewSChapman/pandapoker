import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from 'vuex-persistedstate';

import { RoomStore } from '@/domains/room/store/roomStore';
import { SettingsStore } from '@/domains/settings/settingsStore';
import { UserStore } from '@/domains/user/store/userStore';

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    room: RoomStore,
    settings: SettingsStore,
    user: UserStore,
  },
  plugins: [createPersistedState()],
});
