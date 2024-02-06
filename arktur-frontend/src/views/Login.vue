<template>
	<div class="login--container">
		<div class="head-wrapper">
			<div class="head">
				<h1 class="title">Login</h1>
			</div>
		</div>
		<div class="login-wrapper">
			<div class="row username">
				<div class="label"><label>Benutzername: </label></div>
				<div class="input">
					<input placeholder="" v-model="data.usernameInput" />
				</div>
			</div>
			<div class="row password">
				<div class="label"><label>Passwort: </label></div>
				<div class="input">
					<input type="password" placeholder="" v-model="data.passwordInput" @keyup.enter="loginSubmit" />
				</div>
				<div class="submit"><button @click="loginSubmit"></button></div>
			</div>
			<div class="row error" v-show="data.error">
				<div class="label"></div>
				<div class="message">
					{{ data.error }}
				</div>
			</div>
		</div>
	</div>
</template>
<script setup>
import store from "@/store";
import { reactive } from "vue";
import { useRouter } from "vue-router";
import auth from "@/services/AuthService";

const router = useRouter();

const data = reactive({
	usernameInput: "",
	passwordInput: "",
	error: null,
	output: null
});

if (store.state.User.login) {
	router.push({ name: "Home" });
}

async function loginSubmit() {
	const payload = {
		uid: data.usernameInput,
		password: data.passwordInput
	};
	const answer = await auth.login(payload);
	if (answer.data.errors) {
		data.error = answer.data.errors[Object.keys(answer.data.errors)[0]][0];
	} else if (answer.data.success) {
		store.dispatch('User/setLogin', answer.data.success);
		store.dispatch('User/setLevel', answer.data.level);
		store.dispatch('User/setUid', answer.data.uid);
		store.dispatch('setCurrentSemester', answer.data.currentSemester);
		router.push({ name: "Home" });
	} else {
		data.output = answer.data;
	}
}

</script>

<style lang="scss" scoped>
.login--container {
	font-size: 0.8rem;

	.head-wrapper {
		background-color: #1d60bd;
		padding: 5rem 0 5rem 0;

		.head {
			display: flex;
			flex-direction: column;
			align-items: flex-start;
			width: 42rem;
			margin: auto;

			.title {
				color: white;
				margin: 0;
				font-variation-settings: "opsz" 15, "slnt" 0, "wdth" 30, "wght" 600, "GRAD" 100 !important;
			}
		}
	}

	.login-wrapper {
		display: flex;
		flex-direction: column;
		background: #eee;
		width: 42rem;
		padding: 1rem;
		margin: 2.5rem auto 0 auto;
		color: #002350;

		.row {
			width: 100%;
			display: table;

			.label {
				display: table-cell;
				vertical-align: middle;
				float: none;
				width: 12.5rem;
				padding-right: 1rem;
				text-align: right;

				font-size: 1.375rem;
				font-style: italic;
			}

			.input {
				display: table-cell;
				vertical-align: middle;
				float: none;

				input {
					width: 100%;
					display: block;
					box-sizing: border-box;
					height: 46px;
					padding: 0 1rem;
					border: 1px solid white;

					font-size: 1.375rem;
					font-family: "Palatino Linotype", AmiriRegular, BookAntiqua,
						Georgia, serif;
					font-style: italic;
					color: #002350;

					&:focus,
					&:hover {
						outline: 0;
						border: 1px solid #2285ff;
					}
				}
			}

			.submit {
				display: table-cell;
				vertical-align: middle;
				float: none;
				width: 48px;
				padding-left: 1rem;

				button {
					width: 48px;
					height: 48px;
					padding: 0;
					border: none;
					cursor: pointer;
					background-image: url("https://www.fmi.uni-jena.de/skin/_global/_images/blocks/slider_next.svg");
				}
			}

			.message {
				display: table-cell;
				vertical-align: middle;
				float: left;
				padding-top: 0.7rem;

				font-size: 0.9rem;
				font-family: "Roboto", Arial, sans-serif;
			}
		}

		.username {
			margin-bottom: 1rem;
		}
	}
}

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
