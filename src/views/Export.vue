<template>
	<div class="results" v-if="input.data">
        <table class="item" v-for="(item, semester, index) in input.data.data" :key="index">
            <!-- für jedes semester eigene tabelle -->
            <caption>Semester: {{semester}}</caption>
            <tr>
            <th>Friedolin</th><th>LV Nr.</th><th>Modul Nr.</th><th>Prüfüngs Nr.</th><th>Titel</th><th>Dozent</th><th>Art</th><th>SWS</th><th>Zielgruppe</th>
            </tr>
            <tbody v-for="(prüfung, key, index) in item" :key="index">
                <tr v-for="(item, key, index) in prüfung.veranstaltungen" :key="index">
                <td><button class="übertragen" @click="toggleÜbertragen(item.veranstaltungsnummer, semester)">{{ item.übertragen ? "X" : "" }}</button></td><td>{{item.veranstaltungsnummer}}</td>
                <!-- Da es nicht möglich ist bei rowspan eine Variable anzugeben, abfrage der Länge -->
                <td v-if="prüfung.veranstaltungen.length==1 || prüfung.veranstaltungen.length>4">{{prüfung.Modulcode}}</td><td v-if="prüfung.veranstaltungen.length==1 || prüfung.veranstaltungen.length>4">{{prüfung.pnr}}</td>
                <td rowspan="2" v-if="key==0 && prüfung.veranstaltungen.length==2">{{prüfung.Modulcode}}</td><td rowspan="2" v-if="key==0 && prüfung.veranstaltungen.length==2">{{prüfung.pnr}}</td>
                <td rowspan="3" v-if="key==0 && prüfung.veranstaltungen.length==3">{{prüfung.Modulcode}}</td><td rowspan="3" v-if="key==0 && prüfung.veranstaltungen.length==3">{{prüfung.pnr}}</td>
                <td rowspan="4" v-if="key==0 && prüfung.veranstaltungen.length==4">{{prüfung.Modulcode}}</td><td rowspan="4" v-if="key==0 && prüfung.veranstaltungen.length==4">{{prüfung.pnr}}</td>
                <td>{{item.titel}}</td><td>{{item.vorname}} {{item.nachname}}</td><td>{{item.art}}</td><td>{{item.sws}}</td><td>{{item.Zielgruppe}}</td>

                </tr>
            </tbody>
        </table>
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
		return {
            input,
            toggleÜbertragen
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
        border-collapse: collapse;
  padding: 10px;
    }
	.übertragen{
		height:1.4em;
		width:1.8em;
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