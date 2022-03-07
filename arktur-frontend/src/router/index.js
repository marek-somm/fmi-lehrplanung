import { createRouter, createWebHistory } from 'vue-router';
// import store from '@/store'
import Home from '@/views/Home';
import Login from '@/views/Login';
import Logout from '@/views/Logout';
import Veranstaltungen from '@/views/Veranstaltungen';
import Module from '@/views/Module';
import Export from '@/views/Export';
import NotFound from '@/views/NotFound';
import NewEvent from '@/views/NewEvent';
import EditEvent from '@/views/EditEvent';
import Dashboard from '@/views/Dashboard';
import store from '@/store/index';
import auth from "@/services/AuthService";

const routes = [
	{
		path: '/',
		name: 'Home',
		component: Home,
		beforeEnter: [checkSession, redirectHome]
	},
	{
		path: '/login',
		name: 'Login',
		component: Login,
		beforeEnter: checkAccess,
	},
	{
		path: '/logout',
		name: 'Logout',
		component: Logout,
	},
	{
		path: '/veranstaltungen',
		name: 'Veranstaltungen',
		component: Veranstaltungen,
		beforeEnter: checkAccess
	},
	{
		path: '/module',
		name: 'Module',
		component: Module,
		beforeEnter: checkAccess
	},
	{
		path: '/export',
		name: 'Export',
		component: Export,
		beforeEnter: checkAccess
	},
	{
		path: '/neu/:vnr?/:sem?/:sel?',
		name: 'Neu',
		component: NewEvent,
		beforeEnter: checkAccess,
		props: true
	},
	{
		path: '/bearbeiten/:vnr?/:sem?',
		name: 'Bearbeiten',
		component: EditEvent,
		beforeEnter: checkAccess,
		props: true
	},
	{
		path: '/',
		name: 'Dashboard',
		component: Dashboard,
		beforeEnter: checkAccess
	},
	{
		path: '/:catchAll(.*)',
		name: 'NotFound',
		component: NotFound,
		redirect: Home,
	},
];

async function redirectHome(to, from, next) {
	if (store.state.User.login) {
		next({ name: 'Dashboard' });
	} else {
		next();
	}
}

async function checkAccess(to, from, next) {
	await checkSession(to, from);
	if (to.name == 'Login' || store.state.User.login) {
		next();
	} else {
		next({ name: 'Login' });
	}
}

async function checkSession(to, from) {
	if (!from.name) {
		await setSession();
	}
}

async function setSession() {
	const answer = await auth.check();
	store.dispatch('User/setLogin', answer.data.success);
	store.dispatch('User/setLevel', answer.data.level);
	store.dispatch('User/setUid', answer.data.uid);
}

const router = createRouter({
	history: createWebHistory(process.env.BASE_URL),
	routes,
});

export default router;
