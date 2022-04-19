import { createStore } from 'vuex';
import { UserModule } from "./User";

export default createStore({
  state: {
    // local: false, // TODO REMOVE IN PRODUCTION
    // seclevel: 0, // TODO REMOVE IN PRODUCTION
    // uid: "jg01tmp" // TODO REMOVE IN PRODUCTION
    currentSemester: null
  },
  // Verändern STATE
  mutations: {
    SET_CURRENT_SEMESTER(state, currentSemester) {
      state.currentSemester = currentSemester
    }
  },
  // Werden in App aufgerufen, um mutation zu verwenden
  actions: {
    setCurrentSemester({ commit }, currentSemester) {
      commit('SET_CURRENT_SEMESTER', currentSemester)
    }
  },
  modules: {
    User: UserModule
  }
})