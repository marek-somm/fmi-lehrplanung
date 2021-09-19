<template>
	<div class="veranstaltungen--container">
		<Searchbar
			@input="updateInput"
			@toggleFilter="toggleFilter"
			placeholder="Veranstaltungstitel"
		/>
		<div class="veranstaltungen-content">
			<InfoVeranstaltung
				:selected="data.selectedVeranstaltung"
				:filterActive="data.filterActive"
				:class="{ show: data.showVeranstaltung }"
				@close="closeVeranstaltung"
				@exam="updateModul"
			/>
			<InfoModul
				:selected="data.selectedModul"
				:filterActive="data.filterActive"
				:class="{ show: data.showModul }"
				@close="closeModul"
				@exam="updateVeranstaltungWithExam"
			/>
			<Results
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
import Results from "@/components/Search/Results.vue";
import InfoVeranstaltung from "@/components/Search/InfoVeranstaltung.vue";
import InfoModul from "@/components/Search/InfoModul.vue";
import { reactive } from "vue";
import { request } from "@/scripts/request.js";

export default {
	components: {
		Searchbar,
		Results,
		InfoVeranstaltung,
		InfoModul,
	},
	setup() {
		const rq = new request();

		const veranstaltungen = reactive({
			defaultLimit: 20,
			limit: 20,
			all: null,
		});

		const data = reactive({
			input: "",
			showVeranstaltung: false,
			showModul: false,
			selectedVeranstaltung: null,
			selectedModul: null,
			filterActive: false,
		});

		async function updateVeranstaltung(value, semester) {
			console.log(value + ", " + semester);
			data.selectedVeranstaltung = await rq.getVeranstaltung(
				value,
				semester
			);
			data.showVeranstaltung = true;
		}

		async function updateVeranstaltungWithExam(exam) {
			updateVeranstaltung(exam.Vnr, exam.semester);
		}

		async function updateModul(exam) {
			console.log(exam);
			data.selectedModul = await rq.getModul(exam.Modulcode);
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

		async function searchVeranstaltung(name) {
			veranstaltungen.all = await rq.searchVeranstaltung(
				name,
				veranstaltungen.limit
			);
		}

		function toggleFilter(value) {
			data.filterActive = value;
		}

		return {
			data,
			updateInput,
			updateVeranstaltung,
			updateVeranstaltungWithExam,
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
.veranstaltungen--container {
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