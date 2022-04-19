<template>
	<div id="dashboard-container" v-if="user.level > 0">
		<InfoVeranstaltung
			:selected="data.selectedVeranstaltung"
			:filterActive="data.filterActive"
			:class="{ show: data.showVeranstaltung }"
			class="info"
			@close="closeVeranstaltung"
			@relation="updateModul"
		/>
		<InfoModul
			:selected="data.selectedModul"
			:filterActive="data.filterActive"
			:class="{ show: data.showModul }"
			class="info"
			@close="closeModul"
			@relation="updateVeranstaltungWithModul"
		/>
		<div class="dashboard-content">
			<div class="last-events">
				<h3>Meine Veranstaltungen</h3>
				<Accordion
					:content="data.events"
					@select="updateVeranstaltung"
				></Accordion>
			</div>
		</div>
	</div>

	<StudentDashboard	 v-if="user.level == 0" />
</template>

<script>
import Accordion from "@/components/Accordion";

import { computed, onMounted, reactive } from "@vue/runtime-core";
import search from "@/services/SearchService.js";
import helper from "@/services/HelperService.js";
import InfoVeranstaltung from "@/components/Search/InfoVeranstaltung.vue";
import InfoModul from "@/components/Search/InfoModul.vue";
import { useStore } from 'vuex';
import StudentDashboard from './StudentDashboard.vue';

export default {
	components: {
		Accordion,
		InfoVeranstaltung,
		InfoModul,
		StudentDashboard,
	},
	setup() {
		const store = useStore();
		const user = computed(() => store.state.User);

		const data = reactive({
			events: {
				data: {
					future: {},
					current: {},
					past: {},
				},
				selected: 0
			},
			showVeranstaltung: false,
			showModul: false,
			selectedVeranstaltung: null,
			selectedModul: null,
			filterActive: false,
		});

		onMounted(() => {
			if(user.value.level > 0) {
				getEvents();
			} else {

			}
		});

		async function getEvents() {
			let events = await search.getUserEvents(helper.getCurrentSemester());
			data.events.data = events.data;
			data.events.selected = Object.keys(data.events.data["future"]).length
		}

		async function updateVeranstaltung(vnr, semester) {
			data.selectedVeranstaltung = await search.getEvent(vnr, semester);
			data.showVeranstaltung = true;
		}

		async function updateVeranstaltungWithModul(modul) {
			updateVeranstaltung(modul.vnr, modul.semester);
		}

		async function updateModul(relation) {
			data.selectedModul = await search.getModule(relation.modulecode);
			data.showModul = true;
		}

		function closeVeranstaltung() {
			data.showVeranstaltung = false;
		}

		function closeModul() {
			data.showModul = false;
		}

		return {
			data,
			user,
			store,
			helper,
			updateVeranstaltung,
			updateVeranstaltungWithModul,
			updateModul,
			closeVeranstaltung,
			closeModul,
		};
	},
};
</script>

<style lang="scss" scoped>
#dashboard-container {
	display: flex;
	flex-direction: row;
	height: calc(100vh - 9.676rem);

	.dashboard-content {
		display: flex;
		flex-direction: column;
		align-items: center;
		overflow-y: auto;
		transition: height 0.2s ease;
		width: 100%;

		.last-events {
			background: #eee;
			padding: 0 5rem;
			width: 60%;
		}
	}

	.info {
		height: inherit;
	}
}
</style>