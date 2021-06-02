<template>
	<div class="module--container">
		<Searchbar @input="updateInput" placeholder="Modultitel/Modulcode" />
		<div class="module-content">
			<Info
				:selected="data.selected"
				:class="{ show: data.show }"
				@close="close"
			/>
			<Results @loadMore="loadMore" @select="updateSelected" :data="module"/>
		</div>
	</div>
</template>

<script>
import Searchbar from "@/components/Search/Searchbar.vue";
import Results from "@/components/Search/Results.vue";
import Info from "@/components/Search/InfoModul.vue";
import { reactive } from "vue";
import { request } from "@/scripts/request.js";

export default {
	components: {
		Searchbar,
		Results,
		Info,
	},
	setup() {
		const rq = new request();

		const module = reactive({
			defaultLimit: 20,
			limit: 20,
			all: null
		})

		const data = reactive({
			input: "",
			show: false,
			selected: null,
		});

		async function updateSelected(value, semester) {
			console.log(value + ", " + semester)
			data.selected = await rq.getModul(value);
			data.show = true;
		}

		function close() {
			data.show = false;
		}

		function loadMore() {
			module.limit += module.defaultLimit;
			searchModul(data.input);
		}

		function updateInput(input) {
			data.input = input;
			module.limit = module.defaultLimit;
			searchModul(data.input);
		}

		async function searchModul(name) {
			module.all = await rq.searchModul(
				name,
				module.limit
			)
		}

		return {
			data,
			updateInput,
			updateSelected,
			close,
			module,
			loadMore
		};
	},
};
</script>

<style lang="scss" scoped>
.module--container {
	display: flex;
	flex-direction: column;
	//padding-bottom: 0.5rem;
	justify-content: center;

	.module-content {
		display: flex;
		flex-direction: row;
	}
}
</style>