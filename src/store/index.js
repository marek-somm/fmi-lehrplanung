import { createStore } from 'vuex';
import { UserModule } from "./User";

export default createStore({
  state: {
    local: false, // TODO REMOVE IN PRODUCTION
    seclevel: 0 // TODO REMOVE IN PRODUCTION
  },
  // Verändern STATE
  mutations: {},
  // Werden in App aufgerufen, um mutation zu verwenden
  actions: {},
  modules: {
    User: UserModule
  }
})