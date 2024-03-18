import { createRouter, createWebHistory } from 'vue-router';
// import store from '@/store'
import Home from '@/views/Home';
import Login from '@/views/Login';
import Logout from '@/views/Logout';
import Veranstaltungen from '@/views/Veranstaltungen';
import Module from '@/views/Module';
import Export from '@/views/Export';
import Settings from '@/views/Settings';
import NotFound from '@/views/NotFound';
import NewEvent from '@/views/NewEvent';
import EditEvent from '@/views/EditEvent';
import Dashboard from '@/views/Dashboard';
import Overview from '@/views/Overview.vue';
import store from '@/store/index';
import auth from "@/services/AuthService";

const routes = [
	{
		path: '/',
		name: 'Home',
		component: Home,
		beforeEnter: [checkSession, redirectHome],
		meta: {
			title: "Lehre"
		 }
	},
	{
		path: '/login',
		name: 'Login',
		component: Login,
		beforeEnter: checkAccess,
		meta: {
			title: "Login | Lehre"
		}
	},
	{
		path: '/logout',
		name: 'Logout',
		component: Logout,
		meta: {
			title: "Logout | Lehre"
		}
	},
	{
		path: '/veranstaltungen',
		name: 'Veranstaltungen',
		component: Veranstaltungen,
		beforeEnter: checkAccess,
		meta: {
			title: "Veranstaltungen | Lehre"
		}
	},
	{
		path: '/module',
		name: 'Module',
		component: Module,
		beforeEnter: checkAccess,
		meta: {
			title: "Module | Lehre"
		}
	},
	{
		path: '/export',
		name: 'Export',
		component: Export,
		beforeEnter: checkAccess,
		meta: {
			title: "Export | Lehre"
		}
	},
	{
		path: '/settings',
		name: 'Settings',
		component: Settings,
		beforeEnter: checkAccess,
		meta: {
			title: "Einstellungen | Lehre"
		}
	},
	{
		path: '/neu/:vnr?/:sem?/:sel?',
		name: 'Neu',
		component: NewEvent,
		beforeEnter: checkAccess,
		props: true,
		meta: {
			title: "Neu | Lehre"
		}
	},
	{
		path: '/bearbeiten/:vnr?/:sem?',
		name: 'Bearbeiten',
		component: EditEvent,
		beforeEnter: checkAccess,
		props: true,
		meta: {
			title: "Bearbeiten | Lehre"
		}
	},
	{
		path: '/',
		name: 'Dashboard',
		component: Dashboard,
		beforeEnter: checkAccess,
		meta: {
			title: "Dashboard | Lehre"
		}
	},
	{
		path: '/overview',
		name: 'Overview',
		component: Overview,
		beforeEnter: checkSession,
		meta: {
			title: "Overview | Lehre"
		}
	},
	{
		path: '/:catchAll(.*)',
		name: 'NotFound',
		component: NotFound,
		redirect: Home,
		meta: {
			title: "Not Found | Lehre"
		}
	}
];

async function redirectHome(to, from, next) {
	if (store.state.User.login) {
		if (store.state.User.level >= 2) {
			next({ name: 'Export' });
		} else {
			next({ name: 'Dashboard' });
		}
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
	store.dispatch('setCurrentSemester', answer.data.currentSemester);
}

const router = createRouter({
	history: createWebHistory(process.env.BASE_URL),
	routes,
});

router.afterEach((to, from) => {
	document.title = to.meta.title;
 })

export default router;
