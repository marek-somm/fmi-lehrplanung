<template>
	<div class="export--container">
		<div v-for="(module, index) in input.data.data" :key="index">
			<div class="grid">
				<div class="item title">LV Nr.</div>
				<div class="item title">Modul Nr.</div>
				<div class="item title">Prüf. Nr.</div>
				<div class="item title">Titel</div>
				<div class="item title">Art</div>
				<div class="item title">SWS</div>
				<div
					:style="'grid-area: ' + (index + 2) + ' / 1 / span 1'"
					v-for="(event, index) in getAllEvents(module)"
					:key="index"
					class="item"
				>
					{{ event.vnr }}
				</div>
				<div
					:style="'grid-area: 2 / 2 / span ' + getAllEvents(module).length"
					class="item"
				>
					{{ module.module }}
				</div>
				<div
					:style="'grid-area: auto / 3 / span ' + pnr.events.length"
					v-for="(pnr, index) in module.pnr"
					:key="index"
					class="item"
				>
					{{ pnr.pnr }}
				</div>
				<div
					:style="'grid-area: ' + (index + 2) + ' / 4 / span 1'"
					v-for="(event, index) in getAllEvents(module)"
					:key="index"
					class="item"
				>
					{{ event.title }}
				</div>
				<div
					:style="'grid-area: ' + (index + 2) + ' / 5 / span 1'"
					v-for="(event, index) in getAllEvents(module)"
					:key="index"
					class="item"
				>
					{{ event.type }}
				</div>
				<div
					:style="'grid-area: ' + (index + 2) + ' / 6 / span 1'"
					v-for="(event, index) in getAllEvents(module)"
					:key="index"
					class="item"
				>
					{{ event.sws }}
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { onMounted, reactive } from "vue";
import search from "@/services/SearchService.js";

export default {
	setup() {
		const input = reactive({
			data: {},
		});

		onMounted(() => {
			getVeranstaltungen();
		});

		async function getVeranstaltungen() {
			input.data = await search.getNewEntries();
		}

		var conf = false;
		async function toggleÜbertragen(vnr, sem, modulcode, pnr) {
			if (!conf) {
				conf = confirm(
					"Wollen Sie die Veranstaltung wirklich als übernommen markieren?\nSie ist dann nicht mehr in dieser Ansicht sichtbar!\n\nDiese Meldung erscheint nur ein einziges mal!"
				);
			}
			if (conf) {
				await search.toggleAktiv(vnr, convertSemester(sem), modulcode, pnr);
				getVeranstaltungen();
			}
		}
		function convertSemester(str) {
			if (str.includes("WiSe")) {
				str = str.replace("WiSe ", "");
				str += "1";
			} else {
				str = str.replace("SoSe ", "");
				str += "0";
			}
			return Number(str);
		}
		function exportTableToExcel(tableID, filename = "") {
			var downloadLink;
			var dataType = "application/vnd.ms-excel";
			var tableSelect = document.getElementById(tableID);
			var tableHTML = tableSelect.outerHTML.replace(/ /g, "%20");

			// Specify file name
			filename = filename ? filename + ".xlsx" : "excel_data.xlsx";

			// Create download link element
			downloadLink = document.createElement("a");

			document.body.appendChild(downloadLink);

			if (navigator.msSaveOrOpenBlob) {
				var blob = new Blob(["\ufeff", tableHTML], {
					type: dataType,
				});
				navigator.msSaveOrOpenBlob(blob, filename);
			} else {
				// Create a link to the file
				downloadLink.href = "data:" + dataType + ", " + tableHTML;

				// Setting the file name
				downloadLink.download = filename;

				//triggering the function
				downloadLink.click();
			}
		}

		function getAllEvents(module) {
			let result = [];
			module.pnr.forEach((arr) => {
				let res = arr.events;
				result = [...result, ...res];
			});
			return result;
		}

		return {
			input,
			toggleÜbertragen,
			exportTableToExcel,
			getAllEvents,
		};
	},
};
</script>

<style lang="scss" scoped>
.export--container {
	.grid {
		display: grid;
		grid-template-columns: auto auto auto auto auto auto;
		border: 1px solid black;
		margin-bottom: 2rem;
		margin: 2rem;

		.item {
			border: 1px solid black;
			line-height: 20px;
			text-align: left;
			vertical-align: middle;
			padding: 0.2rem;
		}

		.title {
			margin-bottom: 0.5rem;
			background: lightgray;
			font-weight: bold;
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
	margin: 0;
	padding: 0;
	border-spacing: 0;
}
</style>