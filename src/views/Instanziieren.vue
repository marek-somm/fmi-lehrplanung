<template>
	<div class="results" v-if="data.modul">
	<!-- <div>
		<h1>Login</h1>
	</div> -->
	<form class="modul_imput" @submit.prevent="übernehmen()">
		<label for="name">
			<strong>Titel:</strong>
		</label>
		<input
			id="name"
			name="name"
			:placeholder=data.modul.TitelDE
			v-model="input.name"
		/>
		<label for="zielgruppe">
			<strong>Zielgruppe:</strong>
		</label>
		<input
			id="zielgruppe"
			name="zielgruppe"
			:placeholder=data.modul.Art
			v-model="input.type"
		/>
		<label for="lp">
			<strong>LP:</strong>
		</label>
		<input
			id="lp"
			name="lp"
			:placeholder=data.modul.LP
			v-model="input.lp"
		/>
		<label for="sws">
			<strong>Workload:</strong>
		</label>
		<input
			id="sws"
			name="sws"
			:placeholder=data.modul.Workload
			v-model="input.sws"
		/>
		<label for="info">
			<strong>Zusammensetzung:</strong>
		</label>
		<input
			id="info"
			name="info"
			:placeholder=data.modul.Zusammensetzung
			v-model="input.info"
		/>
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
			name: "",
			type: "",
			lp: "",
			sws: "",
			info: ""
		});
		const ret = reactive({
			name: "",
			type: "",
			lp: "",
			sws: "",
			info: ""
		});
		const data = reactive({
			loading: false,
			modul: null
		});
        const route = useRoute();
        const id = route.params.id;
		watch(
			() => id,
			() => {
				getModul(id);
			}
		);

		onMounted(() => {
			getModul(id);
		});

		async function getModul(modulcode) {
			data.modul = await rq.getModul(modulcode);
			data.modul = data.modul[0]
		}

		async function übernehmen() {
			if (input.name){
				ret.name = input.name
			}
			else{
				ret.name = data.modul.TitelDE
			}
			if (input.type){
				ret.type = input.type
			}
			else{
				ret.type = data.modul.Art
			}
			if (input.lp){
				ret.lp = input.lp
			}
			else{
				ret.lp = data.modul.LP
			}
			if (input.sws){
				ret.sws = input.sws
			}
			else{
				ret.sws = data.modul.Workload
			}
			if (input.info){
				ret.info = input.info
			}
			else{
				ret.info = data.modul.Zusammensetzung
			}
			console.log(ret)
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
.modul_imput {
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
		width: 25%;
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
