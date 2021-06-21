export const UserModule = {
	namespaced: true,
	state: {
		login: false,
		level: 0,
	},
	mutations: {
		SET_LOGIN(state, login) {
			state.login = login
		},
    SET_LEVEL(state, level) {
      state.level = level
    }
	},
	actions: {
		setLogin({ commit }, login) {
			commit('SET_LOGIN', login)
		},
		setLevel({ commit }, level) {
      commit('SET_LEVEL', level)
    }
	},
}
