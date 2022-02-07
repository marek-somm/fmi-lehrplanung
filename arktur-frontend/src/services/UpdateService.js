import rs from '@/services/RequestService.js'

export default {
	async createEvent(event) {
		return rs.put("add/event", event)
	},

	async saveEvent(event) {
		return rs.put("update/event", event)
	},

	async deleteEvent(event) {
		return rs.put("remove/event", event)
	}
}