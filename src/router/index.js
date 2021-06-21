import { createRouter, createWebHistory } from 'vue-router'
// import store from '@/store'
import Home from '@/views/Home'
import Login from '@/views/Login'
import Logout from '@/views/Logout'
import Veranstaltungen from '@/views/Veranstaltungen'
import Module from '@/views/Module'
import NotFound from '@/views/NotFound'
import Instanziieren from '@/views/Instanziieren'
import Bearbeiten from '@/views/Bearbeiten'
import store from '@/store/index'
import { request } from "@/scripts/request.js";

const rq = new request();

const routes = [
	{
		path: '/',
		name: 'Home',
		component: Home,
		beforeEnter: checkSessionAlways,
	},
	{
		path: '/login',
		name: 'Login',
		component: Login,
		beforeEnter: checkSessionAlways,
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
		path: '/:catchAll(.*)',
		name: 'NotFound',
		component: NotFound,
		redirect: Home,
	},
]

async function checkAccess(to, from, next) {
	await checkSession(to, from)
	if (!store.state.User.login && !store.state.debug) next({ name: 'Login' })
	else next()
}

async function checkSession(to, from) {
	if(!from.name) {
		await setSession()
	}
}

async function checkSessionAlways() {
	await setSession()
}

async function setSession() {
	const answer = await rq.session()
	await store.dispatch('User/setLogin', answer.success)
	await store.dispatch('User/setLevel', answer.level)
	console.log(answer.level)
}

const router = createRouter({
	history: createWebHistory(process.env.BASE_URL),
	routes,
})

export default router
