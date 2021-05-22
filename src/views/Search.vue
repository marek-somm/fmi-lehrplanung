<template>
	<div class="search--container">
		<Searchbar @input="updateInput" />
		<div class="search-content">
			<Info :selected="data.selected" :class="{ show: data.selected }" />
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
		});

		function updateInput(value) {
			data.input = value;
		}

		function updateSelected(value) {
			data.selected = value;
		}

		return {
			data,
			updateInput,
			updateSelected,
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

	.searchbar--container {
		width: 100%;
	}

	.search-content {
		display: flex;
		flex-direction: row;

		.results--container {
			flex-grow: 1;
			padding: 0 0 1rem 0;
		}

		.info--container {
			color: transparent;
			width: 0;
			text-align: left;
			padding: 0 0.7rem 1rem 0.7rem;
			transition: all 0.5s ease;
		}

		.show {
			color: #2c3e50;
			width: 30%;
			flex-grow: 0;
			border-right: 1px gray solid;
		}
	}
}
</style>