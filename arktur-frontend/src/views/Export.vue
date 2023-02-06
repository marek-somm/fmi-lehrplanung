<template>
	<div id="export-container">
		<table id="event-table">
			<tr>
				<th>Vorlesungsnr.</th>
				<th>Modul/Prüfung</th>
				<th>Semester</th>
				<th>Titel</th>
				<th>SWS</th>
				<th>Typ</th>
				<th>Extra</th>
				<th>Aktualisiert</th>
			</tr>
			<tr v-for="(event, index) in input.data" :key="index">
				<td>{{ event.vnr }}</td>
				<td class="modules">{{ formatModules(event.modules) }}</td>
				<td class="semester">{{ helper.convertSemester(event.semester) }}</td>
				<td>{{ event.title }}</td>
				<td>{{ event.sws }}</td>
				<td>{{ event.type }}</td>
				<td class="extra">{{ event.extra }}</td>
				<td>{{ convertTime(event.updated_at) }}</td>
			</tr>
		</table>
	</div>
</template>

<script>
import { onMounted, reactive } from "vue";
import moment from "moment"
import search from "@/services/SearchService.js";
import helper from "@/services/HelperService.js";

export default {
	setup() {
		const input = reactive({
			data: [],
		});

		onMounted(() => {
			getVeranstaltungen();
		});

		async function getVeranstaltungen() {
			input.data = await search.getNewEntries();
			input.data = input.data.data;
		}

		function formatModules(modules) {
			let modulePnrs = [];
			modules.forEach((module) => {
				let modulePnrString = module.code + "/" + module.pivot.pnr;
				modulePnrs.push(modulePnrString);
			});

			return modulePnrs.join(" ");
		}

		function convertTime(time) {
			let timestamp = new Date(time);
			
			return moment(timestamp).format('DD.MM.YYYY, HH:mm');;
		}

		return {
			input,
			formatModules,
			convertTime,
			helper
		};
	},
};
</script>

<style lang="scss" scoped>
#export-container {
	height: calc(100vh - 13rem);
	overflow: auto;

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

			.modules {
				max-width: 15rem;
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