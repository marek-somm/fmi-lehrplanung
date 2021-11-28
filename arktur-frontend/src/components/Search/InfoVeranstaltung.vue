<template>
	<div class="info--container" :class="{ filter: filterActive }">
		<div class="header" v-if="selected">
			<h3 class="title">{{ selected.data.content.title }}</h3>
			<button class="new button" @click="editInstance">Edit</button>
			<button class="new button" @click="newInstance">New</button>
			<button class="close button" @click="close">X</button>
		</div>
		<div class="info-content" v-if="selected">
			<h3><u>Informationen:</u></h3>
			<div class="block">
				<p class="attrib">
					Veranstaltungsnummer: {{ selected.data.content.vnr }}
				</p>
				<p class="attrib">
					Semester:
					{{ helper.convertSemester(selected.data.content.semester) }}
				</p>
			</div>
			<div class="block">
				<p class="attrib">
					Aktiv:
					<b>{{ selected.data.content.active ? "Ja" : "Nein" }}</b>
				</p>
				<p class="attrib">
					Turnus:
					{{ helper.convertTurnus(selected.data.content.rotation) }}
				</p>
				<p class="attrib">
					Veranstaltungsart: {{ selected.data.content.type }}
				</p>
			</div>
			<div class="block">
				<p class="attrib">
					Semester Wochenstunden (SWS):
					{{
						selected.data.content.sws
							? selected.data.content.sws
							: "Nicht angegeben"
					}}
				</p>
				<p class="attrib" v-if="selected.data.content.target">
					Zielgruppe: {{ selected.data.content.target }}
				</p>
			</div>
			<People :people="selected.data.people" />
			<h3><u>Module:</u></h3>
			<div
				class="block"
				v-for="(relation, index) in selected.data.modules"
				:key="index"
			>
				<div class="box hover" :key="index" @click="view(relation)">
					<p>
						<b>{{ relation.pivot.title }}</b>
					</p>
					<br />
					<p>Modulcode: {{ relation.modulecode }}</p>
					<p>Prüfungsnummer: {{ relation.pivot.pnr }}</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { onMounted } from "vue";
import { useRouter } from "vue-router";
import People from "./Info/People.vue";
import search from "@/services/SearchService.js";
import helper from "@/services/HelperService.js";

export default {
	components: { People },
	props: {
		selected: Object,
		filterActive: Boolean,
	},
	emits: ["close", "relation"],
	setup(props, { emit }) {
		const router = useRouter();

		onMounted(() => {
			window.addEventListener("keyup", function (event) {
				if (event.key === "Escape") {
					close();
				}
			});
		});

		function close() {
			emit("close");
		}

		function newInstance() {
			console.log(props.selected)
			router.push({
				name: "Instanziieren",
				params: {
					vnr: props.selected.data.content.vnr,
					sem: props.selected.data.content.semester,
				},
			});
		}

		function editInstance() {
			router.push({
				name: "Bearbeiten",
				params: {
					id: props.selected.data.veranstaltungsnummer,
					sem: props.selected.data.semester,
				},
			});
		}

		function toggleAktiv() {
			// speichern von !props.selected.data.aktiv in der datenbank
			search.toggleAktiv(
				props.selected.data.veranstaltungsnummer,
				props.selected.data.semester
			);
		}

		function view(relation) {
			emit("relation", relation);
		}

		return {
			helper,
			close,
			newInstance,
			editInstance,
			toggleAktiv,
			view,
		};
	},
};
</script>

<style lang="scss" scoped>
@import "info.scss";
.attrib {
	button {
		height: 1.4em;
		width: 1.8em;
	}
}
</style>