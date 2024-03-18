<template>
	<div id="export-container">
		<div id="download-bar">
			<select id="semesters" class="selection" @change="getVeranstaltungen(semester.value)" v-model="semester.value">
				<option v-for="(semester, index) in semester.list" :key="index" :value="semester">
					{{ helper.convertSemester(semester) }}
				</option>
			</select>
			<button id="download-button" @click="download">Download</button>
		</div>
		<div id="table-container">
			<table id="event-table">
				<tr>
					<th>Vorlesungsnr.</th>
					<th>Modul/Prüfung</th>
					<th>Titel</th>
					<th>Personen</th>
					<th>Typ</th>
					<th>SWS</th>
					<th>Studiengänge</th>
					<th>Sonstiges</th>
					<th>Raum</th>
					<th>Zeit</th>
					<th>Prüfung</th>
					<th>Aktualisiert</th>
					<th></th>
				</tr>
				<tr v-for="(event, index) in input.data" :key="index">
					<td>{{ event.vnr }}</td>
					<td class="list">{{ formatModules(event.modules) }}</td>
					<td>{{ event.title }}</td>
					<td class="list">{{ formatPeople(event.people) }}</td>
					<td>{{ event.type }}</td>
					<td>{{ event.sws }}</td>
					<td>{{ event.field_of_studies.join(", ") }}</td>
					<td class="extra">{{ event.extra }}</td>
					<td class="extra">{{ event.room }}</td>
					<td class="extra">{{ event.time }}</td>
					<td class="extra">{{ event.exam }}</td>
					<td>{{ convertTime(event.updated_at) }}</td>
					<td class="center"><router-link class="edit" :to="{
						name: 'Bearbeiten',
						query: {
							ref: event.id
						}
					}"><i class="fa fa-pencil" style="font-size:24px" /></router-link></td>
				</tr>
			</table>
		</div>
	</div>
</template>

<script setup>
import { onMounted, reactive } from "vue";
import moment from "moment";
import search from "@/services/SearchService.js";
import helper from "@/services/HelperService.js";
import rs from '@/services/RequestService.js';


const input = reactive({
	data: [],
});

let semesterList = [20241, 20240, 20231, 20230, 20221, 20220];

const semester = reactive({
	list: semesterList,
	value: semesterList[0]
});

onMounted(() => {
	getVeranstaltungen(semester.value);
});

async function getVeranstaltungen(semester) {
	input.data = await search.getNewEntries(semester);
	input.data = input.data.data;
}

function formatModules(modules) {
	let modulePnrs = [];
	modules.forEach((module) => {
		let modulePnrString = module.code + "/" + module.pivot.pnr;
		modulePnrs.push(modulePnrString);
	});

	return modulePnrs.join("\n");
}

function formatPeople(people) {
	let peopleDisplayname = [];
	people.forEach((person) => {
		let peopleName = person.surname + ", " + person.forename;
		peopleDisplayname.push(peopleName);
	});

	return peopleDisplayname.join("\n");
}

function convertTime(time) {
	let timestamp = new Date(time);

	return moment(timestamp).format('DD.MM.YYYY, HH:mm');;
}


async function download() {
	await rs.csrf();
	let data = await rs.get("export", {
		params: {
			semester: semester.value,
		}
	});

	if (data.status != 200) {
		return;
	}

	var link = document.createElement("a");
	link.download = data.data["filename"];
	link.href = "https://lehre.fmi.uni-jena.de/" + data.data["path"];
	document.body.appendChild(link);
	link.click();
	document.body.removeChild(link);
}


</script>

<style lang="scss" scoped>
#export-container {
	#download-bar {
		background-color: $color2_light;
		color: white;

		#download-button {
			color: white;
			background-color: $color2;
			padding: 0.5rem;
			margin: 0.5rem 1rem;
			border-radius: 0;
			border: 1px solid $border-color;

			&:hover {
				cursor: pointer;
				background-color: $color2_dark;
			}
		}

		.selection {
			border: 1px solid $border-color;
			height: 2rem;
			font-size: 0.9rem;
			padding: 0 0.5rem;
		}
	}

	#table-container {
		overflow: auto;
		height: calc(100vh - 15.9rem);

		#event-table {
			text-align: left;
			border-collapse: collapse;
			width: 100%;

			tr {

				th,
				td {
					vertical-align: top;
					border-bottom: 1px solid #ddd;
					padding: 0.5rem;
				}

				th {
					background-color: $color2_light;
					color: white;
					padding: 1rem;

					position: sticky;
					top: 0;
				}

				.list {
					white-space: pre;
				}

				.edit {
					text-align: center;
					text-decoration: none;
					color: black;
				}

				.semester {
					min-width: max-content;
				}

				.extra {
					max-width: 30rem;
				}

				&:nth-child(even) {
					background-color: #f2f2f2;
				}

				&:hover {
					background-color: #d9d9d9;
				}
			}
		}
	}
}

.box {
	border: 1px solid black;
}

table,
td,
th,
tr {
	padding: 0;
}
</style>