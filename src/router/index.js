import { createRouter, createWebHistory } from 'vue-router'
// import store from '@/store'
import Home from '@/views/Home'
import Login from '@/views/Login'
import Logout from '@/views/Logout'
import About from '@/views/About'
import NotFound from '@/views/NotFound'
import ServerTest from '@/views/ServerTest'
import Instanziieren from '@/views/Instanziieren'

const routes = [
	{
		path: '/',
		name: 'Home',
		component: Home,
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
		redirect: Home,
	},
	{
		path: '/about',
		name: 'About',
		component: About,
	},
	{
		path: '/server',
		name: 'ServerTest',
		component: ServerTest,
	},
	{
		path: '/instanziieren/:id',
		name: 'Instanziieren',
		component: Instanziieren,
	},
	{
		path: '/:catchAll(.*)',
		name: 'NotFound',
		component: NotFound,
		redirect: Home,
	},
]

const router = createRouter({
	history: createWebHistory(process.env.BASE_URL),
	routes,
})

// router.beforeEach(async (to, from, next) => {
// 	const user = store.state.User.user;
// 	next()
// })

export default router
