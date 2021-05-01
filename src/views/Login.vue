<template>
	<div>
		<h1>Login</h1>
	</div>

	<form class="login_input" @submit.prevent="loginSubmit">
		<label for="username">
			<strong>Benutzername:</strong>
		</label>
		<input id="username" name="username" v-model="usernameInput">
		<label for="password">
			<strong>Passwort:</strong>
		</label>
		<input id="password" name="password" type="password" v-model="passwordInput">
		<br><br>
		<button>
			<strong>Einloggen</strong>
		</button>
	</form>
</template>
<script>

export default {
	data(){
		return {
			usernameInput: '',
			passwordInput: ''
		}
	},
	methods:{
		loginSubmit(){
			// TODO Verarbeitung der Daten
			console.log(this.usernameInput)
			console.log(this.passwordInput)
			// TODO Login Funktion -> Session eroeffnen
			// TODO richtiges Ziel
			this.$router.push({path: 'About'});
		}
	}
};
</script>

<style lang="scss" scoped>
.login_input{
  padding: 2em 1em;
  font-family: helvetica, sans-serif;

	label {
		color: #2c3e50;
		margin: 0 3% .25em;
		font-size: 1.2em;
		display: block;
		font-family: helvetica, sans-serif;
	}

	input {
		width: 25%;
		padding: .5em .25em;
		margin: 0 3% 1em;
		font-size: 1.2em;
		border: 2px solid #000;
		outline: none;
		color: #2c3e50;
	}

	button{
		width: 25%;
		padding: .5em .25em;
		margin: 0 3% 1em;
		font-size: 1.2em;
		border: 2px solid #000;
		color: #2c3e50;
	}
}
</style>
