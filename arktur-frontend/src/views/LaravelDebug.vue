<template>
	<div>
		<button @click="login">Login</button>
		<button @click="logout">Logout</button>
		<button @click="test">Test</button>
	</div>
</template>

<script>
import { reactive } from '@vue/reactivity'
import AuthService from "@/services/AuthService";

export default {
	setup () {
		const data = reactive({
			name: "go74dir",
			password: "4g8R54536QcPCrhjgFr6"
		})

		async function login() {
			const payload = {
					name: data.name,
					password: data.password,
				};
			let res = await AuthService.login(payload);
			console.log(res);
		}

		async function logout() {
			let res = await AuthService.logout();
			console.log(res);
		}

		async function test() {
			let res = await AuthService.check();
			console.log(res);
		}

		return {
			data,
			login,
			logout,
			test
		}
	}
}
</script>

<style lang="scss" scoped>

</style>