import axios from 'axios';
import store from '@/store'
import { useRouter } from "vue-router";

export const authClient = axios.create({
	baseURL: 'http://localhost:8000', //Local Testing
	//baseURL: 'https://arktur.fmi.uni-jena.de', //Production
	withCredentials: true, // required to handle the CSRF token
});

export function handleError(error) {
	if (error.response.status === 422 || error.response.status === 401) {
		return error.response
	}
	return { error: {
		msg: 'Unhandled Error',
		status: error.response.status,
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
	let token = await csrf();
	if (token.error) return token;
	return authClient
		.post('/api/' + url, payload)
		.catch((error) => handleError(error, errorMsg));
}

export default {
	async check() {
		return get('check');
	},
	async login(payload) {
		//TODO save login in store
		return post('login', payload);
	},
	async logout() {
		const router = useRouter()
		store.dispatch('User/setLogin', false)
		store.dispatch('User/setLevel', 0)
		await post('logout');
      router.push({name: 'Home'});
	},
};
