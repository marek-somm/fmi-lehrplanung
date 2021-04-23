import { createRouter, createWebHistory } from 'vue-router'
import Home from '@/views/Home'
import About from '@/views/About'
import NotFound from '@/views/NotFound'
import ServerTest from "@/views/ServerTest"

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
		component: ServerTest
	},
	{
		path: '/:pathMatch(.*)*',
		name: 'not-found',
		component: NotFound,
		redirect: Home,
	},
	{
		path: '/:pathMatch(.*)',
		name: 'bad-not-found',
		component: NotFound,
		redirect: Home,
	},
]

const router = createRouter({
	history: createWebHistory(),
	routes,
})

export default router
