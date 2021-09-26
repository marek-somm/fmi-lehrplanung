import { createRouter, createWebHistory } from 'vue-router'
// import store from '@/store'
import Home from '@/views/Home'
import Login from '@/views/Login'
import Logout from '@/views/Logout'
import Veranstaltungen from '@/views/Veranstaltungen'
import Module from '@/views/Module'
import Export from '@/views/Export'
import NotFound from '@/views/NotFound'
import Instanziieren from '@/views/Instanziieren'
import Bearbeiten from '@/views/Bearbeiten'
import LaravelDebug from '@/views/LaravelDebug'
import store from '@/store/index'
import AuthService from "@/scripts/AuthService";

const routes = [
	{
		path: '/',
		name: 'Home',
		component: Home,
		beforeEnter: checkAccess
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
		path: '/instanziieren/:id/:sem',
		name: 'Instanziieren',
		component: Instanziieren,
		beforeEnter: checkAccess
	},
	{
		path: '/bearbeiten/:id/:sem',
		name: 'Bearbeiten',
		component: Bearbeiten,
		beforeEnter: checkAccess
	},
	{
		path: '/beta',
		name: 'Beta',
		component: LaravelDebug,
	},
	{
		path: '/:catchAll(.*)',
		name: 'NotFound',
		component: NotFound,
		redirect: Home,
	},
]

async function checkAccess(to, from, next) {
	await checkSession(to, from)
	if(from.name != 'Login')
		next();
	else if (!store.state.User.login) next({ name: 'Login' })
	else next()
}

async function checkSession(to, from) {
	if(!from.name) {
		await setSession()
	}
}

async function setSession() {
	const answer = await AuthService.check()
	store.dispatch('User/setLogin', answer.data.success)
	store.dispatch('User/setLevel', answer.data.level)
	store.dispatch('User/setUid', answer.data.uid)
}

const router = createRouter({
	history: createWebHistory(process.env.BASE_URL),
	routes,
})

export default router
