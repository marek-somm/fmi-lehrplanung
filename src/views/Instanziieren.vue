<template>
	<div class="results" v-if="input.original">
	<form class="veranstaltung_input" @submit.prevent="übernehmen()">
			<!-- <p>{{input.original}}</p> -->
			<!-- <p>{{data.veranstaltung.data}}</p> -->
			<div class="modul-item" v-for="(inhalt, category, index) in input.original" :key="index">
				<h1>{{category}}</h1>
				<!-- key: {{category}}
				item: {{inhalt}} -->
				<div class="modul-item" v-for="(item, key, index) in inhalt" :key="index">
					<div v-if="!(category == 'exams') && !(category == 'people')">
						<!-- <p>key: {{key}} item: {{item}} index: {{index}}</p> -->
						<!-- <p>mau</p> -->
						<label :for=key><strong>{{key}}:</strong></label>
						<input
							:id=key
							:name=key
							:placeholder=item
							v-model=out.input[category][key]
						/>
					</div>
					<div v-if="(category == 'people') ||(category == 'exams')">
						<div class="modul-item" v-for="(item, number, index) in item" :key="index">
							<!-- <p>key: {{number}} item: {{item}} index: {{index}}</p> -->
							<h2>{{category}}: {{number}}</h2>
							<!-- <p>mau</p> -->
							<div class="modul-item" v-for="(item, key, index) in item" :key="index">
								<label :for=key><strong>{{key}}:</strong></label>
								<input
									:id=key
									:name=key
									:placeholder=item
									v-model=out.input[category][number][key]
								/>
							</div>
						</div>
					</div>
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
		const input = reactive({
			original: {},
			});
		const out = reactive({
			input: {},
			return: {},
			});

        const route = useRoute();
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
			// console.log("data", input.original)
			// console.log("input", out.input)
			// console.log("return", out.return)
			input.original = await rq.getVeranstaltung(id, sem);
			// übernimmt data für input mit ausschließlich dict
			for (var key in input.original){
				if (key == "people" || key == "exams"){
					out.input[key] = {}
					// console.log("len", input.original[key])
					for (var i in input.original[key][""]){
						// console.log(i)
						out.input[key][i] = {}
						// console.log("liste", i, out.input[key])
					}
					// console.log("verschachtelt")
				}
				else{
					out.input[key] = {}
					// console.log("einfach", out.input)
				}
			}
			// übernimmt data für return ohne format zu ändern
            for (key in input.original){
                out.return[key] = input.original[key]
            }
			console.log("data", input.original)
			console.log("input", out.input)
			console.log("return", out.return)
		}

		async function übernehmen() {
			// übernimm nicht leere input datan für return
			// wandel dabei in format von data zurück
			for (var entry in out.input){
				if (entry == "people" || entry == "exams"){
					for (var i in out.input[entry]){
						for (var value in out.input[entry][i])
						if (out.input[entry][i][value]){
							console.log("data", input.original[entry][""][i])
							console.log("return", out.return[entry][""][i])
							out.return[entry][""][i][value] = out.input[entry][i][value]
							console.log("data", input.original[entry][""][i])
							console.log("return", out.return[entry][""][i])
							console.log("input", out.input[entry][i])
						}
					}
				}
				else{
					if (out.input[entry]){
						out.return[entry] = out.input[entry]
					}	
				}
			}
		}

		return {
			input,
			out,
            id,
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
