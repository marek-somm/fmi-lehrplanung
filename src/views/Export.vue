<template>
	<div class="results" v-if="input.data">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <div class="item" v-for="(item, semester, index) in input.data.data" :key="index">
            <table :id="semester">
        
            <!-- für jedes semester eigene tabelle -->
            <caption><button class="export" @click="exportTableToExcel(semester, semester)">Semester: {{semester}} <i class="fa fa-download"></i></button></caption>
            
            <tr>
            <th>Übetragen</th><th>LV Nr.</th><th>Modul Nr.</th><th>Prüfungs Nr.</th><th>Titel</th><th>Lehrende</th><th>Art</th><th>SWS</th><th>Zielgruppe</th>
            </tr>
            <tbody v-for="(prüfung, key, index) in item" :key="index">
                <tr v-for="(item, key, index) in prüfung.veranstaltungen" :key="index">
                <td><button class="übertragen" @click="toggleÜbertragen(item.veranstaltungsnummer, semester, prüfung.Modulcode, prüfung.pnr)">{{ item.uebertragen ? "X" : "" }}</button></td><td>{{item.veranstaltungsnummer}}</td>
                <td :rowspan="prüfung.veranstaltungen.length" v-if="key==0">{{prüfung.Modulcode}}</td><td :rowspan="prüfung.veranstaltungen.length" v-if="key==0">{{prüfung.pnr}}</td>
                <td>{{item.titel}}</td>
                <td><p v-for="(item, semester, index) in item.lehrpersonal" :key="index"><a style="color:#2c3e50; text-decoration:none"
					:href="
						'https://friedolin.uni-jena.de/qisserver/rds?state=verpublish&moduleCall=webInfo&publishConfFile=webInfoPerson&publishSubDir=personal&personal.pid=' +
						item.friedolinID
					"
					target="_blank">
                {{item.vorname}} {{item.nachname}}
                </a></p></td>
                <td>{{item.art}}</td><td>{{item.sws}}</td><td>{{item.Zielgruppe}}</td>

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
            input.data = await rq.getNewVeranstaltungen()
        }

        var conf = false;
		function toggleÜbertragen(vnr, sem, modulcode, pnr) {
            if(!conf) {
                conf = confirm("Wollen Sie die Veranstaltung wirklich als übernommen markieren?\nSie ist dann nicht mehr in dieser Ansicht sichtbar!\n\nDiese Meldung erscheint nur ein einziges mal!")
            }
            if(conf) {
                rq.toggleAktiv(vnr, convertSemester(sem), modulcode, pnr)
            }
		}
        function convertSemester(str) {
            if(str.includes("WiSe")) {
                str = str.replace("WiSe ", "")
                str += "1"
            } else {
                str = str.replace("SoSe ", "")
                str += "0"
            }
            return Number(str)
        }
        function exportTableToExcel(tableID, filename = ''){
            var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            var tableSelect = document.getElementById(tableID);
            var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
            
            // Specify file name
            filename = filename?filename+'.xlsx':'excel_data.xlsx';
            
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