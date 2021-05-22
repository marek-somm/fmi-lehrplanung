<template>
	<div class="searchbar--container">
		<input class="searchbar" v-model="data.input" placeholder="Veranstaltungstitel" />
	</div>
</template>

<script>
import { reactive, watch } from "vue";
export default {
	setup(props, { emit }) {
		const data = reactive({
			input: "",
		})

		watch(() => data.input, () => {
			emit('input', data.input)
		})

		return {
			data,
		};
	},
};
</script>

<style lang="scss" scoped>
.searchbar--container {
	display: flex;
	flex-direction: row;
	justify-content: center;
	align-items: center;
	background: #eee;
	position: sticky;

	.searchbar {
		width: 15rem;
		margin: 1rem;
		padding: .3rem .7rem .3rem .7rem;
		text-align: center;
	}
}
</style>