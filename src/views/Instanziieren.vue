<template>
	<div class="results" v-if="input.original">
	<form class="veranstaltung_input" @submit.prevent="">
		<div class="veranstaltung">
			<h1>Veranstaltung</h1>
			<div class="Gruppen">
				<div class="item" v-for="(item, key, index) in input.original.data" :key="index">
					<div v-if="level == 2 || !(key == 'aktiv') && !(key == 'friedolinID') && !(key == 'veranstaltungsnummer') && !(key == 'turnus')">
						<label :for=key><strong>{{beschriftung[key]}}:</strong></label>
						<input
							:id=key
							:name=key
							:placeholder=item
							v-model=out.input.data[key]
						/>
					</div>
				</div>
				<div class="item" v-for="(item, key, index) in input.original.content" :key="index">
					<label :for=key><strong>{{beschriftung[key]}}:</strong></label>
					<input
						:id=key
						:name=key
						:placeholder=item
						v-model=out.input.content[key]
					/>
				</div>
			</div>
		</div>
		<div class="veranstaltung">
			<h1>Personen</h1>
			<div class="Gruppen">
				<div class="item" v-for="(item, number, index) in input.original.people" :key="index">
					<div class="item" v-for="(item, number, index) in item" :key="index">
						<h2>{{number+1}}. Person</h2>

						<div class="item" v-for="(item, key, index) in item" :key="index">
							<div v-if="level == 2 || !(key == 'grad') && !(key == 'friedolinID')">
								<label :for=key+number><strong>{{beschriftung[key]}}:</strong></label>
								<input
									:id=key+number
									:name=key+number
									:placeholder=item
									v-model='out.input.people[""][number][key]'
								/>
							</div>
						</div>
					</div>
				</div>
			</div>
			<button class="new button" @click="newInstance('people')">Hinzufügen</button>
			<button class="new button" @click="removeInstance('people')">Entfernen</button>
		</div>
		<div class="veranstaltung">
			<h1>Prüfungen</h1>
			<div class="Gruppen">
				<div class="item" v-for="(item, number, index) in input.original.exams" :key="index">
					<div class="item" v-for="(item, number, index) in item" :key="index">
						<h2>{{number+1}}. Prüfung</h2>
						<div class="item" v-for="(item, key, index) in item" :key="index">
							<div v-if="level == 2 || !(key == 'titel') && !(key == 'pnr')">
								<label :for=key+number><strong>{{beschriftung[key]}}:</strong></label>
								<input
									:id=key+number
									:name=key+number
									:placeholder=item
									v-model='out.input.exams[""][number][key]'
								/>
							</div>
						</div>
					</div>
				</div>
			</div>
			<button class="new button" @click="newInstance('exams')">Hinzufügen</button>
			<button class="new button" @click="removeInstance('exams')">Entfernen</button>
		</div>
		<button class="succ button" @click="übernehmen()">Übernehmen</button>
		<div class="message">
			{{out.message}}
		</div>
	</form>
	</div>
</template>

<script>
import { onMounted, reactive, watch, computed } from "vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";
import { request } from "@/scripts/request.js";

export default {
	setup() {
		const beschriftung = {
			"data": "Veranstaltung",
			"titel": "Titel",
			"veranstaltungsnummer": "Vnr",
			"semester": "Semester",
			"aktiv": "Aktiv",
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
		const store = useStore();
		const level = computed(() => store.state.User.level);
		const rq = new request();
		const input = reactive({
			original: {},
			});
		const out = reactive({
			input: {},
			return: {},
			message: ""
			});

        const route = useRoute();
        const id = route.params.id;
		const sem = route.params.sem;
		watch(
			() => id,
			() => sem,
			() => {
				getVeranstaltung(id, sem);
			}
		);

		onMounted(() => {
			getVeranstaltung(id, sem);
		});

		async function getVeranstaltung(id, sem) {
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
			var newPerson = false;
			for (var key in input.original){
				for (var key1 in input.original[key]){
					if (key1 != ""){
						if (out.input[key][key1])
							out.return[key][key1] = out.input[key][key1]
						else{if (level.value != 2 && key1 == "friedolinID")
							out.return[key][key1] = null
						else{if (level.value != 2 && key1 == "aktiv")
							out.return[key][key1] = 1
						else
							out.return[key][key1] = input.original[key][key1]
						}}
						console.log(key, key1, out.return[key][key1])
					}
					else{
						for (var i in input.original[key][key1]){
							for(var j in input.original[key][key1][i]){
								if (out.input[key][key1][i][j]){
									if (level.value != 2 && (j == "vorname" || j == "nachname")){
										newPerson = true
									}
									else{if (level.value != 2 && j == "Modulcode"){
										out.return[key][key1][i]["pnr"] = null
										out.return[key][key1][i]["titel"] = null
									}}
									out.return[key][key1][i][j] = out.input[key][key1][i][j]
								}
								else{if (level.value != 2 && newPerson && j == "friedolinID"){
									out.return[key][key1][i][j] = null
									out.return[key][key1][i]["grad"] = null
									newPerson = false
								}
								else
									out.return[key][key1][i][j] = input.original[key][key1][i][j]
								}
							}
							console.log(key, key1, out.return[key][key1][i])
						}
					}
				}
			}
			var ausgabecode = await rq.saveVeranstaltung(out.return)
			console.log(ausgabecode)
			if (ausgabecode.status == 0){
				out.message = "Lehrveranstaltung wurde erfolgreich angelegt.";
			}
			else{if(ausgabecode.status == 1){
				out.message = "Fehler: Es existiert bereits eine Lehrveranstaltung mit dieser Veranstaltungsnummer und Semester Kombination.";
			}else{
				out.message = "Fehler: Ein unbekannter Fehler ist aufgetreten, bitte melden Sie dies dem Prüfungsamt.";
			}}
			// Ausgabe gemäß ausgabecode
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
			übernehmen,
			level
		};
	}
};
</script>

<style lang="scss" scoped>
// TODO: rechts und unten ist immer noch ein ungewollter abstand
.results{
	.veranstaltung_input {
		padding: 1em 1em;
		font-family: helvetica, sans-serif;
		height: calc(100vh - 15.78rem);
		overflow-y: auto;

		label {
			color: #2c3e50;
			margin: 0 3% 0.25em;
			font-size: 1.2em;
			display: block;
		}
		input {
			width: 100%;
			margin: 0 3% 1em;
			font-size: 1.2em;
			border: 2px solid #000;
			outline: none;
			color: #2c3e50;
		}

		button {
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
		.veranstaltung{
			width:30%;
			margin-right: 3%;
			float: left; 
			.Gruppen{ 
				height: calc(100vh - 28rem);
				overflow-y: auto;
			}

			input {
				width: 90%;
			}

			button {
				font-size: 1em;
				width: 44%;
			}
		}
		.veranstaltung:last-child{
			float: none;
			margin-right: 0;
		}
	}
}
</style>
