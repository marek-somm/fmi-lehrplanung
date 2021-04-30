import { createRouter, createWebHistory } from 'vue-router'
import Home from '@/views/Home'
import About from '@/views/About'
import NotFound from '@/views/NotFound'
import ServerTest from '@/views/ServerTest'

const routes = [
	{
		path: '/',
		name: 'Home',
		component: Home,
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

export default router
