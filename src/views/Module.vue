<template>
	<div class="module--container">
		<Searchbar @input="updateInput" placeholder="Modultitel/Modulcode" />
		<div class="module-content">
			<InfoModul
				:selected="data.selectedModul"
				:class="{ show: data.showModul }"
				@close="closeModul"
				@exam="updateVeranstaltung"
			/>
			<InfoVeranstaltung
				:selected="data.selectedVeranstaltung"
				:class="{ show: data.showVeranstaltung }"
				@close="closeVeranstaltung"
				@exam="updateModulWithExam"
			/>
			<Results @loadMore="loadMore" @select="updateModul" :data="module"/>
		</div>
	</div>
</template>

<script>
import Searchbar from "@/components/Search/Searchbar.vue";
import Results from "@/components/Search/Results.vue";
import InfoModul from "@/components/Search/InfoModul.vue";
import InfoVeranstaltung from "@/components/Search/InfoVeranstaltung.vue";
import { reactive } from "vue";
import { request } from "@/scripts/request.js";

export default {
	components: {
		Searchbar,
		Results,
		InfoModul,
		InfoVeranstaltung,
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
			showVeranstaltung: false,
			showModul: false,
			selectedVeranstaltung: null,
			selectedModul: null,
		});

		async function updateModul(value) {
			console.log(value)
			data.selectedModul = await rq.getModul(value);
			data.showModul = true;
		}

		async function updateModulWithExam(exam) {
			updateModul(exam.Modulcode)
		}

		async function updateVeranstaltung(exam) {
			console.log(exam);
			data.selectedVeranstaltung = await rq.getVeranstaltung(exam.Vnr, exam.semester);
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
			if(input != "[object InputEvent]") {
				data.input = input;
				module.limit = module.defaultLimit;
				searchModul(data.input);
			}
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
			updateModul,
			updateModulWithExam,
			updateVeranstaltung,
			closeVeranstaltung,
			closeModul,
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