<template>
	<div>
		<h1>Login</h1>
	</div>

	<form class="login_input" @submit.prevent="loginSubmit">
		<label for="username">
			Benutzername:
		</label>
		<input id="username" name="username" v-model="usernameInput">
		<br><br>
		<label for="password">
		Passwort:
	</label>
	<input id="password" name="password" type="password" v-model="passwordInput">
	<br><br>
		<button>
			Einloggen
		</button>
	</form>
</template>


<script>
import VueSession from 'vue-session'


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
#boxes {
	display: flex;
	justify-content: center;
}
</style>
