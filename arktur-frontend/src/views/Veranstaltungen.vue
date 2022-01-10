<template>
	<div class="veranstaltungen-container">
		<Searchbar
			@input="debounceInput"
			@toggleFilter="toggleFilter"
			placeholder="Veranstaltungstitel"
		/>
		<div class="veranstaltungen-content">
			<InfoVeranstaltung
				:selected="data.selectedVeranstaltung"
				:filterActive="data.filterActive"
				:class="{ show: data.showVeranstaltung }"
				@close="closeVeranstaltung"
				@relation="updateModul"
			/>
			<InfoModul
				:selected="data.selectedModul"
				:filterActive="data.filterActive"
				:class="{ show: data.showModul }"
				@close="closeModul"
				@relation="updateVeranstaltungWithModul"
			/>
			<Result
				@loadMore="loadMore"
				@select="updateVeranstaltung"
				:data="veranstaltungen"
				:filterActive="data.filterActive"
			/>
		</div>
	</div>
</template>

<script>
import Searchbar from "@/components/Search/Searchbar.vue";
import Result from "@/components/Search/ResultVeranstaltung.vue";
import InfoVeranstaltung from "@/components/Search/InfoVeranstaltung.vue";
import InfoModul from "@/components/Search/InfoModul.vue";
import { reactive } from "vue";
import search from "@/services/SearchService.js";
import { debounce } from "debounce";

export default {
	components: {
		Searchbar,
		Result,
		InfoVeranstaltung,
		InfoModul,
	},
	setup() {
		const veranstaltungen = reactive({
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

		searchVeranstaltung();

		async function updateVeranstaltung(vnr, semester) {
			data.selectedVeranstaltung = await search.getEvent(vnr, semester);
			data.showVeranstaltung = true;
		}

		async function updateVeranstaltungWithModul(modul) {
			updateVeranstaltung(modul.vnr, modul.semester);
		}

		async function updateModul(relation) {
			data.selectedModul = await search.getModule(relation.modulecode);
			data.showModul = true;
		}

		function closeVeranstaltung() {
			data.showVeranstaltung = false;
		}

		function closeModul() {
			data.showModul = false;
		}

		function loadMore() {
			veranstaltungen.limit += veranstaltungen.defaultLimit;
			searchVeranstaltung(data.input);
		}

		function updateInput(input) {
			if (input != "[object InputEvent]") {
				data.input = input;
				veranstaltungen.limit = veranstaltungen.defaultLimit;
				searchVeranstaltung(data.input);
			}
		}

		async function searchVeranstaltung(value) {
			veranstaltungen.all = await search.searchEvent(
				value,
				veranstaltungen.limit
			);
			veranstaltungen.count = 0
			Object.keys(veranstaltungen.all.data).forEach(semester => {
				veranstaltungen.count += veranstaltungen.all.data[semester].length
			});
		}

		function toggleFilter(value) {
			data.filterActive = value;
		}

		return {
			data,
			debounceInput: debounce(updateInput, 300),
			updateVeranstaltung,
			updateVeranstaltungWithModul,
			updateModul,
			closeVeranstaltung,
			closeModul,
			veranstaltungen,
			loadMore,
			toggleFilter,
		};
	},
};
</script>

<style lang="scss" scoped>
.veranstaltungen-container {
	display: flex;
	flex-direction: column;
	//padding-bottom: 0.5rem;
	justify-content: center;

	.veranstaltungen-content {
		display: flex;
		flex-direction: row;
	}
}
</style>