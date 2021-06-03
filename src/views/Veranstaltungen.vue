<template>
	<div class="veranstaltungen--container">
		<Searchbar @input="updateInput" placeholder="Veranstaltungstitel" />
		<div class="veranstaltungen-content">
			<InfoVeranstaltung
				:selected="data.selectedVeranstaltung"
				:class="{ show: data.showVeranstaltung }"
				@close="closeVeranstaltung"
				@exam="updateModul"
			/>
			<InfoModul
				:selected="data.selectedModul"
				:class="{ show: data.showModul }"
				@close="closeModul"
			/>
			<Results
				@loadMore="loadMore"
				@select="updateVeranstaltung"
				:data="veranstaltungen"
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
		});

		async function updateVeranstaltung(value, semester) {
			console.log(value + ", " + semester);
			data.selectedVeranstaltung = await rq.getVeranstaltung(value, semester);
			data.showVeranstaltung = true;
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

		return {
			data,
			updateInput,
			updateVeranstaltung,
			updateModul,
			closeVeranstaltung,
			closeModul,
			veranstaltungen,
			loadMore,
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