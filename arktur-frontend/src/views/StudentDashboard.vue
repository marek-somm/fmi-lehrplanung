<template>
	<div id="dashboard-student-container">
		<div class="filter">
			<div class="row semester">
				<button
					:class="{
						hidden: data.semester.selected <= data.semester.current,
					}"
					class="button"
					@click="prevSemester"
				>
					&lt;
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
					&gt;
				</button>
			</div>
			<div class="row">
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

			<div class="row-more" v-if="data.fieldOfStudies">
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
			</div>

			<div class="row-more" v-if="data.categories">
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
			</div>
		</div>
		<p v-show="data.selected.subject">
			Gefundene Treffer: {{ data.events.length }}
		</p>
		<div class="event-container">
			<div class="event" v-for="(item, index) in data.events" :key="index">
				<a class="text"> {{ item.title }} </a>
			</div>
		</div>
	</div>
</template>

<script>
import { reactive } from "@vue/reactivity";
import { onMounted } from "@vue/runtime-core";
import search from "@/services/SearchService.js";
import helper from "@/services/HelperService.js";

export default {
	setup() {
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

		function updateEvents() {
			if (data.selected.subject) {
				console.log(data.selected);
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

		return {
			data,
			helper,
			onClickSubject,
			onClickFieldOfStudy,
			onClickCategory,
			prevSemester,
			nextSemester,
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
	height: calc(100vh - 10.676rem);

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
			font-size: 2rem;
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

	.filter {
		z-index: 10;
		width: 100%;
		display: flex;
		flex-direction: column;
		background-color: $selected2;
		position: sticky;
		top: 0;

		.row-more {
			padding: 0.75rem 2rem;

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

			.item {
				width: 100%;

				input {
					display: none;
				}

				input + label {
					padding: 0.75rem 2rem;
					height: calc(100% - 1.5rem);
					width: calc(100% - 4rem);
					display: inline-block;
					//border: solid 1px $filter-border;
					background-color: $filter;
					color: white;
					line-height: 140%;
					font-weight: bold;
					text-align: center;
					transition: border-color 0.15s ease-out, color 0.25s ease-out,
						background-color 0.15s ease-out, box-shadow 0.15s ease-out;
				}

				input:hover + label {
					//border-color: $hover;
					//background: $hover;
					text-decoration: underline;
					cursor: pointer;
				}

				input:checked + label {
					background-color: $selected2;
					//box-shadow: $activeShadow;
					//border-color: $selected2;
					//z-index: 1;
					text-decoration: underline;
				}

				input:focus + label {
					outline: dotted 1px #ccc;
					outline-offset: 0.45rem;
				}
			}
		}
	}
}
</style>