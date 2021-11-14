<template>
	<div class="new-container">
		<h3>Veranstaltung</h3>
		<div class="grid">
			<label>Titel </label>
			<SearchPanel v-model="data.title.input" />
			<label>SWS </label>
			<SearchPanel v-model="data.sws.value" regex="\B|\d" />
			<label>Turnus </label>
			<SearchPanel
				v-model="data.rotation.input"
				:suggestions="data.rotation.suggestions"
				:placeholder="data.rotation.value"
				@blur="resetValue(data.rotation)"
				@click="selectValue(data.rotation)"
				@enter="selectValue(data.rotation)"
			/>
			<label>Art </label>
			<SearchPanel
				v-model="data.type.input"
				:suggestions="data.type.suggestions"
				:placeholder="data.type.value"
				@blur="resetValue(data.type)"
				@click="selectValue(data.type)"
				@enter="selectValue(data.type)"
			/>
			<label>Zielgruppe </label>
			<SearchPanel />
		</div>

		<h3>Personen</h3>
		<div class="persons">
			<div
				class="list"
				v-for="(person, index) in data.person.list"
				:key="index"
			>
				<button @click="removePerson(data.person, person)">X</button>
				{{ person }}
			</div>
			<div class="grid">
				Person hinzufügen
				<SearchPanel
					v-model="data.person.input"
					:suggestions="data.person.suggestions"
					:placeholder="data.person.value"
					@blur="resetValue(data.person)"
					@click="addPerson(data.person)"
					@enter="addPerson(data.person)"
				/>
			</div>
		</div>

		<h3>Prüfungen</h3>
		<div class="grid">
			<label>Titel </label>
			<SearchPanel />
			<label>Prüfungsnummer </label>
			<SearchPanel />
			<label>Modulecode </label>
			<SearchPanel />
			<label>Beschreibung </label>
			<SearchPanel />
			<label>Titel </label>
			<SearchPanel />
			<label>Prüfungsnummer </label>
			<SearchPanel />
			<label>Modulecode </label>
			<SearchPanel />
			<label>Beschreibung </label>
			<SearchPanel />
		</div>
	</div>
</template>

<script>
import { onMounted, reactive } from "vue";
import { useRoute } from "vue-router";
import search from "@/services/SearchService.js";
import helper from "@/services/HelperService.js";
import SearchPanel from "../components/SearchPanel.vue";

export default {
	components: { SearchPanel },
	setup() {
		const data = reactive({
			title: {
				input: "",
			},
			sws: {
				input: "",
			},
			rotation: {
				input: "",
				value: "",
				suggestions: [
					helper.convertTurnus(0),
					helper.convertTurnus(1),
					helper.convertTurnus(2),
				],
			},
			type: {
				input: "",
				value: "",
				suggestions: [
					"Arbeitsgemeinschaft",
					"Begleitveranstaltung zum Praxissemester",
					"Einführungsveranstaltung",
					"Klausur",
					"Kolloquium",
					"Kurs",
					"Oberseminar",
					"Praktikum",
					"Praktikum/Seminar",
					"Praxismodul",
					"Projekt",
					"Proseminar",
					"Prüfungsvorbereitung",
					"Ringvorlesung",
					"Seminar",
					"Seminar/Übung",
					"Sonstiges",
					"Tutorium",
					"Vorlesung",
					"Vorlesung/Praktikum",
					"Vorlesung/Seminar",
					"Vorlesung/Übung",
					"Vortrag",
					"Workshop",
					"Übung",
					"Übung/Praktikum",
				],
			},
			person: {
				input: "",
				value: "",
				suggestions: [],
				list: [],
			},
		});

		const route = useRoute();
		const vnr = route.params.vnr;
		const sem = route.params.sem;

		onMounted(() => {
			getVeranstaltung(vnr, sem);
			getPersons();
		});

		async function getVeranstaltung(vnr, sem) {
			if (!vnr) {
				vnr = 0;
			}
			if (!sem) {
				sem = 0;
			}
			return await search.getEvent(vnr, sem);
		}

		async function getPersons() {
			let res = await search.searchPerson("");
			let persons = [];
			res.data.forEach((item) => {
				persons.push(item.surname + ", " + item.forename);
			});
			data.person.suggestions = persons;
			data.person.suggestions.sort();
		}

		function addPerson(field) {
			if (field.input) {
				field.list.push(field.input);

				helper.removeFromArray(field.suggestions, field.input);

				field.input = "";
			}
		}

		function removePerson(field, person) {
			helper.removeFromArray(field.list, person);

			field.suggestions.push(person);
			field.suggestions.sort();
		}

		function resetValue(field) {
			field.input = "";
		}

		function selectValue(field) {
			if (field.input != "") {
				field.value = field.input;
			}
			resetValue(field);
		}

		return {
			data,
			addPerson,
			removePerson,
			resetValue,
			selectValue,
		};
	},
};
</script>

<style lang="scss" scoped>
.new-container {
	background: #eee;
	text-align: left;
	margin: 0 30%;
	padding: 0.5rem 1.5rem;
	overflow: auto;
	height: calc(100vh - 13.82rem);

	.grid {
		display: grid;
		grid-template-columns: max-content auto;
		grid-row-gap: 0.3rem;
		grid-column-gap: 1.5rem;
		align-items: center;
	}

	.persons {
		.list {
			margin-left: 1.2rem;
		}
		div {
			margin: 0.5rem 0;
		}
	}
}
</style>
