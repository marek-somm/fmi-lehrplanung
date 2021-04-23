<template>
	<div id="box">
		{{ number }}
	</div>
</template>

<script>
export default {
	props: {
		number: {
			required: true,
			type: Number
		}
	},
	setup() {
		return {};
	},
};
</script>

<style lang="scss" scoped>
#box {
	width: 100px;
	height: 100px;
	border: 2px solid black;
	margin: 1rem;

	display: flex;
	justify-content: center;
	align-items: center;

	font-size: 3rem;
}
</style>