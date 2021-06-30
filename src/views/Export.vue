<template>
	<div class="results" v-if="input.data">
        <div class="item" v-for="(item, semester, index) in input.data.data" :key="index">
            <table :id="semester">
        
            <!-- für jedes semester eigene tabelle -->
            <caption><button class="export" @click="exportTableToExcel(semester, semester)">Semester: {{semester}}</button></caption>
            
            <tr>
            <th>Friedolin</th><th>LV Nr.</th><th>Modul Nr.</th><th>Prüfüngs Nr.</th><th>Titel</th><th>Dozent</th><th>Art</th><th>SWS</th><th>Zielgruppe</th>
            </tr>
            <tbody v-for="(prüfung, key, index) in item" :key="index">
                <tr v-for="(item, key, index) in prüfung.veranstaltungen" :key="index">
                <td><button class="übertragen" @click="toggleÜbertragen(item.veranstaltungsnummer, semester)">{{ item.übertragen ? "X" : "" }}</button></td><td>{{item.veranstaltungsnummer}}</td>
                <td :rowspan="prüfung.veranstaltungen.length" v-if="key==0">{{prüfung.Modulcode}}</td><td :rowspan="prüfung.veranstaltungen.length" v-if="key==0">{{prüfung.pnr}}</td>
                <td>{{item.titel}}</td><td>{{item.vorname}} {{item.nachname}}</td><td>{{item.art}}</td><td>{{item.sws}}</td><td>{{item.Zielgruppe}}</td>

                </tr>
            </tbody>
        </table>
        <br><br>
        </div>
    </div>
</template>

<script>
import { onMounted, reactive } from "vue";
import { request } from "@/scripts/request.js";
export default {
	setup() {
		const rq = new request();
		const input = reactive({
			data: {},
			});

		onMounted(() => {
			getVeranstaltungen();
		});

        async function getVeranstaltungen() {
            input.data = await rq.searchVeranstaltung()
        }

		function toggleÜbertragen(veranstaltungsnummer, semester) {
			rq.toggleAktiv(veranstaltungsnummer, semester)
		}
        function exportTableToExcel(tableID, filename = ''){
            var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            var tableSelect = document.getElementById(tableID);
            var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
            
            // Specify file name
            filename = filename?filename+'.xls':'excel_data.xls';
            
            // Create download link element
            downloadLink = document.createElement("a");
            
            document.body.appendChild(downloadLink);
            
            if(navigator.msSaveOrOpenBlob){
                var blob = new Blob(['\ufeff', tableHTML], {
                    type: dataType
                });
                navigator.msSaveOrOpenBlob( blob, filename);
            }else{
                // Create a link to the file
                downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
            
                // Setting the file name
                downloadLink.download = filename;
                
                //triggering the function
                downloadLink.click();
            }
        }
		return {
            input,
            toggleÜbertragen,
            exportTableToExcel
        }
    }
}

</script>

<style lang="scss" scoped>
.results{
    padding: 1em 1em;
    font-family: helvetica, sans-serif;
    height: calc(100vh - 16.78rem);
    overflow-y: auto;
    table, th, td {
        border: 1px solid black;
        // border-collapse: collapse;
        padding: 10px;
    }
	.übertragen{
		height:1.4em;
		width:1.8em;
	}
    .export{
			width: 25%;
			margin: 0 3% 1em;
			font-size: 1.2em;
			border: 2px solid #000;
			color: #2c3e50;
			transition: box-shadow 0.5s ease, background 0.2s ease;

			&:hover {
				box-shadow: rgba(0, 0, 0, 0.349) 3px 3px;
				background: rgb(201, 201, 201);
			}
		}
/* Hide scrollbar for IE, Edge and Firefox */
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}

/* Hide scrollbar for Chrome, Safari and Opera */
.results::-webkit-scrollbar {
    display: none;
}
</style>