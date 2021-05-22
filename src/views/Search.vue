<template>
	<div class="search--container">
		<Searchbar @input="updateInput" />
		<div class="search-content">
			<Info
				:selected="data.selected"
				:class="{ show: data.show }"
				@close="close"
			/>
			<Results @selected="updateSelected" :input="data.input" />
		</div>
	</div>
</template>

<script>
import Searchbar from "@/components/Search/Searchbar.vue";
import Results from "@/components/Search/Results.vue";
import { reactive } from "vue";
import Info from "@/components/Search/Info.vue";

export default {
	components: {
		Searchbar,
		Results,
		Info,
	},
	setup() {
		const data = reactive({
			input: "",
			selected: null,
			show: false,
		});

		function updateInput(value) {
			data.input = value;
		}

		function updateSelected(value) {
			data.selected = value;
			data.show = true;
		}

		function close() {
			data.show = false;
		}

		return {
			data,
			updateInput,
			updateSelected,
			close,
		};
	},
};
</script>

<style lang="scss" scoped>
.search--container {
	display: flex;
	flex-direction: column;
	//padding-bottom: 0.5rem;
	justify-content: center;

	.search-content {
		display: flex;
		flex-direction: row;
	}
}
</style>