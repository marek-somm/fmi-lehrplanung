import axios from 'axios';

const client = axios.create({
	//** Local Testing */
	baseURL: 'http://localhost:8000',
	//**  Production */
	// baseURL: 'https://arktur.fmi.uni-jena.de',
	withCredentials: true, // required to handle the CSRF token
});

function handleError(error, errorMsg = null) {
	let status = 404;
	if (error.response) {
		if (error.response.status === 422 || error.response.status === 401) {
			return error.response;
		} else {
			status = error.response.status;
		}
	}
	return {
		data: {
			errors: {
				value: status,
			},
			message: errorMsg ? errorMsg : 'Unhandled Error',
		},
	};
}

export default {
	async csrf() {
		return client
			.get('/sanctum/csrf-cookie')
			.catch((error) => handleError(error));
	},

	async get(url, payload, errorMsg = null) {
		return client
			.get('/api/' + url, payload)
			.catch((error) => handleError(error, errorMsg));
	},

	async post(url, payload, errorMsg = null) {
		let token = await this.csrf();
		if (token.error) return token;
		return client
			.post('/api/' + url, payload)
			.catch((error) => handleError(error, errorMsg));
	},

	async put(url, payload, errorMsg = null) {
		client
			.put('/api/' + url, payload)
			.catch((error) => handleError(error, errorMsg));
	},
};
