<template>
	<div>
		<h1>Login</h1>
	</div>
	<!-- Placeholder sind nur vorübergehend um login zu testen -->
	<form class="login_input" @submit.prevent="loginSubmit()">
		<label :for="data.username">
			<strong>Benutzername:</strong>
		</label>
		<input
			id="username"
			name="username"
			placeholder="admin"
			v-model="data.usernameInput"
		/>
		<label for="password">
			<strong>Passwort:</strong>
		</label>
		<input
			id="password"
			name="password"
			type="password"
			placeholder="1234"
			v-model="data.passwordInput"
		/>
		<br /><br />
		<button>
			<strong>Einloggen</strong>
		</button>
	</form>
</template>
<script>
import store from '@/store'
import { reactive } from 'vue';
import { useRouter } from "vue-router";
import { request } from "@/scripts/request.js";

export default {
	setup() {
		const rq = new request();
		const router = useRouter();

		const data = reactive({
			usernameInput: "",
			passwordInput: "",
			loggedIn: false,
		});
		
		if(store.state.User.user) {
			router.push({name: 'Home'});
		}

		async function loginSubmit() {
			data.loggedIn = await rq.login(data.usernameInput, data.passwordInput)
			if(data.loggedIn){
				store.dispatch('User/setUser', data.loggedIn)
				// TODO richtiges Ziel
				router.push({name: 'Home'});
			}
			else{
				// TODO ausgabe aushalb console
				console.log("Login denied")
			}
			rq.session()
		}

		return {
			data,
			loginSubmit,
		}
	}
};
</script>

<style lang="scss" scoped>
.login_input {
	padding: 2em 1em;
	font-family: helvetica, sans-serif;

	label {
		color: #2c3e50;
		margin: 0 3% 0.25em;
		font-size: 1.2em;
		display: block;
		font-family: helvetica, sans-serif;
	}

	input {
		width: 25%;
		padding: 0.5em 0.25em;
		margin: 0 3% 1em;
		font-size: 1.2em;
		border: 2px solid #000;
		outline: none;
		color: #2c3e50;
	}

	button {
		width: 25%;
		padding: 0.5em 0.25em;
		margin: 0 3% 1em;
		font-size: 1.2em;
		border: 2px solid #000;
		color: #2c3e50;
		transition: box-shadow 0.5s ease, background 0.2s ease;

		&:hover {
			box-shadow: rgba(0, 0, 0, 0.349) 3px 3px;
			background: rgb(201, 201, 201);
		}
	}
}
</style>
