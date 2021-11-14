import store from '@/store';
import { useRouter } from 'vue-router';
import rs from '@/services/RequestService.js'

export default {
	async check() {
		return rs.get('check');
	},

	async login(payload) {
		return rs.post('login', payload);
	},

	async logout() {
		const router = useRouter();
		store.dispatch('User/setLogin', false);
		store.dispatch('User/setLevel', 0);
		await rs.post('logout');
		router.push({ name: 'Home' });
	},
};
