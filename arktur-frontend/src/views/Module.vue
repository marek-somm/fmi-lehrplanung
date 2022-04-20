<template>
	<div class="module--container">
		<Searchbar
			@update="debounceInput"
			@toggleFilter="toggleFilter"
			@changeFilter="changeFilter"
			placeholder="Modultitel/Modulcode"
		/>
		<div class="module-content">
			<InfoModul
				:selected="data.selectedModul"
				:filterActive="data.filterActive"
				:class="{ show: data.showModul }"
				@close="closeModul"
				@relation="updateVeranstaltung"
			/>
			<InfoVeranstaltung
				:selected="data.selectedVeranstaltung"
				:filterActive="data.filterActive"
				:class="{ show: data.showVeranstaltung }"
				@close="closeVeranstaltung"
				@relation="updateModulWithVeranstaltung"
			/>
			<Result
				@loadMore="loadMore"
				@select="updateModul"
				:data="module"
				:filterActive="data.filterActive"
			/>
		</div>
	</div>
</template>

<script>
import Searchbar from "@/components/Search/Searchbar.vue";
import Result from "@/components/Search/ResultModul.vue";
import InfoModul from "@/components/Search/InfoModul.vue";
import InfoVeranstaltung from "@/components/Search/InfoVeranstaltung.vue";
import { reactive } from "vue";
import { debounce } from "debounce";
import search from "@/services/SearchService.js"

export default {
	components: {
		Searchbar,
		Result,
		InfoModul,
		InfoVeranstaltung,
	},
	setup() {
		const module = reactive({
			defaultLimit: 20,
			limit: 20,
			all: null,
			count: 0,
		});

		const data = reactive({
			input: "",
			showVeranstaltung: false,
			showModul: false,
			selectedVeranstaltung: null,
			selectedModul: null,
			filterActive: false,
		});

		searchModul()

		async function updateModul(value) {
			data.selectedModul = await search.getModule(value);
			data.showModul = true;
		}

		async function updateModulWithVeranstaltung(veranstaltung) {
			updateModul(veranstaltung.modulecode);
		}

		async function updateVeranstaltung(id) {
			data.selectedVeranstaltung = await search.getEvent(id);
			data.showVeranstaltung = true;
		}

		function closeVeranstaltung() {
			data.showVeranstaltung = false;
		}

		function closeModul() {
			data.showModul = false;
		}

		function loadMore() {
			module.limit += module.defaultLimit;
			searchModul(data.input);
		}

		function updateInput(input) {
			if (input != "[object InputEvent]") {
				data.input = input;
				module.limit = module.defaultLimit;
				searchModul(data.input);
			}
		}

		async function searchModul(name) {
			module.all = await search.searchModule(name, module.limit);
			module.count = module.all.data.length;
		}

		function toggleFilter(value) {
			data.filterActive = value;
		}

		return {
			data,
			debounceInput: debounce(updateInput, 300),
			updateModul,
			updateModulWithVeranstaltung,
			updateVeranstaltung,
			closeVeranstaltung,
			closeModul,
			module,
			loadMore,
			toggleFilter,
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