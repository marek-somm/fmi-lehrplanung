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
		path: '/api',
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
	base: process.env.BASE_URL,
	history: createWebHistory(),
	routes,
})

export default router
