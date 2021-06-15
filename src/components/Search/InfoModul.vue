<template>
	<div class="info--container">
		<div class="header" v-if="selected">
			<h3 class="title">{{ selected.modulcode }}</h3>
			<button class="close button" @click="close">X</button>
		</div>
		<div class="info-content" v-if="selected">
			<div class="block">
				<br />
				<h4 class="title">Titel DE: {{ selected.data.titel_de }}</h4>
				<h4 class="title">Titel EN: {{ selected.data.titel_en }}</h4>
			</div>
			<div class="block">
				<p>ECTS: {{ selected.data.lp }} LP</p>
				<p>Präsenzzeit: {{ selected.data.praesenzzeit }}</p>
				<p>Workload: {{ selected.data.workload }}</p>
			</div>
			<div class="block">
				<p>Turnus: {{ selected.data.turnus }}</p>
				<p>Zusammensetzung: {{ selected.data.zusammensetzung }}</p>
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
			<People :people="selected.people" />
			<Exams :exams="selected.exams" @exam="view"/>
		</div>
	</div>
</template>

<script>
import { onMounted } from "vue";
import People from "./Info/People.vue";
import Exams from "./Info/Exams.vue";

export default {
	components: { People, Exams },
	props: {
		selected: {
			type: Object,
		},
	},
	setup(props, { emit }) {
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

		function view(exam) {
			emit('exam', exam)
		}

		return {
			convertSemester,
			close,
			view,
		};
	},
};
</script>

<style lang="scss" scoped>
@import 'info.scss';
</style>