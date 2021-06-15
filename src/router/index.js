import { createRouter, createWebHistory } from 'vue-router'
// import store from '@/store'
import Home from '@/views/Home'
import Login from '@/views/Login'
import Logout from '@/views/Logout'
import Veranstaltungen from '@/views/Veranstaltungen'
import Module from '@/views/Module'
import NotFound from '@/views/NotFound'
import Instanziieren from '@/views/Instanziieren'
import store from '@/store/index'
import { request } from "@/scripts/request.js";

const rq = new request();

const routes = [
	{
		path: '/',
		name: 'Home',
		component: Home,
		beforeEnter: checkSession,
	},
	{
		path: '/login',
		name: 'Login',
		component: Login,
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
		path: '/:catchAll(.*)',
		name: 'NotFound',
		component: NotFound,
		redirect: Home,
	},
]

async function checkAccess(to, from, next) {
	await checkSession(to, from)
	const user = store.state.User.user
	if (!user && !store.state.debug) next({ name: 'Login' })
	else next()
}

async function checkSession(to, from) {
	if(!from.name)
		store.dispatch('User/setUser', await rq.session())
}

const router = createRouter({
	history: createWebHistory(process.env.BASE_URL),
	routes,
})

export default router
