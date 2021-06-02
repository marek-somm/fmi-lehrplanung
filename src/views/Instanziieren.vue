<template>
	<div class="results" v-if="data.veranstaltung">
	<form class="veranstaltung_input" @submit.prevent="übernehmen()">
			<!-- <p>{{data.veranstaltung}}</p> -->
			<h1>Veranstaltung</h1>
			<!-- <p>{{data.veranstaltung.data}}</p> -->
			<div class="modul-item" v-for="(item, key, index) in ret.data" :key="index">
			<div v-if="!(key == 'friedolinID') && !(key == 'aktiv')">
			<!-- <p>key: {{key}} item: {{item}} index: {{index}}</p> -->
			<label :for=key>
				<strong>{{key}}:</strong>
			</label>
			<input
				:id=key
				:name=key
				:placeholder=item
				:v-model=input[key]
			/>
			</div></div>
			<h1>Inhalt</h1>
			<!-- <p>{{data.veranstaltung.content}}</p> -->
			<div class="modul-item" v-for="(item, key, index) in ret.content" :key="index">
			<!-- <p>key: {{key}} item: {{item}} index: {{index}}</p> -->
			<label :for=key>
				<strong>{{key}}:</strong>
			</label>
			<input
				:id=key
				:name=key
				:placeholder=item
				:v-model=input[key]
			/>
			<p>{{item}}</p>
			</div>
			<h1>Personen</h1>
			<!-- <p>{{data.veranstaltung.people}}</p> -->
			<div class="modul-item" v-for="(item, key, index) in ret.people" :key="index">
				<h1>{{key}}</h1>
				<p>key: {{key}} item: {{item}} index: {{index}}</p>
			<div v-for="(item, key, index) in item" :key="index">
			<div v-for="(item, key, index) in item" :key="index">
					<label :for=key>
						<strong>{{key}}:</strong>
					</label>
					<input
						:id=key
						:name=key
						:placeholder=item
						:v-model=input[key]
					/>
			</div>
			</div>
			</div>
			<h1>Prüfungen</h1>
			<!-- <p>{{data.veranstaltung.exams}}</p> -->
			<div class="modul-item" v-for="(item, key, index) in ret.exams" :key="index">
			<p>key: {{key}} item: {{item}} index: {{index}}</p>
			<div v-for="(item, key, index) in item" :key="index">
			<label :for=key>
				<strong>{{key}}:</strong>
			</label>
			<input
				:id=key
				:name=key
				:placeholder=item
				:v-model=input[key]
			/>
			</div>
			</div>
		<br /><br />
		<button>
			<strong>Übernehmen</strong>
		</button>
	</form>
	</div>
</template>
<script>
import { onMounted, reactive, watch } from "vue";
import { useRoute } from "vue-router";
import { request } from "@/scripts/request.js";

export default {
	setup() {
		const rq = new request();
		const input = reactive({});
		const ret = reactive({
			data:{
				titel: "Der Titel der Veranstaltung",
				veranstaltungsnummer: "Die Nummer der Veranstaltung",
				semester: "Das Anfangsjahr des Semester mit 0 für SoSe und 1 für WiSe",
				friedolinID: null,
				aktiv: "0",
				sws: "Anzahl der Semester Wochen Stunden",
				turnus: "Wie oft wird diese LV angeboten",
				art: "Vorlesung, Übung, Seminar, etc.",
			},
			content:{
				Kommentar: "Wichtige Informationen für die Studenten",
				Literatur: "Emfohlene Literatur",
				Bemerkung: "Weniger wichtige Informationen für die Studenten",
				Zielgruppe: "Studiengänge an die sich die LV richtet",
				Lerninhalte: "Was soll in der LV vermittelt werden",
				Leistungsnachweis: "Welche Prüfungsform ist geplant"
			},
			person:{
				Verantwortlich: [{
					vorname:"",
					nachname:"",
					grad:"",
					rolle:"verantwortlich",
					friedolinID:""
				},
				{
					vorname:"",
					nachname:"",
					grad:"",
					rolle:"verantwortlich",
					friedolinID:""
				}],
				Begleitend: [{
					vorname:"",
					nachname:"",
					grad:"",
					rolle:"begleitend",
					friedolinID:""
				},
				{
					vorname:"",
					nachname:"",
					grad:"",
					rolle:"begleitend",
					friedolinID:""
				}],
				Organisatorisch: [{
					vorname:"",
					nachname:"",
					grad:"",
					rolle:"organisatorisch",
					friedolinID:""
				},
				{
					vorname:"",
					nachname:"",
					grad:"",
					rolle:"organisatorisch",
					friedolinID:""
				}]
			},
			exams:[
				{ "pnr": 0, "modulcode": "", "titel": "" },
				{ "pnr": 0, "modulcode": "", "titel": "" },
				{ "pnr": 0, "modulcode": "", "titel": "" },
				{ "pnr": 0, "modulcode": "", "titel": "" },
			]
		});
		const data = reactive({
			veranstaltung: null,
			modul: null
			});

        const route = useRoute();
        const id = route.params.id;
		const sem = route.params.sem;
		console.log(id);
		console.log(sem);
		watch(
			() => id,
			() => sem,
			() => {
				getModul(id, sem);
			}
		);

		onMounted(() => {
			getModul(id, sem);
		});
		function isNumber(n) {
			return !isNaN(parseFloat(n)) && isFinite(n);
		}

		async function getModul(id, sem) {
			console.log(ret.data)
			if (isNumber(id)){
				console.log("Veranstaltung")
				data.veranstaltung = await rq.getVeranstaltung(id, sem);
				for (var key in data.veranstaltung){
					console.log("key ", key)
					if (!(key == "people")){
						for (var entry in data.veranstaltung[key]){
							console.log("eintrag ",entry)
							if (data.veranstaltung[key][entry] && !(entry == "friedolinID")){
								console.log("data",data.veranstaltung[key][entry])
								ret[key][entry] = data.veranstaltung[key][entry]
							}
						}
					}
					else{
						// if ("Verantwortlich" in data.veranstaltung[key] && data.veranstaltung[key]["Verantwortlich"]){
						// 	console.log("data1",data.veranstaltung[key]["Verantwortlich"])
						// 	ret[key]["Verantwortlich"] = data.veranstaltung[key]["Verantwortlich"]
						// }
						// if (data.veranstaltung[key]["Begleitend"]){
						// 	ret[key]["Begleitend"] = data.veranstaltung[key]["Begleitend"]
						// }
						// if (data.veranstaltung[key]["Organisatorisch"]){
						// 	ret[key]["Organisatorisch"] = data.veranstaltung[key]["Organisatorisch"]
						// }
						ret[key] = data.veranstaltung[key]
					}
					
				}
				// data.veranstaltung.forEach(element => ret[element])
				// console.log(data.veranstaltung)
				// console.log(ret.data)
			}
			else{
				console.log("Modul")
				data.modul = await rq.getModul(id);
				console.log(data.modul)
			}
		}

		async function übernehmen() {
			console.log("Ret")
			for (var key in ret){
				console.log(ret[key])
			}
			console.log("Input")
			for (var entry in input){
				console.log(input[entry])
			}
		}

		return {
			data,
            id,
			input,
			ret,
			übernehmen
		};
	}
};
</script>

<style lang="scss" scoped>
.veranstaltung_input {
	padding: 2em 1em;
	font-family: helvetica, sans-serif;

	label {
		color: #2c3e50;
		margin: 0 3% 0.25em;
		font-size: 1.2em;
		display: block;
		font-family: helvetica, sans-serif;
	}

	input {
		width: 94%;
		padding: 0.5em 0.25em;
		margin: 0 3% 1em;
		font-size: 1.2em;
		border: 2px solid #000;
		outline: none;
		color: #2c3e50;
	}

	button {
		width: 25%;
		padding: 0.5em 0.25em;
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
}
</style>
