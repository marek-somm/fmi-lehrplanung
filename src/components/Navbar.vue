<template>
	<div id="nav">
		<router-link :to="{name: 'Home'}">Home</router-link> |
		<router-link :to="{name: 'About'}">About</router-link> |
		<router-link :to="{name: 'ServerTest'}">Server-Test</router-link> |
		<router-link :to="{name: 'Login'}">Login</router-link>
	</div>
</template>

<script>
export default {
	setup() {
		return {};
	},
};
</script>

<style lang="scss" scoped>
#nav {
	padding: 30px;
	background: #008198;
	margin-bottom: 3rem;

	a {
		font-weight: bold;
		color: #2c3e50;
		padding: 0.5rem;
		text-decoration: none;
		transition: color .2s ease;

		&.router-link-exact-active {
			color: white !important;
		}

		&:hover {
			color: rgb(200, 200, 200);
		}
	}
}
</style>
