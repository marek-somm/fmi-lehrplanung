import rs from '@/services/RequestService.js'

function makePayload (params) {
	return {
		params: params
	}
}

export default {
	async getEvent(vnr, semester) {
		const payload = makePayload({
			vnr: vnr,
			semester: semester
		})
		return rs.get("get/event", payload)
	},

	async searchEvent(value, limit) {
		const payload = makePayload({
			value: value,
			limit: limit
		})
		let result = await rs.get("search/event", payload);
		return result
		
	},

	async getModule(modulecode) {
		const payload = makePayload({
			modulecode: modulecode
		})
		return rs.get("get/module", payload)
	},

	async searchModule(value, limit) {
		const payload = makePayload({
			value: value,
			limit: limit
		})
		let result = await rs.get("search/module", payload);
		return result
	},

	async getNewEntries() {
		return rs.get("get/new")
	},

	async searchPerson(value) {
		const payload = makePayload({
			name: value
		})
		return rs.get("search/person", payload);
	}
}