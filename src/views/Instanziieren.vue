<template>
	<div class="results" v-if="input.original">
	<form class="veranstaltung_input" @submit.prevent="">
			<!-- <p>{{input.original}}</p>
			{{out.return}} -->
			<div class="modul-item" v-for="(inhalt, category, index) in input.original" :key="index">
				<h1>{{beschriftung[category]}}</h1>
				<!-- key: {{category}}
				item: {{inhalt}} -->
				<div class="modul-item" v-for="(item, key, index) in inhalt" :key="index">
						<!-- <p>key: {{key}} item: {{item}} index: {{index}}</p> -->
					<div v-if="!(key == '')">
						<div v-if="!(key == 'aktiv') && !(key == 'friedolinID')">
						<label :for=key><strong>{{beschriftung[key]}}:</strong></label>
						<input
							:id=key
							:name=key
							:placeholder=item
							v-model=out.input[category][key]
						/>
						</div>
					</div>
					<div v-if="(key == '')">
						<!-- item:{{item}} -->
						<!-- {{item[""]}} -->
						<div class="modul-item" v-for="(item, number, index) in item" :key="index">
							<!-- <p>key: {{number}} item: {{item}} index: {{index}}</p> -->
							<h2>{{number+1}}. {{beschriftung[category].slice(0, -2)}}</h2>
							<div class="modul-item" v-for="(item, key, index) in item" :key="index">
								<label :for=key><strong>{{beschriftung[key]}}:</strong></label>
								<input
									:id=key
									:name=key
									:placeholder=item
									v-model='out.input[category][""][number][key]'
								/>
							</div>
						</div>
						<button class="new button" @click="newInstance(category)">Hinzufügen</button>
						<button class="new button" @click="removeInstance(category)">Entfernen</button>
					</div>
				</div>
			</div>
		<br /><br />
		<button class="new button" @click="übernehmen()">Übernehmen</button>
	</form>
	</div>
</template>
<script>
import { onMounted, reactive, watch } from "vue";
// import { useRoute } from "vue-router";
import { request } from "@/scripts/request.js";

export default {
	setup() {
		const beschriftung = {
			"data": "Veranstaltung",
			"titel": "Titel",
			"veranstaltungsnummer": "Vnr",
			"semester": "Semester",
			"sws": "SWS",
			"turnus": "Turnus",
			"art": "Art",
			"content": "Inhalt",
			"Zielgruppe":"Zielgruppe",
			"people": "Personen",
			"vorname": "Vorname",
			"nachname": "Nachname",
			"grad": "Akademischer Grad",
			"friedolinID": "FriedolinID",
			"exams": "Prüfungen",
			"pnr":"Pnr",
			"Modulcode": "Modulcode"
		}
		const rq = new request();
		const input = reactive({
			original: {},
			});
		const out = reactive({
			input: {},
			return: {},
			});

        // const route = useRoute();
        const id = route.params.id;
		const sem = route.params.sem;
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

		async function getModul(id, sem) {
			input.original = await rq.getVeranstaltung(id, sem);
			// baut für input und return die struktur von original nach
			// beides bleibt leer, da return bei übernahme überschrieben wird
			// original = {"d":{"t":"", "n":"",...}, "i":{"z":""}, "p":{"":[{"t":"","n":"", ...}, {...},...]}, "e":{"":[{"t":"","n":"", ...}, {...},...]}}
			for (var key in input.original){
				out.input[key] = {}
                out.return[key] = {}
				for (var key1 in input.original[key]){
					if (key1 != ""){
						out.input[key][key1] = ""
						out.return[key][key1] = ""
					}
					else{
						out.input[key][key1] = []
						out.return[key][key1] = []
						for (var i in input.original[key][key1]){
							out.input[key][key1].push({})
							out.return[key][key1].push({})
							for(var j in input.original[key][key1][i]){
								out.input[key][key1][i][j] = ""
								out.return[key][key1][i][j] = ""
							}
						}
					}
				}
			}
		}

		async function übernehmen() {
			// übernimm nicht leere input datan für return
			// wandel dabei in format von data zurück
			for (var key in input.original){
				for (var key1 in input.original[key]){
					if (key1 != ""){
						if (out.input[key][key1])
							out.return[key][key1] = out.input[key][key1]
						else
							out.return[key][key1] = input.original[key][key1]
						console.log("data", input.original[key][key1])
						console.log("return", out.return[key][key1])
						console.log("input", out.input[key][key1])
					}
					else{
						for (var i in input.original[key][key1]){
							for(var j in input.original[key][key1][i]){
								if (out.input[key][key1][i][j])
									out.return[key][key1][i][j] = out.input[key][key1][i][j]
								else
									out.return[key][key1][i][j] = input.original[key][key1][i][j]
							}
							console.log("data", input.original[key][key1][i])
							console.log("return", out.return[key][key1][i])
							console.log("input", out.input[key][key1][i])
						}
					}
				}
			}
		}
		function newInstance(category) {
			var dict = {}
			if (category == "people")
				dict = {"vorname":"", "nachname":"", "grad":"", "friedolinID":""}
			else if (category == "exams")
				dict = {"titel":"", "pnr":"", "Modulcode":""}
			input.original[category][""].push(dict)
			out.input[category][""].push(dict)
			out.return[category][""].push(dict)
		}
		function removeInstance(category) {
			input.original[category][""].pop()
			out.input[category][""].pop()
			out.return[category][""].pop()
		}

		return {
			input,
			out,
            id,
			beschriftung,
			newInstance,
			removeInstance,
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
		width: 50%;
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
