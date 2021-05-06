<template>
	<div>
		<h1>Login</h1>
	</div>
<!-- Placeholder sind nur vorübergehend um login zu testen -->
	<form class="login_input" @submit.prevent="loginSubmit">
		<label for="username">
			<strong>Benutzername:</strong>
		</label>
		<input id="username" name="username" placeholder="admin" v-model="usernameInput" />
		<label for="password">
			<strong>Passwort:</strong>
		</label>
		<input
			id="password"
			name="password"
			type="password"
			placeholder="1234"
			v-model="passwordInput"
		/>
		<br /><br />
		<button>
			<strong>Einloggen</strong>
		</button>
	</form>
</template>
<script>
import axios from "axios";
import store from '@/store'

export default {
	data(){
		return {
			usernameInput: '',
			passwordInput: '',
			loading: false,
			error: null,
			loggedIn: false
		}
	},
	methods:{
		async loginSubmit(){
			console.log(this.usernameInput)
			console.log(this.passwordInput)
			await this.login(this.usernameInput, this.passwordInput)
			console.log(this.loggedIn)
			if(this.loggedIn){
				store.dispatch('User/setUser', this.loggedIn)
				// TODO richtiges Ziel
				this.$router.push({path: 'About'});
			}
			else{
				// TODO ausgabe aushalb console
				console.log("Login denied")
			}
		},
		async login(user, pwd) {
			this.loading = true;
			this.error = null;

			await axios
				.get("https://arktur.fmi.uni-jena.de/api/login/", {
					headers: { "Content-Type": "application/json" },
					params: {
						user: user,
						pwd: pwd
					}
				})
				.then((res) => {
					this.loggedIn = res.data.success;
					this.loading = false;
				})
				.catch((err) => {
					this.error = err;
					this.loading = false;
				});
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
