<template>
	<div class="new-container">
		<h3>Veranstaltung</h3>
		<div class="grid">
			<label>Titel </label>
			<SearchPanel class="searchpanel" v-model="data.title.value" />
			<label>SWS </label>
			<label v-if="data.existing" class="data">{{ data.sws.value }}</label>
			<SearchPanel
				class="searchpanel"
				v-model="data.sws.value"
				regex="\B|\d"
				v-if="!data.existing"
			/>
			<label>Turnus </label>
			<label v-if="data.existing" class="data">{{
				data.rotation.value
			}}</label>
			<SearchPanel
				class="searchpanel"
				v-model="data.rotation.input"
				:suggestions="data.rotation.suggestions"
				:placeholder="data.rotation.value"
				:dropdown="true"
				@blur="resetValue(data.rotation)"
				@enter="selectValue(data.rotation)"
				v-if="!data.existing"
			/>
			<label>Art </label>
			<label v-if="data.existing" class="data">{{ data.type.value }}</label>
			<SearchPanel
				class="searchpanel"
				v-model="data.type.input"
				:suggestions="data.type.suggestions"
				:placeholder="data.type.value"
				:dropdown="true"
				@blur="resetValue(data.type)"
				@enter="selectValue(data.type)"
				v-if="!data.existing"
			/>
			<label>Semester</label>
			<select id="semesters" class="selection" v-model="data.semester.value">
				<option
					v-for="(semester, index) in data.semester.list"
					:key="index"
					:value="semester"
				>
					{{ convertSemester(semester) }}
				</option>
			</select>
			<label>Sonstiges</label>
			<SearchPanel class="searchpanel" placeholder="z.B. Sprachen" v-model="data.extra.value" />
		</div>

		<h3>Personen</h3>
		<div class="persons">
			<div
				class="list"
				v-for="(person, index) in data.person.list"
				:key="index"
			>
				<button class="remove" @click="removePerson(data.person, person)">
					X
				</button>
				{{ person }}
			</div>
			<div class="grid">
				Person hinzufügen
				<SearchPanel
					class="searchpanel"
					v-model="data.person.input"
					:suggestions="data.person.suggestions"
					:placeholder="data.person.value"
					:dropdown="true"
					@blur="resetValue(data.person)"
					@enter="addPerson(data.person.input)"
				/>
			</div>
		</div>

		<h3>Prüfungen</h3>
		<div class="exams">
			<div class="box" v-for="(exam, index) in data.exams" :key="index">
				<button class="remove" @click="removeExam(exam)">x</button>
				<div class="grid">
					<label>Titel </label>
					<SearchPanel
						class="searchpanel"
						:suggestions="data.title.value ? [data.title.value] : []"
						v-model="exam.title"
					/>
					<label v-if="exam.pnr">Prüfungsnummer </label>
					<label v-if="exam.pnr" class="data">{{ exam.pnr }}</label>
					<label>Modulecode </label>
					<label label v-if="exam.pnr" class="data">{{
						exam.modulecode
					}}</label>
					<SearchPanel
						class="searchpanel"
						v-if="!exam.pnr"
						v-model="exam.modulecode"
					/>
					<label v-show="!exam.modulecode">Beschreibung </label>
					<SearchPanel
						class="searchpanel"
						placeholder="Falls kein Modulcode"
						v-model="exam.description"
						v-show="!exam.modulecode"
					/>
				</div>
			</div>
			<div class="new" @click="addExam" role="button">
				<span class="circle"></span>
				<span class="plus">+</span>
			</div>
		</div>

		<div class="button-bar">
			<button class="save button" role="button" @click="saveEvent">
				Speichern
			</button>
			<button class="cancel button" role="button" @click="cancel">
				Abbrechen
			</button>
			<button class="delete button" role="button" @click="deleteEvent">
				Löschen
			</button>
		</div>
	</div>
</template>

<script>
import { onMounted, reactive } from "vue";
import { useRouter } from "vue-router";
import search from "@/services/SearchService.js";
import helper from "@/services/HelperService.js";
import update from "@/services/UpdateService.js";
import SearchPanel from "../components/SearchPanel.vue";

export default {
	components: { SearchPanel },
	props: {
		id: Number,
	},
	setup(props) {
		const data = reactive({
			existing: false,
			vnr: null,
			title: {
				value: null,
			},
			sws: {
				value: null,
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
			extra: {
				value: null
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
			semester: {
				value: null,
				list: null,
				current: helper.getCurrentSemester()
			},
			person: {
				input: "",
				value: "",
				suggestions: [],
				list: [],
			},
			exams: [],
		});

		const router = useRouter();

		onMounted(() => {
			getPersons();
			getVeranstaltung(props.id);
		});

		async function getVeranstaltung(id) {
			if (id) {
				data.existing = true;
				let event = await search.getEvent(id);
				if (event.status == 404) {
					await router.push({ name: "Home" });
				} else {
					data.vnr = event.data.content.vnr;
					data.title.value = event.data.content.title;
					data.sws.value = event.data.content.sws;
					data.rotation.value = helper.convertTurnus(
						event.data.content.rotation
					);
					data.type.value = event.data.content.type;
					event.data.people.forEach((person) => {
						addPerson(person.surname + ", " + person.forename);
					});
					event.data.modules.forEach((module) => {
						addExam(module);
					});
					data.extra.value = event.data.content.extra;

					//semester list
					let currentSem = data.semester.current;
					let sem1 = helper.addTurnus(currentSem, 1);
					let sem2 = helper.addTurnus(sem1, 1);
					let sem3 = helper.addTurnus(sem2, 1);
					let sem4 = helper.addTurnus(sem3, 1);
					data.semester.list = [currentSem, sem1, sem2, sem3, sem4];
					data.semester.value = event.data.content.semester;
				}
			}
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

		function addPerson(person) {
			if (person) {
				data.person.list.push(person);

				helper.removeFromArray(data.person.suggestions, person);

				data.person.input = "";
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

		function addExam(exam) {
			data.exams.push({
				title: exam.modulecode ? exam.pivot.title : "",
				pnr: exam.modulecode ? exam.pivot.pnr : null,
				modulecode: exam.modulecode ? exam.modulecode : null,
				description: "",
			});
		}

		function removeExam(exam) {
			data.exams.splice(data.exams.indexOf(exam), 1);
		}

		function saveEvent() {
			let createEventBool = true;
			if (
				data.title.value &&
				data.sws.value &&
				data.rotation.value &&
				data.type.value &&
				data.semester.value && 
				data.person.list.length > 0
			) {
				if (data.exams.length > 0) {
					data.exams.forEach((exam) => {
						if (!(exam.title && (exam.modulecode || exam.description))) {
							alert("Es sind noch leere Felder vorhanden!");
							createEventBool = false;
						}
					});
				}
			} else {
				alert("Es sind noch leere Felder vorhanden!");
				createEventBool = false;
			}
			if (createEventBool) {
				save();
			}
		}

		async function save() {
			let response = await update.saveEvent({
				id: props.id,
				vnr: data.vnr,
				sem: data.semester.value,
				title: data.title.value,
				sws: data.sws.value,
				extra: data.extra.value,
				rotation: helper.convertTurnusToNumber(data.rotation.value),
				type: data.type.value,
				people: data.person.list,
				exams: data.exams,
			});
			if (response) {
				if (response.status == 422) {
					alert("Leider ist ein Fehler aufgetreten. Bitte kontaktieren Sie einen zuständigen Administratoren.");
				} else if ((response.status = 200)) {
					alert("Die Veranstaltung wurde erfolgreich angelegt");
					router.push({ name: "Home" });
				}
			} else {
				alert("Ein unerwarteter Fehler ist aufgetreten. Bitte kontaktieren Sie einen zuständigen Administratoren.");
			}
		}

		function cancel() {
			let value = confirm(
				"Wollen Sie wirklich abbrechen? Alle Änderungen gehen verloren."
			);
			if (value == true) {
				router.push({ name: "Home" });
			}
		}

		async function deleteEvent() {
			let value = confirm("Wollen Sie die Veranstaltung wirklich löschen?");
			if (value == true) {
				await update.deleteEvent({
					id: props.id,
				});
				router.push({ name: "Home" });
			}
		}

		function convertSemester(sem) {
			return helper.convertSemester(sem);
		}

		return {
			data,
			addPerson,
			removePerson,
			resetValue,
			selectValue,
			addExam,
			removeExam,
			saveEvent,
			cancel,
			deleteEvent,
			convertSemester,
		};
	},
};
</script>

<style lang="scss" scoped>
$border-color: #8c8c8c;

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

		.selection {
			border: 1px solid $border-color;
			height: 2rem;
			font-size: 0.9rem;
			padding: 0 0.5rem;
		}
	}

	.persons {
		.list {
			margin-left: 1.2rem;
		}

		div {
			margin: 0.5rem 0;
		}

		.remove {
			width: 1.5rem;
			height: 1.5rem;
			border-radius: 0;
			border: 1px solid $border-color;
			background-color: rgb(255, 220, 220);

			&:hover {
				background-color: rgb(255, 100, 100);
			}
		}
	}

	.exams {
		.new {
			display: flex;
			height: 5rem;

			align-items: center;
			justify-content: center;
			border: 1px solid $border-color;
			cursor: pointer;
			position: relative;
			background-color: white;

			.circle {
				height: 2rem;
				width: 2rem;
				background: rgb(230, 230, 230);
				border-radius: 50%;
				filter: blur(0.7rem);
			}

			.plus {
				position: absolute;
			}

			&:hover {
				background: rgb(245, 245, 245);

				.circle {
					background: white;
				}
			}
		}

		.box {
			border: 1px solid $border-color;
			padding: 1rem;
			padding-top: 2.5rem;
			margin-bottom: 1rem;
			position: relative;

			.remove {
				width: 2rem;
				height: 2rem;
				position: absolute;
				top: 0;
				right: 0;

				border-radius: 0;
				border: 1px solid $border-color;
				border-top: 0;
				border-right: 0;
				background-color: rgb(255, 220, 220);

				&:hover {
					background-color: rgb(255, 100, 100);
				}
			}

			.gray {
				background: rgb(230, 230, 230) !important;
				color: rgb(100, 100, 100);
			}
		}
	}

	.searchpanel {
		background: white;
	}

	.data {
		color: #2c3e50;
		padding: 0.3rem;
		font-size: 0.9rem;
	}

	.button-bar {
		display: grid;
		grid-gap: 0.7rem 0.4rem;
		margin: 3rem 0 2rem 0;

		.button {
			font: inherit;
			color: inherit;
			height: 3rem;
			border: 1px solid $border-color;
			cursor: pointer;
			background-color: rgb(255, 220, 220);

			&:hover {
				background-color: rgb(255, 100, 100);
			}

			&.cancel {
				grid-column: 1;
				grid-row: 2;
			}

			&.save {
				grid-column: 1 / span 2;
				grid-row: 1;
				background-color: white;

				&:hover {
					background: rgb(245, 245, 245);
				}
			}

			&.delete {
				grid-column: 2;
				grid-row: 2;
			}
		}
	}
}
</style>
