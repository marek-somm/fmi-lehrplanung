import rs from '@/services/RequestService.js';

function makePayload (params) {
	return {
		params: params
	}
}

export default {
	async createEvent(event) {
		return rs.put("add/event", event);
	},

	async saveEvent(event) {
		return rs.put("update/event", event);
	},

	async deleteEvent(event) {
		return rs.put("remove/event", event);
	},

	async setting(key, value) {
		return rs.put("update/setting", {
			"key": key,
			"value": value
		});
	}
};