import axios from 'axios';

export const authClient = axios.create({
	baseURL: 'http://localhost:8000',
	withCredentials: true, // required to handle the CSRF token
});

export function handleError(error, errorMsg = 'Unauthorized Access') {
	console.log(error);
	let msg = 'Unknown Error';
	let status = 419;
	if (error.response) {
		if (error.response.status === 401) {
			msg = errorMsg;
			status = 401;
		} else if(error.response.status === 422) {
			msg = 'Wrong Format'
			status = 422;
		}
	} else if (error.request) {
		msg = 'Server Error \n\n Please retry in a few seconds';
		status = 0;
	}
	return { error: {
		msg: msg,
		status: status,
	}};
}

async function csrf() {
	return authClient
		.get('/sanctum/csrf-cookie')
		.catch((error) => handleError(error));
}

async function get(url, payload) {
	return authClient
		.get('/api/' + url, payload)
		.catch((error) => handleError(error));
}

async function post(url, payload, errorMsg = null) {
	let token = csrf();
	if (token.error) return token;
	return authClient
		.post('/api/' + url, payload)
		.catch((error) => handleError(error, errorMsg));
}

export default {
	async test() {
		return get('test');
	},
	async login(payload) {
		//TODO save login in store
		return post('login', payload, 'Wrong Credentials');
	},
	async logout() {
		return post('logout');
	},

	async forgotPassword(payload) {
		await csrf();
		return post('/forgot-password', payload);
	},
	getAuthUser() {
		return authClient.get('/api/users/auth');
	},
	async resetPassword(payload) {
		await authClient.get('/sanctum/csrf-cookie');
		return authClient.post('/reset-password', payload);
	},
	updatePassword(payload) {
		return authClient.put('/user/password', payload);
	},
	async registerUser(payload) {
		await authClient.get('/sanctum/csrf-cookie');
		return authClient.post('/register', payload);
	},
	sendVerification(payload) {
		return authClient.post('/email/verification-notification', payload);
	},
	updateUser(payload) {
		return authClient.put('/user/profile-information', payload);
	},
};
