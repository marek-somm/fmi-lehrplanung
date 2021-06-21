<template>
	<div class="home--container">
		<h1>Welcome to Arktur!</h1>
		<div class="textbox">
		</div>
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
#boxes {
	display: flex;
	justify-content: center;
}

.home--container {
	.textbox {
		width: 30%;
		margin: auto;
		text-align: justify;
	}
}
</style>