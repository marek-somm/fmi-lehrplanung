<template>
	<div class="info--container" :class="{ filter: filterActive }">
		<div class="header" v-if="selected">
			<h3 class="title">{{ selected.data.content.title }}</h3>
			<div class="button-bar">
				<router-link class="new button" v-if="user.login" :to="{
					name: 'Neu',
					query: {
						ref: props.selected.data.content.id
					}
				}">Neu</router-link>
				<!-- <button class="new button" @click="newEvent" @mouseDown.middle="" v-if="user.login">Neu</button> -->
				<button class="close button" @click="close">X</button>
				<router-link class="delete button" v-if="user.level == 2 || isOwnEvent()" :to="{
					name: 'Bearbeiten',
					query: {
						ref: props.selected.data.content.id
					}
				}">Ändern</router-link>
				<!-- <button class="delete button" @click="editEvent" v-if="user.level == 2 || isOwnEvent()">Ändern</button> -->
			</div>
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
				<p class="attrib" v-if="selected.data.content.extra">
					Sonstiges: {{ selected.data.content.extra }}
				</p>
			</div>
			<People :people="selected.data.people" />
			<h3><u>Module:</u></h3>
			<div class="block" v-for="(relation, index) in selected.data.modules" :key="index">
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

<script setup>
import { computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import People from "./Info/People.vue";
import search from "@/services/SearchService.js";
import helper from "@/services/HelperService.js";
import { useStore } from 'vuex';

const props = defineProps({
	selected: Object,
	filterActive: Boolean,
});
const emit = defineEmits(["close", "relation"]);
const router = useRouter();
const store = useStore();

const user = computed(() => store.state.User);

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

function newEvent(newTab = false) {
	router.push({
		name: "Neu",
		query: {
			ref: props.selected.data.content.id
		},
	});
}

function editEvent() {
	router.push({
		name: "Bearbeiten",
		params: {
			id: props.selected.data.content.id,
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

function isOwnEvent() {
	return props.selected.data.content.semester > helper.getCurrentSemester() && props.selected.data.content.own;
}
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