<template>
	<div class="container">
		<Navbar />
		<router-view />
	</div>
</template>

<script>
import Navbar from "./components/Search/Navbar/Navbar.vue";
export default {
	components: { Navbar },
	setup() {
		return {};
	},
};
</script>

<style lang="scss">
body {
	margin: 0;
}

#app {
	font-family: "Roboto Flex", "Roboto Flex Fallback", sans-serif;
	letter-spacing: .02em;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
	text-align: center;
	color: #2c3e50;
}

.container {
	display: flex;
	flex-flow: column;
}
</style>