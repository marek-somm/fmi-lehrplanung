<template>
	<div class="searchbar--container">
		<input
			class="searchbar"
			ref="searchbar"
			v-model="data.input"
			placeholder="Veranstaltungstitel"
		/>
	</div>
</template>

<script>
import { onMounted, reactive, ref, watch } from "vue";
export default {
	setup(props, { emit }) {
		const searchbar = new ref(null);
		const data = reactive({
			input: "",
		});

		watch(
			() => data.input,
			() => {
				emit("input", data.input);
			}
		);

		onMounted(() => {
			searchbar.value.focus();
		});

		return {
			data,
			searchbar,
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
	width: 100%;

	.searchbar {
		width: 15rem;
		margin: 1rem;
		padding: 0.3rem 0.7rem 0.3rem 0.7rem;
		text-align: center;
	}
}
</style>