<template>
	<div class="info--container" :class="{ filter: filterActive }">
		<div class="header" v-if="selected">
			<h3 class="title">{{ selected.data.titel }}</h3>
			<button class="new button" @click="editInstance">Edit</button>
			<button class="new button" @click="newInstance">New</button>
			<button class="close button" @click="close">X</button>
		</div>
		<div class="info-content" v-if="selected">
			<div class="block">
				<p class="attrib">Vnr: {{ selected.data.veranstaltungsnummer }}</p>
				<p class="attrib">Friedolin ID: {{ selected.data.friedolinID }}</p>
				<p class="attrib">
					Semester: {{ convertSemester(selected.data.semester) }}
				</p>
				<p class="attrib">Turnus: {{ selected.data.turnus }}</p>
				<p class="attrib">
					Aktiv: <button class="new button" @click="toggleAktiv">{{ selected.data.aktiv ? "X" : "" }}</button>
				</p>
			</div>
			<div class="block">
				<p class="attrib">Art: {{ selected.data.art }}</p>
				<p class="attrib">SWS: {{ selected.data.sws }}</p>
			</div>
			<div class="block">
				<div
					class="attrib-container"
					v-for="(value, key) in selected.content"
					:key="key"
				>
					<p class="attrib">{{ key }}:</p>
					<div class="box" v-html="value" v-if="value"></div>
				</div>
			</div>
			<br />
			<People :people="selected.people"/>
			<Exams :exams="selected.exams" @exam="view"/>
		</div>
	</div>
</template>

<script>
import { onMounted } from "vue";
import { useRouter } from "vue-router";
import People from "./Info/People.vue";
import Exams from './Info/Exams.vue';
import { request } from "@/scripts/request.js";

export default {
	components: { People, Exams },
	props: {
		selected: Object,
		filterActive: Boolean
	},
	setup(props, { emit }) {
		const router = useRouter();
		const rq = new request();

		function convertSemester(code) {
			if (code % 10 == 0) {
				return "SoSe " + parseInt(code / 10);
			} else {
				return "WiSe " + parseInt(code / 10);
			}
		}

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
			router.push({
				name: "Instanziieren",
				params: {
					id: props.selected.data.veranstaltungsnummer,
					sem: props.selected.data.semester,
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
			rq.toggleAktiv(props.selected.data.veranstaltungsnummer, props.selected.data.semester)
		}

		function view(exam) {
			emit('exam', exam)
		}

		return {
			convertSemester,
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
@import 'info.scss';
.attrib {
	button{
		height:1.4em;
		width:1.8em;
	}
}
</style>