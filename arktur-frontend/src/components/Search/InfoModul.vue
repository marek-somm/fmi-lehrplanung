<template>
	<div class="info--container" :class="{ filter: filterActive }">
		<div class="header" v-if="selected">
			<h3 class="title">{{ selected.data.content.title_de }}</h3>
			<button class="close button" @click="close">X</button>
		</div>
		<div class="info-content" v-if="selected">
			<h3><u>Informationen:</u></h3>
			<div class="block">
				<p>Titel Deutsch: {{ selected.data.content.title_de }}</p>
				<p>Titel Englisch: {{ selected.data.content.title_en }}</p>
				<p>
					Modulcode: <b>{{ selected.data.content.modulecode }}</b>
				</p>
			</div>
			<div class="block">
				<p class="attrib">
					Aktiv:
					<b>{{ selected.data.content.active ? "Ja" : "Nein" }}</b>
				</p>
				<p>Turnus: {{ selected.data.content.rotation }}</p>
				<p v-if="selected.data.content.composition">
					Zusammensetzung: {{ selected.data.content.composition }}
				</p>
			</div>
			<div class="block">
				<p>ECTS: {{ selected.data.content.ects }} LP</p>
				<p>
					Präsenszeit: {{ selected.data.content.presence_time }} Stunden
				</p>
				<p>Workload: {{ selected.data.content.workload }} Stunden</p>
			</div>
			<div class="block">
				<div class="attrib-container" v-if="selected.data.content.type">
					<p class="attrib">Modulart:</p>
					<div class="box" v-html="selected.data.content.type"></div>
				</div>
				<div
					class="attrib-container"
					v-if="selected.data.content.prior_knowledge"
				>
					<p class="attrib">Vorwissen:</p>
					<div
						class="box"
						v-html="selected.data.content.prior_knowledge"
					></div>
				</div>
				<div class="attrib-container" v-if="selected.data.content.content">
					<p class="attrib">Inhalt:</p>
					<div class="box" v-html="selected.data.content.content"></div>
				</div>
				<div
					class="attrib-container"
					v-if="selected.data.content.required_creditpoints"
				>
					<p class="attrib">Voraussetzung Leistungspunkte:</p>
					<div
						class="box"
						v-html="selected.data.content.required_creditpoints"
					></div>
				</div>
				<div
					class="attrib-container"
					v-if="selected.data.content.requirement_exam"
				>
					<p class="attrib">Voraussetzung Modulprüfung:</p>
					<div
						class="box"
						v-html="selected.data.content.requirement_exam"
					></div>
				</div>
				<div
					class="attrib-container"
					v-if="selected.data.content.requirement_admission"
				>
					<p class="attrib">Voraussetzung Modulzulassung:</p>
					<div
						class="box"
						v-html="selected.data.content.requirement_admission"
					></div>
				</div>
				<div
					class="attrib-container"
					v-if="selected.data.content.additional_info"
				>
					<p class="attrib">Zusatzinformationen:</p>
					<div
						class="box"
						v-html="selected.data.content.additional_info"
					></div>
				</div>
				<div
					class="attrib-container"
					v-if="selected.data.content.literature"
				>
					<p class="attrib">Literatur:</p>
					<div class="box" v-html="selected.data.content.literature"></div>
				</div>
			</div>
			<People :people="selected.data.people" />
			<h3><u>Veranstaltungen:</u></h3>
			<div v-for="(semester) in helper.sortObj(selected.data.events)" :key="semester[0]">
				<h4>{{ helper.convertSemester(semester[0]) }}</h4>
				<div
					class="block"
					v-for="(relation, index) in semester[1]"
					:key="index"
				>
					<div class="box hover" :key="index" @click="view(relation)">
						<p>
							<b>{{ relation.pivot.title }}</b>
						</p>
						<br />
						<p>Veranstaltungsnummer: {{ relation.vnr }}</p>
						<p>Prüfungsnummer: {{ relation.pivot.pnr }}</p>
					</div>
				</div>
				<br />
			</div>
		</div>
	</div>
</template>

<script>
import { onMounted } from "vue";
import { useRouter } from "vue-router";
import People from "./Info/People.vue";
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
					id: props.selected.modulcode,
					sem: 0,
				},
			});
		}

		function view(relation) {
			emit("relation", relation);
		}

		return {
			helper,
			convertSemester,
			close,
			newInstance,
			view,
		};
	},
};
</script>

<style lang="scss" scoped>
@import "info.scss";

.button {
	height: 2rem;
	width: 2rem;
	border: none;
	padding: 0.4rem;

	&:hover {
		cursor: pointer;
	}
}

.close {
	background-color: rgb(255, 220, 220);
	grid-column: 2;
	grid-row: 1;

	&:hover {
		background-color: rgb(255, 100, 100);
	}
}
</style>