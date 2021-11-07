<template>
	<div class="new-container">
		<h3>Veranstaltung</h3>
		<div>
			<label>Titel </label>
			<input />
		</div>
		<div>
			<label>SWS </label>
			<input />
		</div>
		<div>
			<label>Turnus </label>
			<input>
		</div>
		<div>
			<label>Art </label>
			<input>
		</div>
		<div>
			<label>Zielgruppe </label>
			<input>
		</div>

		<h3>Personen</h3>
		<div>
			Person A
			<button>X</button>
		</div>
		<div>
			Person B
			<button>X</button>
		</div>
		<div>
			<input>
		</div>

		<h3>Prüfungen</h3>
		<div>
			<label>Titel </label>
			<input>
		</div>
		<div>
			<label>Prüfungsnummer </label>
			<input>
		</div>
		<div>
			<label>Modulecode </label>
			<input>
		</div>
		<div>
			<label>Beschreibung </label>
			<input>
		</div>
		<br>
		<div>
			<label>Titel </label>
			<input>
		</div>
		<div>
			<label>Prüfungsnummer </label>
			<input>
		</div>
		<div>
			<label>Modulecode </label>
			<input>
		</div>
		<div>
			<label>Beschreibung </label>
			<input>
		</div>
	</div>
</template>

<script>
import { onMounted, reactive } from "vue";
import { useRoute } from "vue-router";
import search from "@/services/SearchService.js";

export default {
	setup() {
		const data = reactive({

		})

		const route = useRoute();
		const vnr = route.params.vnr;
		const sem = route.params.sem;

		onMounted(() => {
			getVeranstaltung(vnr, sem);
		});

		async function getVeranstaltung(vnr, sem) {
			if(!vnr) {
				vnr = 0
			}
			if(!sem) {
				sem = 0
			}
			return await search.getEvent(vnr, sem)
		}

		return {
			data,
		};
	},
};
</script>

<style lang="scss" scoped>
// TODO: rechts und unten ist immer noch ein ungewollter abstand
.new-container {
	text-align: left;
	margin: 0.5rem;

	div {
		margin: 0.3rem;
	}
}

.results {
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
		.veranstaltung {
			width: 30%;
			margin-right: 3%;
			float: left;
			.Gruppen {
				height: calc(100vh - 28rem);
				overflow-y: auto;
				/* Hide scrollbar for IE, Edge and Firefox */
				-ms-overflow-style: none; /* IE and Edge */
				scrollbar-width: none; /* Firefox */
			}
			/* Hide scrollbar for Chrome, Safari and Opera */
			.Gruppen::-webkit-scrollbar {
				display: none;
			}

			input {
				width: 90%;
			}

			button {
				font-size: 1em;
				width: 44%;
			}
		}
		.veranstaltung:last-child {
			float: none;
			margin-right: 0;
		}
	}
}
</style>
