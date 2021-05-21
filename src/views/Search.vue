<template>
	<div>
		<Searchbar @input="updateInput" />
		<Results :input="data.input"/>
	</div>
</template>

<script>
import Searchbar from "@/components/Search/Searchbar.vue";
import Results from "@/components/Search/Results.vue";
import { reactive } from "vue";

export default {
	components: {
		Searchbar,
		Results,
	},
	setup() {
		const data = reactive({
			input: "",
		});

		function updateInput(value) {
			data.input = value;
		}

		return {
			data,
			updateInput,
		};
	},
};
</script>

<style lang="scss" scoped>
</style>