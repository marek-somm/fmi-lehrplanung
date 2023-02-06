<template>
	<div id="dashboard-student-container">
		<div class="filter">
			<div class="row semester">
				<div class="placeholder"></div>
				<div class="item">
					<button
						:class="{
							hidden: data.semester.selected <= data.semester.current,
						}"
						class="button"
						@click="prevSemester"
					>
						&larr;
					</button>
					<p class="name">
						{{ helper.convertSemesterFull(data.semester.selected) }}
					</p>
					<button
						:class="{
							hidden:
								data.semester.selected >=
								helper.addTurnus(data.semester.current, 2),
						}"
						class="button"
						@click="nextSemester"
					>
						&rarr;
					</button>
				</div>
				<div class="button-pannel">
					<button
						v-show="data.hideFilter == true"
						@click="data.hideFilter = false"
					>
						&plus;
					</button>
				</div>
			</div>
			<div class="row" v-if="!data.fieldOfStudies">
				<div
					class="item"
					v-for="(subject, index) in data.subjects"
					:key="index"
				>
					<input
						type="radio"
						:value="subject"
						v-model="data.selected.subject"
						name="subject"
						@click="onClickSubject(subject)"
						:id="subject"
					/>
					<label :for="subject">{{ subject }}</label>
				</div>
			</div>

			<div
				class="row-more"
				v-if="data.fieldOfStudies"
				:class="{ 'hide-filter': data.hideFilter }"
			>
				<div class="placeholder"></div>
				<div class="item">
					<label :for="fieldOfStudy">Studiengang: </label>
					<select
						name="fieldOfStudy"
						id="fieldOfStudy"
						v-model="data.selected.fieldOfStudy"
						@change="onClickFieldOfStudy"
					>
						<option
							v-for="(fieldOfStudy, index) in data.fieldOfStudies"
							:key="index"
							:value="fieldOfStudy"
						>
							{{ fieldOfStudy }}
						</option>
					</select>
				</div>
				<div class="button-pannel">
					<button v-show="!data.categories">&#10226;</button>
					<button
						v-show="!data.categories"
						@click="data.hideFilter = true"
					>
						&minus;
					</button>
				</div>
			</div>

			<div
				class="row-more"
				v-if="data.categories"
				:class="{ 'hide-filter': data.hideFilter }"
			>
				<div class="placeholder"></div>
				<div class="item">
					<label :for="category">Säule: </label>
					<select
						name="category"
						id="category"
						v-model="data.selected.category"
						@change="onClickCategory"
					>
						<option
							v-for="(category, index) in data.categories"
							:key="index"
							:value="category"
						>
							{{ category }}
						</option>
					</select>
				</div>
				<div class="button-pannel">
					<button>&#10226;</button>
					<button @click="data.hideFilter = true">&minus;</button>
				</div>
			</div>
		</div>
		<div class="content">
			<InfoVeranstaltung
				:selected="event.selected"
				:filterActive="false"
				:class="{ show: event.show, smaller: data.categories }"
				class="info"
				@close="closeVeranstaltung"
				@relation="updateModul"
			/>
			<InfoModul
				:selected="module.selected"
				:filterActive="false"
				:class="{ show: module.show, smaller: data.categories }"
				class="info"
				@close="closeModul"
				@relation="updateVeranstaltung"
			/>
			<div class="result-container" :class="{ smaller: data.categories }">
				<div class="event-container">
					<p v-show="data.selected.subject">
						Gefundene Treffer: {{ data.events.length }}
					</p>
					<div
						class="event"
						v-for="(item, index) in data.events"
						:key="index"
						@click="updateVeranstaltung(item.id)"
					>
						<a class="text"> {{ item.title }} </a>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { reactive } from "@vue/reactivity";
import { onMounted } from "@vue/runtime-core";
import search from "@/services/SearchService.js";
import helper from "@/services/HelperService.js";
import InfoVeranstaltung from "@/components/Search/InfoVeranstaltung.vue";
import InfoModul from "@/components/Search/InfoModul.vue";

export default {
	components: {
		InfoVeranstaltung,
		InfoModul,
	},
	setup() {
		const event = reactive({
			show: false,
			selected: null,
		});
		const module = reactive({
			show: false,
			selected: null,
		});

		const data = reactive({
			events: {},
			selected: {
				subject: null,
				fieldOfStudy: "Alle",
				category: "Alle",
			},
			subjects: null,
			fieldOfStudies: null,
			categories: null,
			semester: {
				current: helper.getCurrentSemester(),
				selected: helper.getCurrentSemester(),
			},
			hideFilter: null,
		});

		onMounted(() => {
			setSubjects();
		});

		async function setSubjects() {
			let subjects = await search.getSubjects();
			data.subjects = subjects.data;
		}

		async function setFieldOfStudies(subject) {
			let fieldOfStudies = await search.getFieldOfStudies(subject);
			data.fieldOfStudies = fieldOfStudies.data;
		}

		async function setCategories(fieldOfStudy) {
			let categories = await search.getCategories(fieldOfStudy);
			data.categories = categories.data;
		}

		async function setEvents(filter) {
			filter["semester"] = data.semester.selected;
			let events = await search.getStudentEvents(filter);
			data.events = events.data;
		}

		function onClickSubject(subject) {
			if (subject != data.selected.subject) {
				data.fieldOfStudies = setFieldOfStudies(subject);
				data.selected.fieldOfStudy = "Alle";
				data.categories = null;
				setEvents({
					subject: subject,
				});
			}
		}

		function onClickFieldOfStudy(fieldOfStudy) {
			if (data.selected.fieldOfStudy == "Alle") {
				data.categories = null;
				data.selected.category = "Alle";
				setEvents({
					subject: data.selected.subject,
				});
			} else if (fieldOfStudy != data.selected.fieldOfStudy) {
				data.categories = setCategories(data.selected.fieldOfStudy);
				data.selected.category = "Alle";
				setEvents({
					subject: data.selected.subject,
					fieldOfStudy: data.selected.fieldOfStudy,
				});
			}
		}

		function onClickCategory(category) {
			if (data.selected.category == "Alle") {
				setEvents({
					subject: data.selected.subject,
					fieldOfStudy: data.selected.fieldOfStudy,
				});
			} else if (category != data.selected.category) {
				setEvents({
					subject: data.selected.subject,
					fieldOfStudy: data.selected.fieldOfStudy,
					category: data.selected.category,
				});
			}
		}

		function onClickEvent() {
			console.log("clock");
		}

		function updateEvents() {
			if (data.selected.subject) {
				setEvents({
					subject: data.selected.subject,
					fieldOfStudy:
						data.selected.fieldOfStudy == "Alle"
							? null
							: data.selected.fieldOfStudy,
					category:
						data.selected.category == "Alle"
							? null
							: data.selected.category,
				});
			}
		}

		function prevSemester() {
			if (data.semester.selected > data.semester.current) {
				data.semester.selected = helper.addTurnus(
					data.semester.selected,
					-1
				);
				updateEvents();
			}
		}

		function nextSemester() {
			if (
				data.semester.selected < helper.addTurnus(data.semester.current, 2)
			) {
				data.semester.selected = helper.addTurnus(
					data.semester.selected,
					1
				);
				updateEvents();
			}
		}

		async function updateVeranstaltung(id) {
			event.selected = await search.getEvent(id);
			event.show = true;
		}

		async function updateModul(relation) {
			module.selected = await search.getModule(relation.modulecode);
			module.show = true;
		}

		function closeVeranstaltung() {
			event.show = false;
		}

		function closeModul() {
			module.show = false;
		}

		return {
			data,
			event,
			module,
			helper,
			onClickSubject,
			onClickFieldOfStudy,
			onClickCategory,
			onClickEvent,
			prevSemester,
			nextSemester,
			updateVeranstaltung,
			updateModul,
			closeVeranstaltung,
			closeModul,
		};
	},
};
</script>

<style lang="scss" scoped>
$filter: #1d60bd;
$filter-border: #1b509b;
$hover: #2370dd;
$selected1: #205cb4;
$selected2: #174488;

$activeShadow: 0 0 10px rgba($selected1, 0.5);

#dashboard-student-container {
	display: flex;
	flex-flow: column;
	flex-grow: 1;
	align-items: center;
	padding: 0 0 1rem 0;
	transition: height 0.2s ease;
	position: relative;
	overflow-y: auto;
	height: calc(100vh - 13.85rem);

	.semester {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 100%;
		background-color: $filter;

		.name {
			margin: 1rem 0rem;
			color: white;
			font-weight: bold;
		}

		.button {
			height: 100%;
			width: max-content;
			font-size: 1.5rem;
			text-align: center;
			padding-bottom: 0.4rem;

			background-color: $filter;
			color: white;
			border: none;

			&:hover {
				cursor: pointer;
			}

			&.hidden {
				opacity: 0;

				&:hover {
					cursor: auto;
				}
			}
		}
	}

	.content {
		width: 100%;
		display: flex;
		flex-direction: row;

		.info {
			height: calc(100vh - 20.1rem);

			&.smaller {
				height: calc(100vh - 24.1rem) !important;
			}
		}

		.result-container {
			display: flex;
			flex-flow: column;
			flex-grow: 1;
			align-items: center;
			overflow-y: auto;
			height: calc(100vh - 21.1rem);
			padding: 0 0 1rem 0;
			transition: height 0.2s ease;

			&.smaller {
				height: calc(100vh - 25.1rem) !important;
			}

			.event-container {
				padding: 1rem 0;
				width: 40%;

				.event {
					border: 1px black solid;
					transition: all 0.1s ease;
					padding: 2rem;
					margin-bottom: 0.5rem;
					z-index: 0;

					&:hover {
						cursor: pointer;
						background-color: rgb(240, 240, 240);
					}
				}
			}
		}
	}

	.filter {
		z-index: 10;
		width: 100%;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		background-color: $selected2;
		position: sticky;
		top: 0;

		.hide-filter {
			height: 0 !important;
			padding: 0 !important;
		}

		.placeholder {
			width: 10%;
			margin: 0 1rem;
		}

		.button-pannel {
			width: 10%;
			margin: 0 1rem;

			button {
				font-size: 1.5rem;
				width: 50%;
				height: 100%;
				color: white;
				background-color: $selected1;
				border: none;

				&:hover {
					cursor: pointer;
					background: $hover;
				}
			}
		}

		.row-more {
			padding: 0.75rem 0rem;
			display: flex;
			justify-content: space-between;
			width: 100%;
			overflow: hidden;

			.item {
				* {
					padding: 0.75rem 2rem;
					text-align: center;
					color: white;
					font-weight: bold;
				}

				select {
					background-color: $filter;
					border: none;
				}
			}
		}

		.row {
			display: flex;
			flex-direction: row;
			justify-content: center;
			align-items: center;

			.item {
				width: 100%;
				height: 100%;
				display: flex;
				justify-content: center;
				align-items: center;
				background-color: $filter;

				input {
					display: none;
				}

				input + label {
					padding: 0.75rem 2rem;
					width: calc(100% - 4rem);
					height: max-content;
					display: inline-block;
					//border: solid 1px $filter-border;
					color: white;
					line-height: 140%;
					font-weight: bold;
					text-align: center;
					transition: border-color 0.15s ease-out, color 0.25s ease-out,
						background-color 0.15s ease-out, box-shadow 0.15s ease-out;
				}

				&:hover {
					label {
						//border-color: $hover;
						//background: $hover;
						text-decoration: underline;
						cursor: pointer;
					}
				}
			}
		}
	}
}
</style>