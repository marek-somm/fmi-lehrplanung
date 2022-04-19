import rs from '@/services/RequestService.js'
import store from '@/store'

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

	async searchEvent(value, limit, filter="") {
		const payload = makePayload({
			value: value,
			limit: limit,
			filter: filter
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
	},

	async getUserEvents(currentSem) {
		const payload = makePayload({
			user: store.state.User.uid,
			currentSem: currentSem
		})
		return rs.get("user/events", payload);
	},

	async getSubjects() {
		return rs.get("get/subjects");
	},

	async getFieldOfStudies(subject) {
		const payload = makePayload({
			"subject": subject
		})
		return rs.get("get/fieldOfStudies", payload);
	},

	async getCategories(fieldOfStudy) {
		const payload = makePayload({
			"fieldOfStudy": fieldOfStudy
		})
		return rs.get("get/categories", payload);
	},

	async getStudentEvents(filter) {
		const payload = makePayload(filter)
		return rs.get("student/events", payload);
	}
}